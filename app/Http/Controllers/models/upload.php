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


        $addnew             =   new tblUploadFiles;
        $addnew->id         =   $newid;
        $addnew->type       =   $request['type'];
        $addnew->token      =   $token;
        $addnew->extentions =   $ext;
        $addnew->url        =   '/upload/' . $ext . '/' .$namefile;
        $addnew->user_id    =   $user_id;
        $addnew->status     =   1;
        $addnew->save();

        //upload
        $dir = $ext === 'pdf' ? 'pdf' : ($ext === 'docx' ? 'doc' : 'images');

        $addUpload = new \App\Http\Controllers\upload\index;
        $addUpload = $addUpload->main([
            'file'      =>  $file,
            'name'      =>  $namefile,
            'path'      =>  'upload/' . $dir . '/'
        ]);
    }
}