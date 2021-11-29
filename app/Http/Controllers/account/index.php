<?php
namespace App\Http\Controllers\account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\user_images as tblUserImages;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use App\user_resetpasswords as tblUserResetpasswords;
use Illuminate\Support\Facades\Hash;

class index extends Controller
{
    //
    public function show($request)
    {
        // get information accoun
        $account = $request;

        $Config = new Config;

        //
        if( $account['level'] == 1 ) // admin disdik
        {
            $admin = $this->aDisdik($request);
        }
        elseif( $account['level'] == 2)
        {
            $admin = $this->OPS($request);
        }
        else
        {
            $admin = '';
        }


        //get image
        $getimg = tblUserImages::where([
            'user_id'           =>  $account['id'],
            'status'            =>  1
        ])->first();



        // 
        $data = [
            'id'            =>  $account['id'],
            'key'           =>  $account['token'],
            'name'          =>  $account['name'],
            'email'         =>  $account['email'],
            'level'         =>  $account['level'],
            'bidang'        =>  $account['sublevel'],
            'seksi'         =>  $account['seksi'],
            'pelayanan'     =>  $account['set_bidang'],
            'image'         =>  $Config->apps()['URL_API'] . ( $getimg === null ? '/images/none/user.png' : $getimg->url),
            'admin'         =>  $admin,
            'company_id'    =>  $account['company_id']
        ];

        return $data;
    }

    //
    private function aDisdik($request)
    {
        $data = [
            'sublevel'      =>  $request['sublevel']
        ];

        return $data;
    }

    //
    private function OPS($request)
    {
        $data = [
            'sublevel'      =>  $request['sublevel']
        ];

        return $data;
    }

    //
    public function checkResetPassword(Request $request)
    {
        $Config = new Config;

        $token = trim($request->q);

        $cek = tblUserResetpasswords::where([
            'token'     =>  $token,
            'status'    =>  1
        ])
        ->count();

        if( $cek == 0)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data,404);
        }
    

        //
        $data = [
            'message'       =>  '',
            'response'      =>  $token
        ];

        return response()->json($data,200);
    }

    //
    public function resetPassword(Request $request)
    {
        $Config = new Config;
        
        $token = trim($request->token);

        //
        $cek = tblUserResetpasswords::where([
            'token'     =>  $token,
            'status'        =>  1
        ])->first();

        if($cek == null)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data,404);
        }

        //
        $upAccount = tblUsers::where([
            'id'      =>  $cek->id
        ])
        ->update([
            'password'      =>  Hash::make($request->password)
        ]);

        $upUserResetpassword = tblUserResetpasswords::where([
            'token'     =>  $token,
            'status'    =>  1
        ])
        ->update([
            'status'        =>  0
        ]);

        //
        $data = [
            'message'       =>  ''
        ];

        return response()->json($data,200);
    }

}