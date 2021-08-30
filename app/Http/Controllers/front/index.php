<?php
namespace App\Http\Controllers\front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class index extends Controller
{
    //
    public function main(Request $request)
    {
        $data = [
            'message'       =>  'Selamat Datang di Pelayanaan Disdik'
        ];

        return response()->json($data, 200);
    }
}