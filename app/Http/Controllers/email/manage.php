<?php
namespace App\Http\Controllers\email;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\email_sender_tables as tblEmailSenderTables;
use App\users as tblUsers;


class manage extends Controller
{

    public function dataEmail($request)
    {
        $getdata = tblEmailSenderTables::where([
            'id'       =>  $request
        ])->first();


        if($getdata == null)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        $send = new \App\Http\Controllers\email\send;
        $send = $send->main($getdata);

        //if not sending
        if( $send['message'] != '')
        {
            return response()->json([
                'message'       =>  $send['message']
            ],401);
        }


        return response()->json($send, 200);
    }


    //
    public function registers(Request $request)
    {
        $Config = new Config;
        $token = trim($request->token);


        $getuser = tblUsers::where([
            'token'     =>  $token
        ])->first();

        $getdata = tblEmailSenderTables::where([
            'user_id'       =>  $getuser->id,
            'status'        =>  1
        ])->first();

        //data
        $data = $this->dataEmail($getdata->id);

        return $data;
    }


    public function main(Request $request)
    {
        $getdata = tblEmailSenderTables::where([
            'token'       =>  trim($request->token)
        ])->first();


        if($getdata == null)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        $send = new \App\Http\Controllers\email\send;
        $send = $send->main($getdata);

        // if not sending 
        if( $send['message'] != '')
        {
            return response()->json([
                'message'       =>  $send['message']
            ],401);
        }

        //success
        return response()->json($send, 200);

        // $data = [
        //     'message'       =>  'resend'
        // ];

        // return response()->json($data,200);
    }


}