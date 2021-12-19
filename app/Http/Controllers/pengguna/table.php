<?php
namespace App\Http\Controllers\pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use App\schools as tblSchools;

class table extends Controller
{
    //
    public function main(Request $request)
    {
        $Config = new Config;

        //
        $search = trim($request->src);
        $status = trim($request->selected_status);
        $sortname = trim($request->sort_name);
        $paging = trim($request->paging);

        //
        $getdata = tblUsers::from('users as u')
        ->select(
            'u.id', 'u.name', 'u.email', 'u.register_file', 'u.level', 'u.company_id', 'u.created_at',
            'ul.name as level_name'
        )
        ->leftJoin('user_levels as ul', function($join)
        {
            $join->on('ul.type', '=', 'u.level');
        })
        ->where([
            ['u.name', 'like', '%' . $search . '%'],
            ['u.level', '<', 9]
        ]);
        if( $status != "-1")
        {
            if( $status == "1")
            {
                $getdata = $getdata->where([
                    'u.register_file'       =>  1
                ]);
            }
            else
            {
                $getdata = $getdata->where([
                    'u.register_file'       =>  0
                ]);
            }
        }

        $count = $getdata->count();

        if( $count == 0)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data,404);
        }

        //VIDATA
        $vdata = $vdata = $getdata->orderBy('u.id', $sortname)
        ->take($Config->table(['paging'=>$paging])['paging_item'])
        ->skip($Config->table(['paging'=>$paging])['paging_limit'])
        ->get();

        // LOOP
        foreach($vdata as $row)
        {

            if($row->level == 3)
            {
                $getcomp = tblSchools::where([
                    'id'        =>  $row->company_id
                ])->first();
                $company = $getcomp->name;
            }
            else
            {
                $company = "";
            }

            $list[] = [
                'id'        =>  $row->id,
                'name'      =>  $row->name,
                'email'     =>  $row->email,
                'level_name'=>  $row->level_name,
                'date'      =>  $Config->timeago($row->created_at),
                'verify'    =>  $row->register_file,
                'company'   =>  $company
            ];
        }
        
        //
        $data = [
            'message'       =>  '',
            'response'      =>  [
                'list'          =>  $list,
                'paging'        =>  $paging,
                'total'         =>  $count,
                'countpage'     =>  ceil($count / $Config->table(['paging'=>$paging])['paging_item'] )
            ]
        ];

        return response()->json($data,200);
    }
}