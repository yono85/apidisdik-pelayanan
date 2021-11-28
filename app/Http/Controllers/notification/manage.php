<?php
namespace App\Http\Controllers\notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;

class manage extends Controller
{
    //
    public function newUsers($request)
    {

        $getdata = tblUsers::where([
            'id'        =>  $request['user_id']
        ])->first();

        $datanotification = [
            'type'          =>  1,
            'groups'        =>  0,
            'from_id'       =>  $request['user_id'],
            'to_id'         =>  0,
            'title'         =>  'Verifikasi Akun',
            'text'          =>  'Segera lakukan verifikasi akun untuk user ' . $getdata->name,
            'url'           =>  '/dashboard/users/verification?q=' . $getdata->token
        ];

        $create = new \App\Http\Controllers\models\notifications;
        $create->main($datanotification);
    }
}