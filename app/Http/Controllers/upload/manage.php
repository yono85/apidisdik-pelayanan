<?php
namespace App\Http\Controllers\upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\upload_files as tblUploadFiles;

class manage extends Controller
{
    //
    public function addNew($requst)
    {
        $Config = new Config;

        $newid = $Config->createnewid([
            'value'     =>  tblUploadFiles::count(),
            'length'    =>  15
        ]);

        $token = md5($newid);

        $addnew             =   new tblUploadFiles;
        $addnew->id         =   $newid;
        $addnew->type       =   $requst['type'];
        $addnew->token      =   $token;
        $addnew->extentions =   $requst['extention'];
        $addnew->url        =   $Config->apps()['URL_API'] . '/upload/files/' . $requst['name'];
        $addnew->user_id    =   $requst['user_id'];
        $addnew->status     =   1;
        $addnew->save();
        
    }
}