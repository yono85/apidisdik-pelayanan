<?php
namespace App\Http\Controllers\account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\config\index as Config;

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

    //preview data verify
    public function viewVerifyAdmin(Request $request)
    {
        $Config = new Config;

        $getdata = tblUsers::from("users as u")
        ->select(
            "u.id", "u.name", "u.email",
            "s.name as company_name",
            "uf.url as url_file"
        )
        ->leftJoin("schools as s", function($join)
        {
            $join->on("s.id", "=", "u.company_id");
        })
        ->leftJoin("upload_files as uf", function($join)
        {
            $join->on("uf.user_id", "=", "u.id")
            ->where([
                "uf.type"       =>  1,
                "uf.status"     =>  1
            ]);
        })
        ->where([
            'u.id'            =>  trim($request->id)
        ])
        ->first();

        $data = [
            "message"       =>  "",
            "response"      =>  [
                "id"            =>  $getdata->id,
                "name"          =>  $getdata->name,
                "email"         =>  $getdata->email,
                "company_name"  =>  $getdata->company_name,
                "url_file"      =>  $getdata->url_file
            ]
        ];

        return response()->json($data, 200);
    }

    //VERIFY FILE
    public function verifyFile(Request $request)
    {
        $id = trim($request->user_id);

        //
        $updata = tblUsers::where([
            "id"        =>  $id
        ])
        ->update([
            "register_file"         =>  1
        ]);

        $data = [
            "message"       =>  "Pengguna berhasil di verifikasi",
            "id"            =>  $id
        ];

        return response()->json($data, 200);
    } 


}