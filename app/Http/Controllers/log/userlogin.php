<?php
namespace App\Http\Controllers\log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\user_logins as tblUserLogins;

class userlogin extends Controller
{
    //
    public function add($request)
    {
        $Config = new Config;

        $update = tblUserLogins::where([
            'user_id'       =>  $request['user_id']
        ])
        ->update([
            'logout'        =>  1,
            'logout_date'   =>  date('Y-m-d H:i:s', time())
        ]);


        $newid = $Config->createnewid([
            'value'     =>  tblUserLogins::count(),
            'length'    =>  15
        ]);

        //
        $add                =   new tblUserLogins;
        $add->id            =   $newid;
        $add->user_id       =   $request['user_id'];
        $add->token         =   md5($newid);
        $add->token_jwt     =   $request['token'];
        $add->logout        =   0;
        $add->logout_date   =   '';
        $add->status        =   1;
        $add->save();
    }
}