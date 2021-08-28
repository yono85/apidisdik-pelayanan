<?php
namespace App\Http\Controllers\account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class index extends Controller
{
    //
    public function show($request)
    {

        // get information request in guard user
        $account = $request;

        $data = [
            'id'            =>  $account['id'],
            'name'          =>  $account['name'],
            'email'         =>  $account['email'],
            'level'         =>  $account['level'],
            'username'      =>  $account['username']
        ];

        return $data;
    }
}