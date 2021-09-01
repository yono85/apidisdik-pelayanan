<?php
namespace App\Http\Controllers\access;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;

class manage extends Controller
{

    public function login(Request $request)
    {

        
        $email = trim($request->email);
        $password = trim($request->password);

        //GET DATA USERS
        $getData = tblUsers::where([
            'email'     =>  $email
        ])->first();


        //CEK NOT EXIST ACCOUNT
        if( $getData == null)
        {
            $data = [
                'message'       =>  'Alamat email tidak terdaftar',
                'focus'         =>  'email'
            ];
            return response()->json($data, 404);
        }


        //CEK PASSWORD
        $pwd = $getData->password;
        $cekPWD = Hash::check($request->password, $pwd) ? 1 : 0;

        if( $cekPWD == 0 )
        {
            $data = [
                'message'       =>  'Password tidak dikenali',
                'focus'         =>  'password'
            ];
            return response()->json($data, 404);
        }


        $data = [
            'message'       =>  '',
            'data'          =>  $getData
        ];

        return response()->json($data);
    }

}