<?php
namespace App\Http\Controllers\access;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use Auth;
use DB;

class manage extends Controller
{

    //manage login
    // ceking token if this exist not login again
    // if this exist ceking on logins user
    // if logins users null return login
    public function login(Request $request)
    {

        $Config = new Config;

        
        // ceking on logins table users
        if( trim($request->token) != '' )
        {
            $cektoken = DB::table('logins')
            ->where([
                'token_jwt'         =>  $request->token,
                'status'            =>  1
            ])->count();


            if( $cektoken > 0 )
            {
                $data = [
                    'status'            =>  'Keep login',
                    'token'             =>  $request->token
                ];
    
                return response()->json($data, 200);
            }
        }


        // create token after validate 
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials))
        {
            
            $datalogins = [
                'account'       =>  $this->account($this->guard()->user()),
                'token'         =>  $token
            ];

            //create new log in table logins
            $logins = new \App\Http\Controllers\log\access\manage;
            $logins = $logins->logins($datalogins);


            return response()->json($datalogins, 200);

        }

        // if not wrong email or password insert
        return response()->json(['message' => 'Email atau Password tidak dikenal'], 401);

    }
    


    public function profile()
    {
        
        return $this->guard()->getToken();
    }



    //manage logout
    //cek expire session token
    public function logout(Request $request)
    {

        
        $token = trim($request->token);
        $this->guard()->logout();

        return response()->json(['message' => 'Anda berhasil logout'],200);
    }


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    // protected function respondWithToken($token)
    // {

    //     $data = [
    //         'token'     =>  $token,
    //         'expire'    =>  $this->guard()->factory()->getTTL() * 60
    //     ];

    //     return $data;
    //     // return response()->json([
    //     //     'token' => $token,
    //     //     // 'token_type' => 'bearer',
    //     //     'token_expire' => $this->guard()->factory()->getTTL() * 60
    //     // ], 200);
    // }

    public function guard()
    {
        return app('auth')->guard();
    }


}