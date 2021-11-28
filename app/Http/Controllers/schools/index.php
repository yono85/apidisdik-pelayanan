<?php
namespace App\Http\Controllers\schools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\schools as tblSchools;

class index extends Controller
{
    //
    public function lists(Request $request)
    {
        $src = '%' . trim($request->q) .'%';

        $cek = tblSchools::where([
            ['name','like',$src],
            ['status','=',1]
        ]);

        if( $cek->count() == 0 )
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data,404);
        }

        $getdata = $cek->orderBy('name','asc')
        ->skip(0)->take(50)->get();

        foreach($getdata as $row)
        {
            $list[] = [
                'id'        =>  $row->id,
                'name'      =>  $row->name
            ];
        }

        $data = [
            'message'       =>  '',
            'response'      =>  [
                'list'          =>  $list
            ]
        ];

        return response()->json($data,200);
    }
}