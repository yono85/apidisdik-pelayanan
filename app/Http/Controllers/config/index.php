<?php
namespace App\Http\Controllers\config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class index extends Controller
{

    public function apps()
    {
        $data = [
            'NAME'      =>  'Simpeldik',
            'URL'       =>  env('URL_WEB'),
            'LOGO'      =>  env('URL_WEB') . '/assets/images/logo/logo-disdik-dki.png',
            'URL_API'   =>  env('URL_API')
        ];

        return $data;
    }

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

    //
    public function configApps()
    {
        $data   =   [
            'email'     =>  [
                'sender_id'     =>  '10001'
            ]
        ];

        return $data;

    }

    public function table($request)
    {
        $item = 15;
        $limit = (( (int)$request['paging'] - 1) * $item);

        $data = [
            'paging_item'       =>  $item,
            'paging_limit'      =>  $limit,
            'paging'            =>  $request['paging']
        ];

        return $data;
    }


    // TIMEAGO
    public function timeago($ptime)
    {

        $gettime = strtotime($ptime);

        $estimate_time = time() - $gettime;
        if( $estimate_time < 1 )
        {
            return '1d lalu';
        }

        $condition = [ 
            12 * 30 * 24 * 60 * 60  =>  'thn',
            30 * 24 * 60 * 60       =>  'bln',
            24 * 60 * 60            =>  'hari',
            60 * 60                 =>  'j',
            60                      =>  'm',
            1                       =>  'd'
        ];

        foreach( $condition as $secs => $str )
        {
            $d = $estimate_time / $secs;

            $r = round($d);

            if( $d >= 1 )
            {
                    // $r = round( $d );
                // return ' ' . $r . $str;
                
                if( $str == 'm' || $str == 'd')
                {   
                    return $r . $str . ' lalu';
                }
                elseif( $str == 'j' )
                {
                    if( $r < 4 )
                    {
                        return $r . $str . ' lalu';
                    }
                    else
                    {
                        return date('H.i', $gettime);
                    }
                }
                elseif( $str == 'hari' && $r < 7)
                {
                    return $this->namahari($ptime) . ', ' . date('H:i', $gettime);
                    
                }
                else
                {
                    return date('d/m/Y', $gettime);

                }

            }
        }

    } 

    // NAMA HARI
    function namahari($date)
    {
        $info=date('w', strtotime($date));

        switch($info){
            case '0': return "Minggu"; break;
            case '1': return "Senin"; break;
            case '2': return "Selasa"; break;
            case '3': return "Rabu"; break;
            case '4': return "Kamis"; break;
            case '5': return "Jumat"; break;
            case '6': return "Sabtu"; break;
        };
    }


    function typePegawai($request)
    {
        $data = [
            '1'     =>  'PNS',
            '2'     =>  'CPNS',
            '3'     =>  'KKI',
            '4'     =>  'HONOR'
        ];

        return $data[$request];
    }


}