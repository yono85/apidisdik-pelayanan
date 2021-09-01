<?php
namespace App\Http\Controllers\config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class index extends Controller
{

    
    //create new id to insert data
    public function createnewid($request)
    {
        $numb = (int)$request['value'];
        $numb++;

        $length = ( (int)$request['length'] - 1);
        $sprint = sprintf('%0'.$length.'s', 0);

        $condition = [ 
            10 . $sprint =>  9,
            9 . $sprint  =>  8,
            8 . $sprint  =>  7,
            7 . $sprint  =>  6,
            6 . $sprint  =>  5,
            5 . $sprint  =>  4,
            4 . $sprint  =>  3,
            3 . $sprint  =>  2,
            2 . $sprint  =>  1
        ];

        $sprintnew = strlen($numb) === (int)$request['length'] ? substr($numb, 1) : $numb;

        foreach($condition as $row => $val)
        {
            if( $numb < $row )
            {
                $value = $val . sprintf('%0'.$length.'s', $sprintnew);;
            }
        }

        return $value;
    }


    // default date for table
    public function date()
    {
        return date('Y-m-d H:i:s', time());
    }


    //number
    public function number($request)
    {
        return preg_replace('/\D/', '', $request);
    }


    //create new uniq (number and char A-Z)
    public function createuniq($q)
    {
        $length = (int)$q['length'];
        $value = (int)$q['value'];

        //
        $char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $value;
        $charlength = strlen($char);
        $rand = '';

        //
        for ($i = 0; $i < $length; $i++)
        {
            $rand .= $char[rand(0, $charlength - 1)];
        }
        return $rand;
    }


}