<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuizSessionsExport;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\QuizSchedule;
use App\Models\QuizSession;
use App\Repositories\QuizRepository;
use App\Settings\LocalizationSettings;
use App\Settings\SiteSettings;
use App\Transformers\Admin\QuizScoreReportTransformer;
use App\Transformers\Admin\QuizSessionExportTransformer;
use App\Transformers\Admin\UserQuizSessionTransformer;
use App\Transformers\Platform\QuizSolutionTransformer;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
class QuizAnalyticsController extends Controller
{
    /**
     * @var QuizRepository
     */
    private QuizRepository $repository;

    public function __construct(QuizRepository $repository)
    {
        $this->middleware(['role:admin|instructor']);
        $this->repository = $repository;
    }

    /**
     * Get Quiz Overall Report with statistics from sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
     */
    public function overallReport(Quiz $quiz)
    {   
        $schedule = null;
        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
            $stats = $this->repository->getQuizSessionStats($quiz->id, request()->get('schedule'));
        } else {
            $stats = $this->repository->getQuizSessionStats($quiz->id, null);
        }

        return Inertia::render('Admin/Quiz/OverallReport', [
            'quiz' => $quiz->only('id', 'code', 'title', 'slug', 'quiz_type_id'),
            'type' => $quiz->quizType,
            'schedule' => $schedule != null ? $schedule->only('id', 'code') : null,
            'stats' => [
                'total_attempts' => $stats[0]->total_attempts,
                'pass_count' => $stats[0]->pass_count,
                'failed_count' => $stats[0]->failed_count,
                'unique_test_takers' => $stats[0]->unique_test_takers,
                'avg_time' => formattedSeconds(round($stats[0]->avg_time, 0)),
                'avg_score' => round($stats[0]->avg_score, 1).'/'.$quiz->total_marks,
                'high_score' => round($stats[0]->high_score, 1),
                'low_score' => round($stats[0]->low_score, 1),
                'avg_percentage' => round($stats[0]->avg_percentage, 0)."%",
                'avg_accuracy' => round($stats[0]->avg_accuracy, 0)."%",
                'avg_speed' => round($stats[0]->avg_speed, 0)." que/hr",
                'avg_questions_answered' => round(calculatePercentage($stats[0]->sum_answered, $stats[0]->sum_questions), 0)."%",
            ],
        ]);
    }


    public function multipart_unique_user($quiz)
    {   

        $part1 = QuizSession::with('user')->where('quiz_id', '=', $quiz->id)->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();

        $part2 = QuizSession::with('user')->where('quiz_id', '=', $quiz->is_multipart_id)->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();

        $part1_user = array();
        $part2_user = array();
        foreach ($part1 as $key => $value) {
            $part1_user[] = $value->user_id;
        }

        foreach ($part2 as $key => $value) {
            $part2_user[] = $value->user_id;
        }

        $result = array_intersect($part1_user, $part2_user);
        return $result;
    }


    /**
     * Get Quiz Overall Report with graph statistics from sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
     */
    public function graphSkillOverallReport(Quiz $quiz, $slug = 'developer', $skill)
    {
        $schedule = null;

        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $both_part_user = $this->multipart_unique_user($quiz);

        $userGroups = UserGroup::where('slug',$slug)->first();

        $group_id = $userGroups->id;

        $users_ids = array();
        $users_ids = user::whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('usergroups')->whereIn('id',$both_part_user)->get()->pluck('id'); 
       
        $quiz_id = array();
        $quiz_ids[0] =$quiz->id;
        $quiz_ids[1] = $quiz->is_multipart_id;

        $quiz_ids = array_values($quiz_ids);

        $sessions_data = QuizSession::with('user', 'usergroups','questions')->whereIn('user_id',$users_ids)->whereIn('quiz_id', $quiz_ids)->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();

        $main_graph = array();
        $main_graph_users = array(); 
        $main_graph_result = array(); 
        
        $skill_data = Skill::where('id',$skill)->first();
            
        foreach ($sessions_data as $key => $session) {

            $skillwisedata = $session->questions;

            $collection = collect();

            $totalmarks = 0;
            $totalques = 0;
            $correctans = 0;
            $marksobtained = 0;

            foreach ($skillwisedata as $key => $value) {

                if ($value->skill_id == $skill) {
                    $totalmarks = $totalmarks + $value->default_marks;
                    $totalques++;
                    $correctans = $correctans + $value->getOriginal('pivot_is_correct');
                    $marksobtained = $marksobtained + $value->getOriginal('pivot_marks_earned');
                    $skillname = getskillname($value->skill_id);
                }
            }

            if($totalmarks != 0){
                $data['totalmarks'] = $totalmarks;
                $data['marksobtained'] = $marksobtained;
                $data['totalques'] = $totalques;
                $data['correctans'] = $correctans;
                $percentage = ($marksobtained / $totalmarks) * 100; // 20
                // $main_graph_result[] = $percentage;
                // $main_graph_users[] = $session->user->first_name.' '.$session->user->last_name;

                $percentage = ($marksobtained / $totalmarks) * 100; // 20
            
                if (!array_key_exists($session->user->id, $main_graph_result)) {
                    $main_graph_result[$session->user->id] = $percentage;
                    $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
                }else{
                    if ($main_graph_result[$session->user->id] < $percentage) {
                        $main_graph_result[$session->user->id] = $percentage;
                        $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
                    }
                }

            }
        }

        $main_graph['data'] = array_values($main_graph_result);
        $main_graph['labels'] = array_values($main_graph_users);


        return response()->json([
            'skill_data' => $skill_data,
            'main_graph' => $main_graph,
        ], 200);
    }


    /**
     * Get Quiz Detaild Report with graph statistics from sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
     */
    public function graphSkillReport(Quiz $quiz, $slug = 'developer', $skill)
    {
        $schedule = null;
        dd($skill);
        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $userGroups = UserGroup::where('slug',$slug)->first();

        $group_id = $userGroups->id;

        $sessions_data = QuizSession::with('user', 'usergroups','questions')->where('quiz_id', '=', $quiz->id)->whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
            })->where('status', '=', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

       

        $main_graph = array();
        $main_graph_users = array(); 
        $main_graph_result = array(); 
        
        $skill_data = Skill::where('id',$skill)->first();
            
        foreach ($sessions_data as $key => $session) {

            $skillwisedata = $session->questions;

            $collection = collect();

            $totalmarks = 0;
            $totalques = 0;
            $correctans = 0;
            $marksobtained = 0;

            foreach ($skillwisedata as $key => $value) {

                if ($value->skill_id == $skill) {
                    $totalmarks = $totalmarks + $value->default_marks;
                    $totalques++;
                    $correctans = $correctans + $value->getOriginal('pivot_is_correct');
                    $marksobtained = $marksobtained + $value->getOriginal('pivot_marks_earned');
                    $skillname = getskillname($value->skill_id);
                }

            }

            $data['skill'] = $skillname;
            $data['totalmarks'] = $totalmarks;
            $data['marksobtained'] = $marksobtained;
            $data['totalques'] = $totalques;
            $data['correctans'] = $correctans;

            $percentage = ($marksobtained / $totalmarks) * 100; // 20

            if (!array_key_exists($session->user->id, $main_graph_result)) {
                $main_graph_result[$session->user->id] = $percentage;
                $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
            }else{
                if ($main_graph_result[$session->user->id] < $percentage) {
                    $main_graph_result[$session->user->id] = $percentage;
                    $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
                }
            }
            
        }

        $main_graph['labels'] = array_values($main_graph_users);  
        $main_graph['data'] = array_values($main_graph_result);

        return response()->json([
            'skill_data' => $skill_data,
            'main_graph' => $main_graph,
        ], 200);
    }

    /**
     * Get Quiz Detailed Report with graph statistics from sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
    */
    public function graphdetailedReport(Quiz $quiz, $slug = 'developer')
    {   


        $schedule = null;
        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $usergroups = UserGroup::orderBy('name')->get();

        $userGroups = UserGroup::where('slug',$slug)->first();
        $group_id = $userGroups->id;
        $sessions_data = QuizSession::where('quiz_id', '=', $quiz->id)->whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('user', 'usergroups')->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();


        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->id);
        $quizs_skills =  $quizs->questions()->select('skill_id')->get();
        $quizs_skills_array = array();

        foreach ($quizs_skills as $key => $value) {
            $quizs_skills_array[] = $value->skill_id; 
        }

        $skills_unique = array_values(array_unique($quizs_skills_array));

        $skills = Skill::with('section:id,name')->whereIn('id',$skills_unique)->orderBy('name')->get()->toArray();

        $user_sessions = $sessions_data->groupBy('user_id');

        $sessions = array();
        $main_graph = array();
        $main_graph_users = array(); 
        $main_graph_result = array(); 
        foreach ($user_sessions as $key => $sessions) {
            foreach ($sessions as $key => $session) {
                $main_graph_result[] = $session['results']['percentage'];
                $main_graph_users[] = $session->user->first_name.' '.$session->user->last_name;
            }
        } 

        $main_graph['labels'] = $main_graph_users;  
        $main_graph['data'] = $main_graph_result;  

        return Inertia::render('Admin/Quiz/DetailedGraphReport', [
            'schedule' => $schedule != null ? $schedule->only('id', 'code') : null,
            'quiz' => $quiz->only('id', 'code', 'title', 'slug','quiz_type_id'),
            'type' => $quiz->quizType,
            'main_graph' => $main_graph,
            'skills' => $skills,
            'slug' => $slug,
            'report_type' => 'detailed',
            'usergroups' => $usergroups,
        ]);

    }



    /**
     * Get Quiz Overall Report with graph statistics from sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
    */
    public function graphoverallReport(Quiz $quiz, $slug = 'developer')
    {   

        $both_part_user = $this->multipart_unique_user($quiz);

        $schedule = null;

        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $usergroups = UserGroup::orderBy('name')->get();

        $userGroups = UserGroup::where('slug',$slug)->first();

        $group_id = $userGroups->id;

        $users_ids = array();
        $users_ids = user::whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('usergroups')->whereIn('id',$both_part_user)->get()->pluck('id'); 

        $quiz_id = array();
        $quiz_ids[0] =$quiz->id;
        $quiz_ids[1] = $quiz->is_multipart_id;

        $quiz_ids = array_values($quiz_ids);

        $sessions_data1 = QuizSession::whereIn('user_id',$users_ids)->whereIn('quiz_id',$quiz_ids)->with('user', 'usergroups','questions')->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();

        // Both part skill start

        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->id);
        $quizs_skills =  $quizs->questions()->select('skill_id')->get();
        $quizs_skills_array = array();

        foreach ($quizs_skills as $key => $value) {
            $quizs_skills_array[] = $value->skill_id; 
        }

        $skills_unique1 = array_values(array_unique($quizs_skills_array));


        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->is_multipart_id);
        $quizs_skills =  $quizs->questions()->select('skill_id')->get();
        $quizs_skills_array = array();

        foreach ($quizs_skills as $key => $value) {
            $quizs_skills_array[] = $value->skill_id; 
        }

        $skills_unique2 = array_values(array_unique($quizs_skills_array));
        $skills_unique = array_merge($skills_unique1,$skills_unique2);

        $skills = Skill::with('section:id,name')->whereIn('id',$skills_unique)->orderBy('name')->get()->toArray();

        // Both part skill end

        $user_sessions = $sessions_data1->groupBy('user_id');

        $sessions = array();
        $main_graph = array();
        $main_graph_users = array(); 
        $main_graph_result = array(); 
        foreach ($user_sessions as $key => $sessions) {
            foreach ($sessions as $key => $session) {
                if(array_key_exists($session->user->id,$main_graph_result)){
                    $value1 = $main_graph_result[$session->user->id];
                    $value2 = $session['results']['percentage'];
                    $avg_percentage = ($value1 + $value2)/2;
                    $main_graph_result[$session->user->id] = $avg_percentage;
                }else{
                    $main_graph_result[$session->user->id] = $session['results']['percentage'];
                }

                $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
            }
        } 

        $main_graph['labels'] = array_values($main_graph_users);  
        $main_graph['data'] = array_values($main_graph_result);  


        $quiz->title = str_replace('Part 2', '', $quiz->title);  
        $quiz->title = str_replace('Part 1', '', $quiz->title);


        $quiz->title = $quiz->title.' Part 1 & Part 2';  
        return Inertia::render('Admin/Quiz/DetailedGraphReport', [
            'schedule' => $schedule != null ? $schedule->only('id', 'code') : null,
            'quiz' => $quiz->only('id', 'code', 'title', 'slug','quiz_type_id'),
            'type' => $quiz->quizType,
            'main_graph' => $main_graph,
            'skills' => $skills,
            'slug' => $slug,
            'report_type' => 'overall',
            'usergroups' => $usergroups,
        ]);
        
    }

    public function graphoverallReport2(Quiz $quiz, $slug = 'developer')
    {   

        $both_part_user = $this->multipart_unique_user($quiz);

        $schedule = null;

        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $usergroups = UserGroup::orderBy('name')->get();

        $userGroups = UserGroup::where('slug',$slug)->first();

        $group_id = $userGroups->id;

        $users_ids = array();
        $users_ids = user::whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('usergroups')->whereIn('id',$both_part_user)->get()->pluck('id'); 

        $quiz_id = array();
        $quiz_ids[0] =$quiz->id;
        $quiz_ids[1] = $quiz->is_multipart_id;

        $quiz_ids = array_values($quiz_ids);

        $sessions_data1 = QuizSession::whereIn('user_id',$users_ids)->whereIn('quiz_id',$quiz_ids)->with('user', 'usergroups','questions')->where('status', '=', 'completed')->orderBy('completed_at', 'desc')->get();

        // Both part skill start

        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->id);
        $quizs_skills =  $quizs->questions()->select('skill_id')->get();
        $quizs_skills_array = array();

        foreach ($quizs_skills as $key => $value) {
            $quizs_skills_array[] = $value->skill_id; 
        }

        $skills_unique1 = array_values(array_unique($quizs_skills_array));


        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->is_multipart_id);
        $quizs_skills =  $quizs->questions()->select('skill_id')->get();
        $quizs_skills_array = array();

        foreach ($quizs_skills as $key => $value) {
            $quizs_skills_array[] = $value->skill_id; 
        }

        $skills_unique2 = array_values(array_unique($quizs_skills_array));
        $skills_unique = array_merge($skills_unique1,$skills_unique2);

        $skills = Skill::with('section:id,name')->whereIn('id',$skills_unique)->orderBy('name')->get()->toArray();

        // Both part skill end

        $user_sessions = $sessions_data1->groupBy('user_id');

        $sessions = array();
        $main_graph = array();
        $main_graph_users = array(); 
        $main_graph_result = array(); 
        foreach ($user_sessions as $key => $sessions) {
            foreach ($sessions as $key => $session) {
                if(array_key_exists($session->user->id,$main_graph_result)){
                    $value1 = $main_graph_result[$session->user->id];
                    $value2 = $session['results']['percentage'];
                    $avg_percentage = ($value1 + $value2)/2;
                    $main_graph_result[$session->user->id] = $avg_percentage;
                }else{
                    $main_graph_result[$session->user->id] = $session['results']['percentage'];
                }

                $main_graph_users[$session->user->id] = $session->user->first_name.' '.$session->user->last_name;
            }
        } 

        $main_graph['labels'] = $main_graph_users;  
            $main_graph['data'] = $main_graph_result;  
       


        $quiz->title = str_replace('Part 2', '', $quiz->title);  
        $quiz->title = str_replace('Part 1', '', $quiz->title);


        $quiz->title = $quiz->title.' Part 1 & Part 2';  
        
        return [
            'main_graph' => $main_graph,
            'skills' => $skills,
        ];

    }


    public function graphReport($slug = 'director')
    {   
        $personality_assessment_graph = array(); 
        $results_360_graph = array();
        $results_gpi_graph = array();
        $results_assessment_center_graph = array();
        $competencies_assessment_graph = array();
        $overall_graph = array();

        $usergroups = UserGroup::whereIn('id',[2,3,4])->orderBy('name')->get();
        $userGroups = UserGroup::where('slug',$slug)->first();

        $group_id = $userGroups->id;

        $users_ids = array();

        $group_users_d = user::whereNotIn('id',[1,2,3,4])->whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('usergroups');

        $users_ids = $group_users_d->get()->pluck('id');
        $group_users = $group_users_d->get();

        if ($slug == 'director') {
            $results_360 = user::select('id','first_name','last_name','result_360')->whereIn('id',$users_ids)->get();
            foreach ($results_360 as $key => $value) {
                $results_360_users[$value->id] = $value->first_name.' '.$value->last_name;  
                $results_360_data[$value->id] = $value->result_360;
            }
            $results_360_graph['labels'] = $results_360_users;  
            $results_360_graph['data'] = $results_360_data;
        }

        if ($slug == 'manager') {
           
            $results_gpi = user::select('id','first_name','last_name','gpi')->whereIn('id',$users_ids)->get();
            foreach ($results_gpi as $key => $value) {
                $results_gpi_users[$value->id] = $value->first_name.' '.$value->last_name;  
                $results_gpi_data[$value->id] = $value->gpi;
            }
            $results_gpi_graph['labels'] = $results_gpi_users;  
            $results_gpi_graph['data'] = $results_gpi_data;
        }


        $results_assessment_center = user::select('id','first_name','last_name','assessment_center')->whereIn('id',$users_ids)->get();

        foreach ($results_assessment_center as $key => $value) {
            $results_assessment_center_users[$value->id] = $value->first_name.' '.$value->last_name;  
            $results_assessment_center_data[$value->id] = $value->assessment_center;
        }

        $results_assessment_center_graph['labels'] = $results_assessment_center_users;  
        $results_assessment_center_graph['data'] =  $results_assessment_center_data;

        $personality_assessment_collect = collect(QuizSession::with('user')->where('quiz_id',9)->whereIn('user_id',$users_ids)->where('status', '=','completed')
            ->orderBy('completed_at', 'desc')->distinct('id')->get());

        $personality_assessment_unique = $personality_assessment_collect->unique('user_id');

        $personality_assessment = $personality_assessment_unique->values()->all();

        $quiz = Quiz::find(9);

        $personality_assessment_results = $personality_assessment;

        $disc_d = array(); 
        $disc_i = array();  
        $disc_s = array();   
        $disc_c = array();

        foreach ($personality_assessment_results as $key => $par) {

            $session = QuizSession::with('user')->where('code', $par->code)->firstOrFail();
            $questions = fractal($session->questions()->with(['questionType:id,name,code', 'skill:id,name'])
            ->get(['id','code', 'question', 'question_type_id', 'skill_id', 'solution']), new QuizSolutionTransformer())->toArray();
            
            if ($quiz->quiz_type_id == 7) {
                
                $maxScoreDisc = ["D","I","S","C"];
                $scores = array();
                
                foreach ($maxScoreDisc as $key => $value) {
                    $scores[$value] = getQuestionScoringDISC($questions['data'],$value);
                }

                $max_disc = array_keys($scores, max($scores));

                if (in_array('D', $max_disc)) {
                    $disc_d[$par->user_id]['names'] = $session->user->first_name.' '.$session->user->last_name;
                    $disc_d[$par->user_id]['score'] = $scores['D'];
                }
                if (in_array('I', $max_disc)) {
                    $disc_i[$par->user_id]['names'] = $session->user->first_name.' '.$session->user->last_name;
                    $disc_i[$par->user_id]['score'] = $scores['I'];
                }
                if (in_array('S', $max_disc)) {
                    $disc_s[$par->user_id]['names'] = $session->user->first_name.' '.$session->user->last_name;
                    $disc_s[$par->user_id]['score'] = $scores['S'];
                }
                if (in_array('C', $max_disc)) {
                    $disc_c[$par->user_id]['names'] = $session->user->first_name.' '.$session->user->last_name;
                    $disc_c[$par->user_id]['score'] = $scores['C'];
                }
                
            }

            $personality_assessment_graph['results']['labels'][$par->user_id] = $session->user->first_name.' '.$session->user->last_name;
            $personality_assessment_graph['results']['data'][$par->user_id] = $session['results']['percentage']; 

            $personality_assessment_graph['total_questions'] = $session['results']['total_questions'];
            
        }

        $graphSkillsAverageReport = array();

        if ($slug == 'director' || $slug == 'manager') {
            $quiz = Quiz::find(15);
            $competencies_assessment_graph = $this->graphoverallReport2($quiz,$slug);
            $skills = $competencies_assessment_graph['skills'];

            $graphSkillsAverageReport = $this->graphSkillsAverageReport($quiz,$slug,$skills);


        }

        if ($slug == 'supervisor') {
            $quiz = Quiz::find(13);
            $competencies_assessment_graph = $this->graphoverallReport2($quiz,$slug);
            $skills = $competencies_assessment_graph['skills'];

            $graphSkillsAverageReport = $this->graphSkillsAverageReport($quiz,$slug,$skills);
        }

        if ($slug == 'director' || $slug == 'manager' || $slug == 'supervisor') {

            $competencies_assessment_d = $competencies_assessment_graph['main_graph']['data'];


            foreach ($group_users as $key => $group_user) {

                if ($slug == 'director') {
                    $results_360_d = $results_360_graph['data'][$group_user->id];
                    $results_assessment_center_d = $results_assessment_center_graph['data'][$group_user->id];
                    $results_competencies_assessment_d = (key_exists($group_user->id, $competencies_assessment_d)) ? $competencies_assessment_d[$group_user->id] : 0.00;

                    $overall_graph['data'][$group_user->id] = $this->value_calualtion($results_360_d,0.4) + $this->value_calualtion($results_assessment_center_d,0.35) + $this->value_calualtion($results_competencies_assessment_d,0.25);
                    $overall_graph['labels'][$group_user->id] = $group_user->first_name.' '.$group_user->last_name;
                }

                if ($slug == 'manager') {
                    $results_gpi_d = $results_gpi_graph['data'][$group_user->id];
                    $results_assessment_center_d = $results_assessment_center_graph['data'][$group_user->id];
                    $results_competencies_assessment_d = (key_exists($group_user->id, $competencies_assessment_d)) ? $competencies_assessment_d[$group_user->id] : 0.00;

                    $overall_graph['data'][$group_user->id] = $this->value_calualtion($results_gpi_d,0.25) + $this->value_calualtion($results_assessment_center_d,0.4) + $this->value_calualtion($results_competencies_assessment_d,0.35);
                    $overall_graph['labels'][$group_user->id] = $group_user->first_name.' '.$group_user->last_name;
                }

                if ($slug == 'supervisor') {
                    $results_assessment_center_d = $results_assessment_center_graph['data'][$group_user->id];
                    $results_competencies_assessment_d = (key_exists($group_user->id, $competencies_assessment_d)) ? $competencies_assessment_d[$group_user->id] : 0.00;
                    $overall_graph['data'][$group_user->id] = $this->value_calualtion($results_assessment_center_d,0.6) + $this->value_calualtion($results_competencies_assessment_d,0.4);
                    $overall_graph['labels'][$group_user->id] = $group_user->first_name.' '.$group_user->last_name;
                }
            }
        }

        if ($slug == 'director') {
            $results_360_graph['labels'] = array_values($results_360_graph['labels']);  
            $results_360_graph['data'] = array_values($results_360_graph['data']);
        }

        if ($slug == 'manager') {
            $results_gpi_graph['labels'] = array_values($results_gpi_graph['labels']);  
            $results_gpi_graph['data'] = array_values($results_gpi_graph['data']);
        }

        $results_assessment_center_graph['labels'] = array_values($results_assessment_center_graph['labels']);  
        $results_assessment_center_graph['data'] = array_values($results_assessment_center_graph['data']);

        $personality_assessment_graph['labels'] = array_values($personality_assessment_graph['results']['labels']);  
        $personality_assessment_graph['data'] = array_values($personality_assessment_graph['results']['data']); 

        if ($slug == 'director' || $slug == 'supervisor' || $slug == 'manager') {
            $competencies_assessment_graph['labels'] = array_values($competencies_assessment_graph['main_graph']['labels']);  
            $competencies_assessment_graph['data'] = array_values($competencies_assessment_graph['main_graph']['data']);
        }
        
        $overall_graph['labels'] = array_values($overall_graph['labels']);  
        $overall_graph['data'] = array_values($overall_graph['data']);

        return Inertia::render('Admin/Reports', [
            'slug' => $slug,
            'results_360_graph'=>$results_360_graph,
            'results_gpi_graph'=>$results_gpi_graph,
            'disc_result'=> [
                'disc_d'=>$disc_d,
                'disc_i'=>$disc_i,
                'disc_s'=>$disc_s,
                'disc_c'=>$disc_c,
            ], 
            'quiz' => $quiz->only('id', 'code', 'title', 'slug','quiz_type_id'),
            'results_assessment_center_graph' => $results_assessment_center_graph,
            'personality_assessment_graph' => $personality_assessment_graph,
            'competencies_assessment_graph' => $competencies_assessment_graph,
            'overall_graph' => $overall_graph,
            'usergroups' => $usergroups,
            'skills' => $skills,
            'skills_average_report' => $graphSkillsAverageReport,
        ]);

    }

    public function graphSkillsAverageReport($quiz, $slug = 'director', $skills)
    {   

        $quizs = Quiz::with(['questions:skill_id'])->findOrFail($quiz->id);

        $both_part_user = $this->multipart_unique_user($quiz);
        $userGroups = UserGroup::where('slug',$slug)->first();
        $group_id = $userGroups->id;
        $users_ids = array();
        $users_ids = user::whereHas('usergroups', function ($query) use ($group_id)  {
                $query->where('user_group_id', $group_id);
        })->with('usergroups')->whereIn('id',$both_part_user)->get()->pluck('id'); 
       
        $quiz_id = array();
        $quiz_ids[0] =$quiz->id;
        $quiz_ids[1] = $quiz->is_multipart_id;

        $quiz_ids = array_values($quiz_ids);

        $sessions_data = QuizSession::with('user', 'usergroups','questions')->whereIn('user_id',$users_ids)->whereIn('quiz_id', $quiz_ids)->where('status', '=', 'completed')->distinct('user_id')->orderBy('completed_at', 'desc')->get();

        foreach ($skills as $key => $skill_data) {

            $skill = $skill_data['id'];

            $main_graph_result = array();

            foreach ($sessions_data as $key => $session) {

                $skillwisedata = $session->questions;

                $totalmarks = 0;
                $marksobtained = 0;

                foreach ($skillwisedata as $key => $value) {
                    if ($value->skill_id == $skill) {
                        $totalmarks = $totalmarks + $value->default_marks;
                        $marksobtained = $marksobtained + $value->getOriginal('pivot_marks_earned');
                    }
                }

                if($totalmarks != 0){
                    $data['totalmarks'] = $totalmarks;
                    $data['marksobtained'] = $marksobtained;
                    $percentage = ($marksobtained / $totalmarks) * 100; // 20
                    $main_graph_result[] = $percentage;
                }

            }
            
            $main_graph['data'][] = round(array_sum($main_graph_result)/count($users_ids),2);
            $main_graph['labels'][] = $skill_data['name'];
        }
       
        return $main_graph;
    }

    
    private function value_calualtion($value,$p){
        return number_format(($value*$p), 2, '.', '');
    }

    /**
     * Get Quiz Detailed Report with user sessions
     *
     * @param Quiz $quiz
     * @return \Inertia\Response
     */
    public function detailedReport(Quiz $quiz)
    {
        $schedule = null;
        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
        }

        $key = request()->has('schedule') ? 'quiz_schedule_id' : 'quiz_id';
        $value = request()->has('schedule') ? request()->get('schedule') : $quiz->id;

        $sessions = QuizSession::with('user:id,first_name,last_name,email')
            ->where($key, '=', $value)
            ->where('status', '=', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(request()->perPage != null ? request()->perPage : 20);
            //dd(fractal($sessions, new UserQuizSessionTransformer($quiz))->toArray());
        return Inertia::render('Admin/Quiz/DetailedReport', [
            'schedule' => $schedule != null ? $schedule->only('id', 'code') : null,
            'quiz' => $quiz->only('id', 'code', 'title', 'slug','quiz_type_id'),
            'type' => $quiz->quizType,
            'quizSessions' => fractal($sessions, new UserQuizSessionTransformer($quiz))->toArray(),
        ]);
    }

    /**
     * Export Quiz Report in Excel
     *
     * @param Quiz $quiz
     * @param LocalizationSettings $localization
     * @param SiteSettings $settings
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReport(Quiz $quiz, LocalizationSettings $localization, SiteSettings $settings)
    {
        $now = Carbon::now()->timezone($localization->default_timezone);
        $user = auth()->user();
        $footer = "Generated by {$user->first_name} {$user->last_name} on {$now->toDayDateTimeString()}";

        if(request()->has('schedule')) {
            $schedule = QuizSchedule::where('id', '=', request()->get('schedule'))->first();
            $stats = $this->repository->getQuizSessionStats($quiz->id, request()->get('schedule'));
            $title = "{$quiz->title} - Schedule ({$schedule->code}) Detailed Report, {$settings->app_name}";
        } else {
            $stats = $this->repository->getQuizSessionStats($quiz->id, null);
            $title = "{$quiz->title} - Detailed Report, {$settings->app_name}";
        }

        $key = request()->has('schedule') ? 'quiz_schedule_id' : 'quiz_id';
        $value = request()->has('schedule') ? request()->get('schedule') : $quiz->id;

        $sessions = fractal(QuizSession::with('user:id,first_name,last_name,email')
            ->where($key, '=', $value)
            ->where('status', '=', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get(), new QuizSessionExportTransformer())->toArray()['data'];

        return Excel::download(new QuizSessionsExport($stats[0], $sessions, $title, $footer), "quiz-detailed-report-{$now->timestamp}.xlsx");
    }

    /**
     * User Quiz Session Export PDF
     *
     * @param Quiz $quiz
     * @param $session
     * @param LocalizationSettings $localization
     * @param SiteSettings $settings
     * @return \Illuminate\Http\Response
     */

   
    public function secondHighest(array $arr) {
        $arr = array_unique($arr);
        rsort($arr); 
        return $arr[1];
    }

    public function exportPDF(Quiz $quiz, $session, LocalizationSettings $localization, SiteSettings $settings)
    {   

        $session = QuizSession::with('user')->where('code', $session)->firstOrFail();

        $now = Carbon::now()->timezone($localization->default_timezone);
        $user = auth()->user()->first_name.' '.auth()->user()->last_name;

        $questions = fractal($session->questions()->with(['questionType:id,name,code', 'skill:id,name'])
            ->get(['id','code', 'question', 'question_type_id', 'skill_id', 'solution']),
            new QuizSolutionTransformer())->toArray();

        if ($quiz->quiz_type_id == 7) {
            $maxScoreDisc = ["D","I","S","C"];
            $scores = array();
            foreach ($maxScoreDisc as $key => $value) {
                $scores[$value] = getQuestionScoringDISC($questions['data'],$value);
            }
            $max_disc = array_keys($scores, max($scores));
            $max_disc2 = array_keys($scores, $this->secondHighest($scores));
            $final_disc_array[] = $max_disc[0];
            
            if ($this->secondHighest($scores) > 0) {
                $final_disc_array[] = $max_disc2[0];
            }

        }else{
            $final_disc_array = array();
        }

        $collection = collect();
        if ($quiz->quiz_type_id == 1) {
            $skillwisedata = $session->questions->groupBy('skill_id');
            $collection = collect();
            foreach ($skillwisedata as $key => $value) {
                $totalmarks = 0;
                $totalques = 0;
                $correctans = 0;
                $marksobtained = 0;
                foreach ($value as $valuekey => $resultdata) {
                    // dump($resultdata);
                    $totalmarks = $totalmarks + $resultdata->default_marks;
                    $totalques++;
                    $correctans = $correctans + $resultdata->getOriginal('pivot_is_correct');
                    $marksobtained = $marksobtained + $resultdata->getOriginal('pivot_marks_earned');
                    $skillname = getskillname($resultdata->skill_id);
                }
                $data['skill'] = $skillname;
                $data['totalmarks'] = $totalmarks;
                $data['marksobtained'] = $marksobtained;
                $data['totalques'] = $totalques;
                $data['correctans'] = $correctans;
                $collection->put($key, $data);
            }
        }

        $pdf = PDF::loadView('pdf.quiz-session-report', [
            'quiz' => $quiz->only('code', 'title', 'quiz_type_id'),
            'disc' => $final_disc_array,
            'questions' => $questions['data'],
            'session' => fractal($session, new QuizScoreReportTransformer())->toArray()['data'],
            'footer' => "* Report Generated from {$settings->app_name} by {$user} on {$now->toDayDateTimeString()}",
            'logo' => url('storage/'.$settings->logo_path),
            'skillwiseresult' => $collection,
        ]);

        return $pdf->download("report-{$session->code}.pdf");
    }

    /**
     * Quiz Session Analysis and Progress Status
     *
     * @param Quiz $quiz
     * @param $session
     * @return \Inertia\Response
     */
    public function results(Quiz $quiz, $session)
    {
        $session = QuizSession::with('user')->where('code', $session)->firstOrFail();
        $skillwisedata = $session->questions->groupBy('skill_id');
        $collection = collect();
        $collection_skill = [];
        $collectionskill_data = [];
        $skillwisegraph_result = [];
        foreach ($skillwisedata as $key => $value) {
            $totalmarks = 0;
            $totalques = 0;
            $correctans = 0;
            $marksobtained = 0;
            foreach ($value as $valuekey => $resultdata) {
                // dump($resultdata);
                $totalmarks = $totalmarks + $resultdata->default_marks;
                $totalques++;
                $correctans = $correctans + $resultdata->getOriginal('pivot_is_correct');
                $marksobtained = $marksobtained + $resultdata->getOriginal('pivot_marks_earned');
                $skillname = getskillname($resultdata->skill_id);
            }
            $data['skill'] = $skillname;
            $data['totalmarks'] = $totalmarks;
            $data['marksobtained'] = $marksobtained;
            $data['totalques'] = $totalques;
            $data['correctans'] = $correctans;
            $collection->put($key, $data);

            $percentage = ($marksobtained / $totalmarks) * 100; // 20

            $collection_skill[$key] = $skillname;
            $collectionskill_data[$key] = $percentage;

        }

        $skillwisegraph_result['labels'] = array_values($collection_skill);
        $skillwisegraph_result['data'] = array_values($collectionskill_data);

        return Inertia::render('Admin/Quiz/SessionResults', [
            'quiz' => $quiz->only('code', 'title', 'slug', 'total_questions', 'total_marks', 'settings','quiz_type_id'),
            'type' => $quiz->quizType,
            'session' => fractal($session, new UserQuizSessionTransformer($quiz))->toArray()['data'],
            'skillwiseresult' => $collection,
            'skillwisegraph_result' => $skillwisegraph_result,
        ]);
    }

    /**
     * Get quiz solutions api endpoint
     *
     * @param Quiz $quiz
     * @param $session
     * @return \Illuminate\Http\JsonResponse
     */
    public function solutions(Quiz $quiz, $session)
    {
        $session = QuizSession::where('code', $session)->firstOrFail();

        $questions = fractal($session->questions()->with(['questionType:id,name,code', 'skill:id,name'])
            ->get(['id','code', 'question', 'question_type_id', 'skill_id', 'solution']),
            new QuizSolutionTransformer())->toArray();

        return response()->json([
            'questions' => $questions['data'],
        ], 200);
    }
}
