<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{{ $quiz['title'] }} Quiz Score Report</title>
        <style>
            body { width: 100%; color: #444; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif}
            table {width: 100%;border: 1px solid transparent}
            table {border-collapse: collapse}
            table, th {border: 1px solid transparent;vertical-align: middle;padding: 5px}
            td {border: 1px solid transparent;padding: 5px}
            footer {position: fixed;bottom: 0;left: 0;right: 0;height: 20px}
            header {position: fixed;top: 0;left: 0;right: 0;height: 20px}

            .footer-note {padding-top: 10px; font-size: 12px}
            .logo-block {text-align: center; width: 30%}
            .heading-block {text-align: right; width: 70%}
            .logo {height: 35px}
            .heading {text-align: center; background: #dadada;font-weight: bold}
            .list-item {display: block;padding: 4px}
            .heading-item {display: block;padding: 4px;margin-top: 4px}
            .text-center {text-align: center}
            .text-left {text-align: left}
            .uppercase {text-transform: uppercase}
            .signature-block {text-align: center; vertical-align: bottom}
            .green {color: green}
            .red {color: red}
            .w-25 {width: 25%}
            .w-50 {width: 50%}
            p{font-size: 14px;}
            ul li{ font-size: 14px; }
            header{ display: none; }  
            .personality-content-wrap h4{
                padding-left: 20px;
                color: #032348;
            }
            .personality-content-wrap ul {
                margin-left: 0px;
            }

            .personality-content-wrap p {
                padding-left: 20px;
            }
            @page :first header{ display: block; }  
            .page-break {page-break-after: always;}
        </style>
    </head>
    <body>
    <?php if ($quiz['quiz_type_id'] != 7): ?>
        <div class="report">
            <table>
                <tbody>
                <tr>
                    <th colspan="1" class="logo-block">
                        <img class="logo" src="{{ $logo }}" alt="Logo" />
                    </th>
                    <th colspan="3" class="heading-block">
                        <h2>Assessment Score Report</h2>
                        <h3>{{ $quiz['title'] }}</h3>
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="heading w-50">
                        Test Details
                    </td>
                    <td colspan="2" class="heading w-50">
                        User Details
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="w-50">
                        <span class="heading-item"><strong>Assessment Name:</strong></span>
                        <span class="list-item">{{ $quiz['title'] }}</span>
                        <span class="heading-item"><strong>Completed on:</strong></span>
                        <span class="list-item">{{ $session['completed_at'] }}</span>
                        <span class="heading-item"><strong>Session ID:</strong></span>
                        <span class="list-item">{{ $session['id'] }}</span>
                       
                    </td>
                    <td colspan="2" class="w-50">
                        <span class="heading-item"><strong>Test Taker:</strong></span>
                        <span class="list-item">{{ $session['user'] }}</span>
                        <span class="heading-item"><strong>E-Mail:</strong></span>
                        <span class="list-item">{{ $session['email'] }}</span>
                        <span class="heading-item"><strong>IP & Device:</strong></span>
                        <span class="list-item">{{ $session['ip'] }}, {{ $session['device'] }}, {{ $session['os'] }}, {{ $session['browser'] }}</span>
                       
                    </td>
                </tr>


                    <tr>
                        <td colspan="4" class="heading">
                            Test Results
                        </td>
                    </tr>
                    @foreach($session['properties'] as $properties)
                        <tr>
                            @foreach($properties as $property)
                                <td class="w-25">
                                    <span class="list-item"><strong>{{ $property['key'] }}</strong></span>
                                </td>
                                <td class="w-25">
                                    <span class="list-item">{{ $property['value'] }}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <?php if ($quiz['quiz_type_id'] == 1): ?>
                        <!-- <tr>
                            <th colspan="4" class="heading">
                                Skill Results
                            </th>
                        </tr>
                        <tr>
                            <td colspan="2"><span class="heading-item"><strong>Skill</strong></span></td>
                            <td colspan="1" class="text-center"><span class="heading-item"><strong>Score</strong></span></td>
                            <td colspan="1" class="text-center"><span class="heading-item"><strong>Total marks</strong></span></td>
                        </tr> -->
                        @foreach($skillwiseresult as $skill_data)
                           <!--  <tr>
                                <td colspan="2">{{$skill_data['skill']}}</td>
                                <td colspan="1" class="text-center">{{$skill_data['correctans']}}/{{$skill_data['totalques']}}</td>
                                <td colspan="1" class="text-center">{{$skill_data['marksobtained']}}/{{$skill_data['totalmarks']}}</td>
                            </tr> -->
                        @endforeach

                    <?php endif ?>
                    
                    <tr>
                        <td colspan="2" class="w-50">
                            <br>
                            <h2 class="uppercase {{ $session['status'] == 'Passed' ? 'green' : 'red' }}">{{ $session['status'] }}</h2>
                            <br>
                            Final Result
                        </td>
                        <td colspan="2" class="signature-block w-50">Authorized by</td>
                    </tr>
                
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="report">
            <table>
                <tbody>
                <tr>
                    <th colspan="1" class="logo-block">
                        <img class="logo" src="{{ $logo }}" alt="Logo" />
                    </th>
                    <th colspan="3" class="heading-block">
                        <h2>Assessment Score Report</h2>
                        <h3>{{ $quiz['title'] }}</h3>
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="heading w-50">
                        Test Details
                    </td>
                    <td colspan="2" class="heading w-50">
                        User Details
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="w-50">
                        <span class="heading-item"><strong>Assessment Name:</strong></span>
                        <span class="list-item">{{ $quiz['title'] }}</span>
                        <span class="heading-item"><strong>Completed on:</strong></span>
                        <span class="list-item">{{ $session['completed_at'] }}</span>
                        <span class="heading-item"><strong>Session ID:</strong></span>
                        <span class="list-item">{{ $session['id'] }}</span>
                       
                    </td>
                    <td colspan="2" class="w-50">
                        <span class="heading-item"><strong>Test Taker:</strong></span>
                        <span class="list-item">{{ $session['user'] }}</span>
                        <span class="heading-item"><strong>E-Mail:</strong></span>
                        <span class="list-item">{{ $session['email'] }}</span>
                        <span class="heading-item"><strong>IP & Device:</strong></span>
                        <span class="list-item">{{ $session['ip'] }}, {{ $session['device'] }}, {{ $session['os'] }}, {{ $session['browser'] }}</span>
                       
                    </td>
                </tr>

                    <tr>
                        <td colspan="4" class="heading text-left w-100">Scores</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="w-50">
                            <span class="heading-item"><strong>D  - {{ getQuestionScoringDISC($questions,'D') }}</strong></span>
                            <span class="heading-item"><strong>I  - {{ getQuestionScoringDISC($questions,'I') }}</strong> </span>
                           
                        </td>
                        <td colspan="2" class="w-50">
                            <span class="heading-item"><strong>S - {{ getQuestionScoringDISC($questions,'S') }}</strong></span>
                            <span class="heading-item"><strong>C - {{ getQuestionScoringDISC($questions,'C') }}</strong></span>
                            <br>
                           
                        </td>
                    </tr>            
                </tbody>
            </table>
            <div>
                <div class="w-100 personality-content-wrap">
                    <?php if (in_array('D', $disc)) : ?>
                        <div class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-0" style="background: #14967c; padding: 15px 20px; color: #fff;">D – Dominant/Direct</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with A Style is well-respected by the organization as a go-getter who delivers on promises, yet direct with businesslike approach & could be too forceful. They prefer an exciting, action-oriented work environment; however, he/she seems intense and demanding, and can push their ideas through without reaching out to others. They usually don’t seem interested in collaboration and engaging people through teamwork</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside page-break">
                                <li>Getting immediate & quick results</li>
                                <li>Causing action & moving people</li>
                                <li>Accepting challenges</li>
                                <li>Making quick decisions</li>
                                <li>Questioning the status quo</li>
                                <li>Taking charge</li>
                                <li>Solving problems Produces quick results</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3" >Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Lack of concern for others (insensitive)</li>
                                <li>Impatience that can intimidate others</li>
                                <li>Overlook details</li>
                                <li>They could be overly domineering</li>
                                <li>Can make too risky & hasty decisions</li>
                                <li>Reluctance to delegate</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Power and authority</li>
                                <li>Prestige and challenge</li>
                                <li>Opportunities for individual accomplishments</li>
                                <li>Direct answers</li>
                                <li>Opportunities for advancement</li>
                                <li>Freedom from controls and supervision</li>
                                <li>Many new and varied activities</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Management</li>
                                <li>Team leading</li>
                                <li>Sales</li>
                                <li>Strategy</li>
                            </ul>
                        </div>

                    <?php endif ?>

                    <?php if (in_array('I', $disc)) : ?>
                        <div class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #d0413e; padding: 15px 20px; color: #fff;">I – Influential/Spirited</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a B style has priority of enthusiasm with an outgoing nature. He/she seems to know everyone on a first-name basis. They show positive outlook and lively approach with excitement for new ideas. They tend to prioritize action and a fast pace along with spontaneity. Their energetic approach and inclination toward change are very useful, yet they might get so caught up in new ideas that they fail to stick to more routine tasks. They value collaboration and teamwork, enjoying the social aspects of work, but they also crave for attention at times.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside page-break">
                                <li>Building relationships & working in teams</li>
                                <li>Making a favorable impression</li> 
                                <li>Being articulate & promoter</li>
                                <li>Creating a motivational environment</li>
                                <li>Generating enthusiasm</li>
                                <li>Viewing people and situations with optimism</li>
                                <li>Idea generator</li>
                                <li>Vision-oriented thinker</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Impulsiveness & over commitment</li>
                                <li>Disorganization (Time management issues)</li>
                                <li>Lack of follow through</li>
                                <li>Over eagerness leads to missing on important details</li>
                                <li>Depend too much on intuition & spontaneity</li> 
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Popularity & social recognition</li>
                                <li>Freedom of expression</li>
                                <li>Group activities outside of the job</li>
                                <li>Democratic relationships</li>
                                <li>Freedom from control and detail</li> 
                                <li>Opportunities to verbalize proposals</li>
                                <li>Coaching and counseling</li>
                                <li>Favorable working conditions</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Sales</li>
                                <li>Customer Service</li>
                                <li>Public Relations</li>
                                <li>HR: Training & Development</li>
                                <li>Office Manager</li>
                            </ul>
                        </div>
                    <?php endif ?>

                    <?php if (in_array('S', $disc)) : ?>
                        <div class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #1c6d9b; padding: 15px 20px; color: #fff;">S – Supportive/Considera</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a C style seems kind and supportive, and whenever you ask them a question, they are always patient and happy to help. However, while they always seem warm and sincere, they might focus too much energy on supporting others rather than on energizing the team. He/she is well-liked by everyone and can always be counted on to perform his/her job consistently. Sometimes they can be too cautious and tentative. They value relationships and team spirit, yet with a low profile.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside page-break">
                                <li>Performing in a consistent manner (reliable)</li>
                                <li>Patient</li>
                                <li>Helpful</li>
                                <li>Loyal</li>
                                <li>Good listener</li>
                                <li>Consultative & considerate of others’ views</li>
                                <li>Excellent team player</li>
                                <li>Providing reassurance in difficult times (peacemaker)</li>
                                <li>Creating a stable & harmonious work environment</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Overly accommodating</li> 
                                <li>Avoiding change (likes comfortable zone)</li> 
                                <li>Indecisiveness</li> 
                                <li>Could be too permissive & tolerant (taken advantage of)</li> 
                                <li>Being open to others can lead to missing on important issues</li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Maintenance of the status quo unless given reasons for change </li>
                                <li>Predictable routines </li>
                                <li>Credit for work accomplished</li>
                                <li>Sincere appreciation</li>
                                <li>Identification with a group</li>
                                <li>Standard operating procedures</li>
                                <li>Minimal conflict </li>
                            </ul>

                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>HR</li>
                                <li>Accounting</li>
                                <li>Office Manager</li>
                                <li>Support Functions in general</li>
                            </ul>
                        </div>
                    <?php endif ?>

                   <?php if (in_array('C', $disc)) : ?>

                        <div class="w-full bg-white dark:bg-gray-800 rounded flex flex-col items-left justify-left p-5 relative shadow">
                            <h2 class="text-gray-800 dark:text-gray-100 text-lg font-bold mb-3" style="background: #f49e11; padding: 15px 20px; color: #fff;">C – Cautious/Systematic</h2>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold">Description</h4>
                            
                            <p class="font-normal text-sm text-gray-600 dark:text-gray-100 mt-1">Someone with a D style has a systematic approach in dealing with others. They are not highly sociable and maintain a private nature. They require quality and accuracy, checking their work (and others’) two or three times before being satisfied. They want a stable environment where they can ensure reliable outcomes. He/she is careful, skeptical and ask a lot of probing questions. They have tendency to prioritize details and challenge other people’s ideas. Yet they tend to be conscientious and they do follow through on commitments.</p>
                            
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Strengths</h4>
                            <ul class="list-disc text-sm list-inside page-break">
                                <li>Adhering to key directives and standards</li>
                                <li>Concentrating on key details</li>
                                <li>Thinking analytically, weighing pros and cons</li>
                                <li>Using subtle or indirect approaches to conflict</li>
                                <li>Checking for accuracy & objectivity</li>
                                <li>Analyzing performance critically</li>
                                <li>Using a systematic approach to situations or activities</li>
                                <li>Providing structure for activities</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Limitations</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Overly critical of others</li>
                                <li>Overanalyzing</li>
                                <li>Isolating self (socially)</li>
                                <li>Can be counterproductive with too many details</li>
                                <li>Being too systematic makes them impersonal or lacking creativity</li>
                                <li>Can be too perfectionistic</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Preferred Environment</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Clearly defined performance expectations</li> 
                                <li>Values on quality and accuracy</li>
                                <li>Reserved, business-like atmosphere</li>
                                <li>Opportunities to demonstrate expertise</li>
                                <li>Control over those factors that affect their performance</li>
                                <li>Opportunity to ask "why" questions</li>
                                <li>Recognition for specific skills and accomplishments</li>
                            </ul>
                            <h4 class="text-gray-800 dark:text-gray-100 text-sm font-bold mb-3 mt-3">Most Effective Work Areas</h4>
                            <ul class="list-disc text-sm list-inside">
                                <li>Second line of Management</li>
                                <li>Strategic Planning/Thinking</li>
                                <li>Finance</li>
                                <li>R&D</li>
                                <li>Supply Chain</li>
                            </ul>
                        </div>

                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php endif ?>
    <footer>
        <p class="footer-note">{{ $footer }}</p>
    </footer>
    </body>
</html>

