<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\notifications as tblNotifications;
use App\Http\Controllers\config\index as Config;

class notifications extends Controller
{
    //
    public function main($request)
    {
        $Config = new Config;

        //
        $newid = $Config->createnewid([
            'value'         =>  tblNotifications::count(),
            'length'        =>  15
        ]);

        //
        $addnew                 =   new tblNotifications;
        $addnew->id             =   $newid;
        $addnew->type           =   $request['type']; 
        $addnew->groups         =   $request['groups'];
        $addnew->from_id        =   $request['from_id'];
        $addnew->to_id          =   $request['to_id'];
        $addnew->title          =   $request['title'];
        $addnew->text           =   $request['text'];
        $addnew->url            =   $request['url'];
        $addnew->read           =   0;
        $addnew->read_id        =   0;
        $addnew->read_date      =   '';
        $addnew->status         =   1;
        $addnew->save();

        //notes:
        // type: 1 = verifikasi akun

        //groups: 0 = admin

    }
}