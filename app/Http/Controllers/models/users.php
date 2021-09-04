<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use App\user_resetpasswords as tblUserResetPasswords;

class users extends Controller
{
    //ADD NEW USER
    public function addNew($request)
    {

    }

    //
    public function userResetPassword($request)
    {
        $Config = new Config;

        $update = tblUserResetPasswords::where([
            'user_id'       =>  $request['user_id']
        ])
        ->update([
            'status'        =>  0
        ]);

        //
        $newid = $Config->createnewid([
            'value'     =>  tblUserResetPasswords::count(),
            'length'    =>  15
        ]);

        $add = new tblUserResetPasswords;
        $add->id                =   $newid;
        $add->token             =   md5($newid);
        $add->user_id           =   $request['user_id'];
        $add->status            =   1;
        $add->save();

        return $newid;
    }

}