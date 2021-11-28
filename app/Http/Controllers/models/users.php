<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use App\user_resetpasswords as tblUserResetPasswords;
use App\user_registers as tblUserRegisters;
use Illuminate\Support\Facades\Hash;

class users extends Controller
{
    //ADD NEW USER
    public function addNew($request)
    {
        
        //include 
        $Config = new Config;

        //request
        $name = trim($request->name);
        $email = trim($request->email);
        $level = trim($request->level);
        $status = trim($request->status_selected);
        $noid = trim($request->no_id);
        //
        $bidang = (int)$level > 2 ? 0 : trim($request->bidang_selected);
        $seksi = (int)$level > 2 ? 0 : trim($request->pelayanan_selected);
        $company_id = (int)$level === 3 ? trim($request->school_id) : 0;
        // $set_bidang = (int)$level === 3 ? trim($request->set_bidang) : 0;

        //create
        $newid = $Config->createnewid([
            'value'     =>  tblUsers::count(),
            'length'    =>  15
        ]);

        $token = md5($newid);

        //
        $addnew                 = new tblUsers;
        $addnew->id             =   $newid;
        $addnew->token          =   $token;
        $addnew->username       =   '';
        $addnew->name           =   $name;
        $addnew->gender         =   0;
        $addnew->birth          =   '';
        $addnew->email          =   $email;
        $addnew->password       =   '';
        $addnew->level          =   $level; // 9 admin, 1 disdik, 2 sudin, 3 sch
        $addnew->sublevel       =   $bidang; //bidang
        $addnew->seksi          =   $seksi; //seksi
        $addnew->set_bidang      =   0;
        $addnew->type           =   $status;
        $addnew->noid           =   $noid;
        $addnew->company_id      =   $company_id;
        $addnew->register_status = 0;
        $addnew->register_file  = 0;
        $addnew->status         =   1;
        $addnew->save();


        // ID REGISTER ON TBALE
        $data = [
            'id'        =>  $newid,
            'token'     =>  $token
        ];

        return $data;

    }


    //
    // REGISTER
    public function userRegister($request)
    {
        $Config = new Config;

        //create table registers
        $newidreg = $Config->createnewid([
            'value'     =>  tblUserRegisters::count(),
            'length'    =>  15
        ]);

        $addreg                 =   new tblUserRegisters;
        $addreg->id             =   $newidreg;
        $addreg->token          =   md5($newidreg);
        $addreg->user_id        =   $request['user_id'];
        $addreg->status         =   1;
        $addreg->save();
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


        //insert email sender table
        $insEmail = new \App\Http\Controllers\models\email;
        $insEmail = $insEmail->emailForgetPassword(['user_id'=>$request['user_id']]);

        return [
            'resetid'       =>  $newid,
            'sendid'        =>  $insEmail['id'],
            'token'         =>  $insEmail['token']
        ];
    }

}