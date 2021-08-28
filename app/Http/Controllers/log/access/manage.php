<?php
namespace App\Http\Controllers\log\access;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use DB;

class manage extends Controller
{
    //user logins
    public function logins($request)
    {

        $Config = new Config;


        //update logins
        $updatelogins = DB::table('logins')
        ->where([
            'user_id'       =>  $request['user_id'],
            'status'        =>  1
        ])
        ->update([
            'logout'        =>  1,
            'type_logout'   =>  1,
            'date_logout'   =>  $Config->date(),
            'status'        =>  0
        ]);


        //create new id
        $newid = DB::table('logins')->count();
        $newid++;
        // $newidlogins = $newid;
        $datacreatenewid = [
            'value'             =>  $newid++,
            'length'            =>  16
        ];
        $newidlogins = $Config->createnewid($datacreatenewid);

        $datalog = [
            'id'            =>  $newidlogins,
            'token'         =>  md5($newidlogins),
            'token_jwt'     =>  $request['token'],
            'user_id'       =>  $request['user_id'],
            'ip_address'    =>  '',
            'country_id'    =>  '',
            'city'          =>  '',
            'device'        =>  '',
            'os'            =>  '',
            'browser'       =>  '',
            'date_login'    =>  $Config->date(),
            'logout'        =>  0,
            'type_logout'   =>  0,
            'date_logout'   =>  '',
            'status'        =>  1
        ];

        $inslogins = DB::table('logins')->insert($datalog);


        //insert user logs

    }



    //logout
    public function logout($request)
    {
        $token = $request->token;

        $uplogins = DB::table('logins')
        ->where([
            'token_jwt'         =>  $token
        ])->update(['status'       =>   0]);
    }
}