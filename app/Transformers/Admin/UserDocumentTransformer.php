<?php
/**
 * File name: CategoryTransformer.php
 * Last modified: 01/02/21, 5:16 PM
 * Author: NearCraft - https://codecanyon.net/user/nearcraft
 * Copyright (c) 2021
 */

namespace App\Transformers\Admin;

use App\Models\UserDocument;
use League\Fractal\TransformerAbstract;

class UserDocumentTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Category $category
     * @return array
     */
    public function transform(UserDocument $userdocuments)
    {
        return [
            'id' => $userdocuments->id,
			'user_id' => $userdocuments->user_id,
            'title' => $userdocuments->title,
            'type' => 'pdf',
            'document_path' => url('/').'/uploads/'.$userdocuments->document_path
        ];
    }
}
