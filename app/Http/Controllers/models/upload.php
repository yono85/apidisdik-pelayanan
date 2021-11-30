<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\upload_files as tblUploadFiles;

class upload extends Controller
{
    //
    public function main($request)
    {
        $Config = new Config;

        $file = $request['file'];
        $ext = $file->getClientOriginalExtension();
        $user_id = $request['user_id'];
        

        $newid = $Config->createnewid([
            'value'     =>  tblUploadFiles::count(),
            'length'    =>  15
        ]);

        $token = md5($newid);
        $namefile = $token . '.' . $ext;

        $url_file = env('URL_API') . '/' . $request['folder'] . $ext . '/' .$namefile;

        $addnew             =   new tblUploadFiles;
        $addnew->id         =   $newid;
        $addnew->type       =   $request['type']; // 1 = register, 2 replay
        $addnew->token      =   $token;
        $addnew->extentions =   $ext;
        $addnew->url        =   $url_file;
        $addnew->user_id    =   $user_id;
        $addnew->status     =   1;
        $addnew->save();

        //upload
        $dir = $ext === 'pdf' ? 'pdf' : ($ext === 'docx' ? 'doc' : 'images');

        $addUpload = new \App\Http\Controllers\upload\index;
        $addUpload = $addUpload->main([
            'file'      =>  $file,
            'name'      =>  $namefile,
            'path'      =>  $request['folder'] . $dir . '/'
        ]);

        return $data = [ 
            'id'            =>  $newid,
            'url'           =>  $url_file
        ];
    }
}