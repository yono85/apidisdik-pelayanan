<?php
namespace App\Http\Controllers\account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class index extends Controller
{
    //
    public function show($request)
    {

        // get information accoun
        $account = $request;

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
        $data = [
            'id'            =>  $account['id'],
            'name'          =>  $account['name'],
            'email'         =>  $account['email'],
            'level'         =>  $account['level'],
            'sublevel'      =>  $account['sublevel'],
            'admin'         =>  $admin
        ];

        return $data;
    }

    private function aDisdik($request)
    {
        $data = [
            'sublevel'      =>  $request['sublevel']
        ];

        return $data;
    }

    private function OPS($request)
    {
        $data = [
            'sublevel'      =>  $request['sublevel']
        ];

        return $data;
    }
}