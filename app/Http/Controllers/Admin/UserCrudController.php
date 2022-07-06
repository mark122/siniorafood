<?php
/**
 * File name: UserCrudController.php
 * Last modified: 19/07/21, 12:55 AM
 * Author: NearCraft - https://codecanyon.net/user/nearcraft
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Filters\UserFilters;
use App\Filters\UserDocumentFilters;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserDocument;
use App\Transformers\Admin\UserTransformer;
use App\Transformers\Admin\UserDocumentTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin']);
    }

    /**
     * List all users
     *
     * @param UserFilters $filters
     * @return \Inertia\Response
     */
    public function index(UserFilters $filters)
    {
        return Inertia::render('Admin/Users', [
            'roles' => [
                ['value' => 1, 'text' => 'Admin'],
                ['value' => 2, 'text' => 'Instructor'],
                ['value' => 3, 'text' => 'Student'],
                ['value' => 5, 'text' => 'Guest'],
            ],
            'users' => function () use($filters) {
                return fractal(User::with('roles:id,name')->filter($filters)
                ->paginate(request()->perPage != null ? request()->perPage : 10),
                       new UserTransformer())->toArray();
                  },
            'userGroups' => UserGroup::select(['id', 'name'])->active()->get()
        ]);
    }

    /**
     * Store an user
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'user_name' => $request['user_name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'is_active' => $request['is_active'],
            'gpi' => $request['gpi'],
            'assessment_centre' => $request['assessment_centre'],
            'result_360' => $request['result_360'],
        ]);

        if($user) {
            $user->assignRole($request['role']);
            $user->userGroups()->sync($request->user_groups);
        }

        return redirect()->back()->with('successMessage', 'User was successfully added!');
    }

    /**
     * Show an user
     *
     * @param $id
     * @return array
     */
    public function show($id)
    {
        $user = User::find($id);
        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Edit an user
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {   
        $user = User::find($id);
        return response()->json([
            'user' => $user,
            'userGroups' => $user->userGroups()->pluck('id')
        ]);
    }

    /**
     * Update an user
     *
     * @param UpdateUserRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        if(config('qwiktest.demo_mode')) {
            return redirect()->back()->with('errorMessage', 'Demo Mode! Users can\'t be changed.');
        }

        $user = User::find($id);
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->user_name = $request['user_name'];
        $user->email = $request['email'];
        $user->is_active = $request['is_active'];

        $user->gpi = $request['gpi'];
        $user->assessment_center = $request['assessment_center'];
        $user->result_360 = $request['result_360'];

        // If user is in-active, delete all sessions
        if($request['is_active'] == false) {
            if (config('session.driver') == 'database') {
                DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $user->getAuthIdentifier())
                    ->delete();
            }
        }

        if($request['password'] != null || $request['password'] != '') {
            $user->password = Hash::make($request['password']);
        }

        $user->update();

        $user->syncRoles($request['role']);
        $user->userGroups()->sync($request->user_groups);

        return redirect()->back()->with('successMessage', 'User was successfully updated!');
    }

    /**
     * Delete an user
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if(config('qwiktest.demo_mode')) {
            return redirect()->back()->with('errorMessage', 'Demo Mode! Users can\'t be deleted.');
        }

        try {
            $user = User::find($id);

            if(!$user->canSecureDelete('practiceSessions', 'quizSessions')) {
                return redirect()->back()->with('errorMessage', 'Unable to Delete User . Remove all associations and Try again!');
            }

            $user->userGroups()->detach();
            $user->roles()->detach();
            $user->secureDelete('practiceSessions', 'quizSessions');
        }
        catch (\Illuminate\Database\QueryException $e){
            return redirect()->back()->with('errorMessage', 'Unable to Delete User . Remove all associations and Try again!');
        }
        return redirect()->back()->with('successMessage', 'User was successfully deleted!');
    }

    public function upload_document(Request $request){
        
        $this->validate($request,[
            'userid' => 'required',
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
        ]);

        /*if($request->file){
            $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
            \Image::make($request->file)->save(public_path('images/uploads/').$name);
            $request->merge(['file' => $name]);
        }
        UserDocuments::create($request->all()); */
        
        $userdocuments = new UserDocuments;
        
        if($request->file()) {
                $file_name = time().'_'.$request->file->getClientOriginalName();
                $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
    
                $userdocuments->file = time().'_'.$request->file->getClientOriginalName();
                $userdocuments->userid = $request->userid;
                $userdocuments->save();
    
                return response()->json(['success'=>'File uploaded successfully.']);
            }
    }

    public function document_list($id, UserDocumentFilters $filters){

        //$user_documents =  DB::connection(config('session.connection'))->table('user_documents')->get();

        /*return Inertia::render('Admin/User/Documents', [
            'questions' => function () use($filters) {
                return fractal(UserDocument::filter($filters)
                    ->with(['user_documents:id,file'])
                    ->orderBy('id', 'desc')
                    ->paginate(request()->perPage != null ? request()->perPage : 10),
                    new UserDocumentTransformer())->toArray();
            },
        ]);*/
        // return Inertia::render('Admin/User/Documents', [
        //     'users_doucment' => $user_documents 
        // ]);

        return Inertia::render('Admin/User/Documents', [
            'userdocuments' => function () use ($filters) {
                return fractal(UserDocument::select(['id', 'title','document_path'])->filter($filters)
                    ->paginate(request()->perPage != null ? request()->perPage : 10),
                    new UserDocumentTransformer())->toArray();
            },
        ]);   

    }

    public function document_delete($id){
        
        $upload = UserDocuments::findOrFail($id);

        $upload->delete();

        // $currentfile = $upload->file;

        // $userfile = public_path('storage/uploads/').$currentfile;

        // if(file_exists($userfile)) {

        //     @unlink($userfile);
                
        // }

     return response()->json(['success'=>'File deleted successfully.']);
               
    }

}
