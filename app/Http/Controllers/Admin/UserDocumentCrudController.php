<?php
/**
 * File name: UserDocumentCrudController.php
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
use App\Settings\HomePageSettings;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserDocument;
use App\Transformers\Admin\UserTransformer;
use App\Transformers\Admin\UserDocumentTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserDocumentCrudController extends Controller
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
    public function index($id,UserFilters $filters)
    {   
        return Inertia::render('Admin/User/Documents', [
            'userdocuments' => function () use ($filters,$id) {
                return fractal(UserDocument::filter($filters)->where('user_id',$id)
                    ->paginate(request()->perPage != null ? request()->perPage : 10),
                    new UserDocumentTransformer())->toArray();
            },
            "currentId" => $id,
        ]); 
    }

    /**
     * Store an user
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {   

        $this->validate($request,[
            'document_path' => 'required|mimes:csv,xlx,xls,xlsx,pdf,doc,docx,ods'
        ]);
                
        if($request->file('document_path')){
    		$fileName = time().'.'.$request->document_path->extension();  
            $request->document_path->move(public_path('uploads'), $fileName);
            UserDocument::insert([
                'user_id' =>  $request->user_id,
                'title' => $request->title,
                'document_path' => $fileName
            ]);
        }else {
            return redirect()->back()->with('successMessage', 'User Document uploaded was faild try again!');
        }

        return redirect()->back()->with('successMessage', 'User Document was successfully uploaded!');
    }

    /**
     * Show an user
     *
     * @param $id
     * @return array
     */
    public function show($id)
    {
        $user = UserDocument::find($id);
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
        
    }

    /**
     * Delete an user
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($id){
        
        $upload = UserDocument::findOrFail($id);

        $upload->delete();

        $currentfile = $upload->document_path;

        $userfile = public_path('public/uploads/').$currentfile;

        if(file_exists($userfile)) {

            @unlink($userfile);
                
        }
        return redirect()->back()->with('successMessage', 'User Document was successfully deleted!');
               
    }


}
