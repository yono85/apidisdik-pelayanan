<?php
namespace App\Http\Controllers\pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;

class manage extends Controller
{
    //
    public function create(Request $request)
    {

        //
        if( trim($request->level_selected) == "9")
        {
            $ceksu = tblUsers::where([
                "level"     =>  9
            ])->count();
    
            // LEVEL SUPER ADMIN HANYA 2
            if( $ceksu >= 2 )
            {
                $data = [
                    "message"       =>  "Akun dengan level Super Admin sudah tidak dijinkan"
                ];
    
                return response()->json($data, 404);
            }
        }


        //INSERT TABLE USERS
        $adduser = new \App\Http\Controllers\models\users;
        $adduser = $adduser->addNewAdmin( $request );

        //INSERT TABLE USER REGISTER
        $addregister = new \App\Http\Controllers\models\users;
        $addregister = $addregister->userRegister([
            'user_id'       =>  $adduser['id']
        ]);


        //create email sender table
        $newEmailSender = new \App\Http\Controllers\models\email;
        $newEmailSender = $newEmailSender->EmailRegisters(['user_id'=>$adduser['id']]);

        $data = [
            "message"       =>  "Admin berhasil dibuat",
            "response"      =>  $newEmailSender
        ];

        return response()->json($data, 200);
    }
}