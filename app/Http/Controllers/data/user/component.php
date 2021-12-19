<?php
namespace App\Http\Controllers\data\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\user_sublevels as tblUserSublevels;
use App\sub_pelayanans as tblSubPelayanans;
use App\pelayanans as tblPelayanans;
use App\users as tblUsers;

class component extends Controller
{
    //
    public function bidang(Request $request)
    {
        $level = trim($request->level);


        $getdata = tblUserSublevels::select(
            'id', 'name', 'sub'
        )
        ->where([
            'status'    =>  1
        ]);
        if( $level != "-1" )
        {
            $getdata = $getdata->where([
                "level_id"      =>  $level
            ]);
        }
        $getdata = $getdata->get();

        $data = [
            'message'       =>  '',
            'response'          =>  $getdata
        ];

        return response()->json($data, 200);
    }


    public function pelayanan(Request $request)
    {
        $sublevel = trim($request->id);


        $getdata = tblSubPelayanans::select(
            'id', 'name'
        )
        ->where([
            'sublevel'     =>  $sublevel,
            'status'    =>  1
        ])->get();

        $data = [
            'message'       =>  '',
            'response'          =>  $getdata
        ];

        return response()->json($data, 200);
    }


    public function bidangByUser(Request $request)
    {
        $level = trim($request->level);
        $set_id = trim($request->set);

        $getdata = tblUserSublevels::select(
            'id', 'name', 'sub'
        );
        if( $level != '9' && $level != "4")
        {
            $getdata = $getdata
            ->whereIn('id', [$set_id,1000005,1000006,1000008]);
        }
        $getdata = $getdata->where([
            'status'    =>  1
        ])
        ->get();

        $data = [
            'message'       =>  '',
            'response'          =>  $getdata
        ];

        return response()->json($data, 200);
    }


    public function subPelayanan(Request $request)
    {
        $bidang = trim($request->bidang);
        $sub = trim($request->sub);

        $getdata = tblPelayanans::where([
            'bidang_id'     =>  $bidang,
            'sub_id'        =>  $sub,
            'status'        =>  1
        ])->get();

        $data = [
            'message'       =>  '',
            'response'      =>  $getdata
        ];

        return response()->json($data, 200);

    }

    //TELER
    public function teller(Request $request)
    {
        $level = trim($request->level);
        $id = trim($request->id);

        $getdata = tblUsers::where([
            "level"             =>  4,
            "register_status"   =>  1,
            "status"            =>  1
        ]);
        if( $level != "9")
        {
            $getdata = $getdata->where([
                "id"     =>  $id
            ]);
        }
        
        $count = $getdata->count();

        if( $count > 0 )
        {
            foreach($getdata->get() as $row)
            {
                $list[] = [
                    "id"        =>  $row->id,
                    "name"      =>  $row->name
                ];
            }

            $data = [
                "message"       =>  "",
                "response"      =>  [
                    "list"          =>  $list
                ]
            ];

            return response()->json($data, 200);
        }

        $data = [
            "message"       =>  "Data tidak ditemukan"
        ];

        return response()->json($data, 404);


    }


}