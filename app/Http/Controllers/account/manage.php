<?php
namespace App\Http\Controllers\account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;
use Illuminate\Support\Facades\Hash;

class manage extends Controller
{
    //
    public function checkVerifyAdmin(Request $request)
    {

        $check = tblUsers::where([
            'token'     =>  $request->token
        ])->first();

        if( $check === null )
        {
            $data = [
                'message'       =>  'Opss.. akun Anda tidak ditemukan'
            ];

            return response()->json($data, 404);
        }


        if( $check->register_file == '0' )
        {
            $data = [
                'message'       =>  'Akun anda masih menununggu verifikasi admin, harap menunggu'
            ];

            return response()->json($data, 401);

        }


        $data = [
            'message'       =>  '',
            'response'      =>  [
                'email'         =>  $check->email
            ]
        ];

        return response()->json($data, 200);
    }


    public function loginVerify(Request $request)
    {
        $check = tblUsers::where([
            'email'     =>  $request->email
        ])->first();

        if( $check === null)
        {
            $data = [
                'message'       =>  'Opss.. akun Anda tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        $pwd = $check->password;
        $cekPWD = Hash::check($request->password, $pwd) ? 1 : 0;

        if( $cekPWD == 0 )
        {
            $data = [
                'message'       =>  'Password yang Anda masukan salah'
            ];
            return response()->json($data, 404);
        }


        $datalogin = [
            'email'     =>  $request->email,
            'password'  =>  $request->password
        ];
        $truelogin = new \App\Http\Controllers\access\manage;
        $truelogin = $truelogin->trueLogin($datalogin);




        return response()->json($truelogin, 200);
    }
}