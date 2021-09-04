<?php
namespace App\Http\Controllers\data;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;
use Illuminate\Support\Facades\Hash;

class inject extends Controller
{
    //
    public function resetpassword(Request $request)
    {
        $email = trim($request->email);
        $password = trim($request->password);

        $cek = tblUsers::where([
            'email'         =>  $email
        ])
        ->count();

        if( $cek == 0)
        {
            $data = [
                'message'       =>  'Email tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        //
        $update = tblUsers::where([
            'email'         =>  $email
        ])
        ->update([
            'password'      =>  Hash::make($password)
        ]);

        $data = [
            'message'       =>  'Perubahan password berhasil'
        ];

        return response()->json($data, 200);
    }
}