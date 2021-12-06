<?php
namespace App\Http\Controllers\access;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use App\user_resetpasswords as tblUserResetPasswords;
use App\email_sender_tables as tblEmailSenderTables;
use App\user_logins as tblUserLogins;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;

class manage extends Controller
{

    public function login(Request $request)
    {
        //
        $Config = new Config;

        //
        $email = trim($request->email);
        $password = trim($request->password);

        //GET DATA USERS
        $getData = tblUsers::from('users as u')
        ->select(
            'u.id', 'u.password', 'u.register_status', 'u.token',
            DB::raw('IFNULL(ur.token, "") as register_token')
        )
        ->leftJoin('user_registers as ur', function($join)
        {
            $join->on('ur.user_id', '=', 'u.id')
            ->where([
                'ur.status'        =>  1
            ]);
        })
        ->where([
            'u.email'     =>  $email
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


        //CEK STATUS REGISTRASI
        if( $getData->register_status == 0)
        {
            $data = [
                'message'   =>  'Akun anda menunggu verifikasi silahkan lakukan <a href="/registers/success?token='.$getData->token.'">Verifikasi disini</a>'
            ];

            return response()->json($data,401);
        }

        //CEK PASSWORD
        $pwd = $getData->password;
        $cekPWD = Hash::check($password, $pwd) ? 1 : 0;

        if( $cekPWD == 0 )
        {
            $data = [
                'message'       =>  'Password tidak dikenali',
                'focus'         =>  'password'
            ];
            return response()->json($data, 404);
        }

        


        //DATA LOGIN
        $datalogin = [
            'email'     =>  $email,
            'password'  =>  $password
        ];

        //CALL FUNCTION TRUE LOGIN
        $login = $this->trueLogin($datalogin);

        //RESPONSE
        return response()->json($login,200);
    }


    // True login Credential token JWT
    public function trueLogin($request)
    {
        $credentials = [
            'email'     =>  $request['email'],
            'password'  =>  $request['password']
        ];
        $token = $this->guard()->attempt($credentials);

        if( $token == false )
        {
            $data = [
                'message'       =>  'Proses login gagal'
            ];

            return $data;
        }

        //GET DATA ACCOUNT
        $account = new \App\Http\Controllers\account\index;
        $account = $account->show($this->guard()->user());

        //datalogs
        $datalogs = [
            'user_id'       =>  $account['id'],
            'token'         =>  $token
        ];

        //CALL INSERT LOGS
        $insetLogs = new \App\Http\Controllers\log\userlogin;
        $insetLogs = $insetLogs->add($datalogs);
        

        //DATA
        $data = [
            'message'       =>  '',
            'response'      =>  [
                'account'       =>  $account,
                'token'         =>  $token
            ]
        ];

        return $data;
    }

    public function forgetpassword(Request $request)
    {
        //
        $Config = new Config;

        //
        $email = trim($request->email);

        //GET DATA USERS
        $getData = tblUsers::from('users as u')
        ->select(
            'u.id', 'u.password', 'u.register_status', 'u.token',
            DB::raw('IFNULL(ur.token, "") as register_token')
        )
        ->leftJoin('user_registers as ur', function($join)
        {
            $join->on('ur.user_id', '=', 'u.id')
            ->where([
                'ur.status'        =>  1
            ]);
        })
        ->where([
            'u.email'     =>  $email
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

        //CEK STATUS REGISTRASI
        if( $getData->register_status == 0)
        {
            $data = [
                'message'   =>  'Akun anda menunggu verifikasi silahkan lakukan <a href="/registers/success?token='.$getData->token.'">Verifikasi disini</a>'
            ];

            return response()->json($data,404);
        }

        //CEK LIMIT REQUSET PERDAY
        $now = date('Y-m-d', time());

        $ceklimit = tblUserResetPasswords::where([
            ['created_at', 'like', '%' . $now . '%']
        ])->count();

        if( $ceklimit > 2)
        {
            $data = [
                'message'       =>  'Permintaan perubahan password maksimal 3x dalam 1 hari'
            ];

            return response()->json($data, 401);
        }


        //INSERT DATA RESET PASSWORD
        $datareset = [
            'user_id'       =>  $getData->id
        ];
        $insreset = new \App\Http\Controllers\models\users;
        $insreset = $insreset->userResetPassword($datareset);

        $data = [
            'message'       =>  '',
            'response'      =>  [
                'resetid'   =>  $insreset['resetid'],
                'sendid'            =>  $insreset['sendid'],
                'token'         =>  $insreset['token']
            ]
        ];

        return response()->json($data,200);
    }

        //GUARD
    public function guard()
    {
        return app('auth')->guard();
    }


    public function logout(Request $request)
    {
        $getdata = tblUserLogins::where([
            'token_jwt'     =>  $request->token
        ])
        ->update([
            'logout'        =>  1,
            'logout_date'   =>  date('Y-m-d H:i:s', time())
        ]);
        
        $data = [
            'message'       =>  '',
            'response'      =>  [
                'redirect'      =>  '/login'
            ]
        ];

        return response()->json($data,200);
    }

}