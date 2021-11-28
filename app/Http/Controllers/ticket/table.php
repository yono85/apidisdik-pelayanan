<?php
namespace App\Http\Controllers\ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\tickets as tblTickets;
use DB;

class table extends Controller
{

    public function main()
    {
        $getdata = tblTickets::from("tickets as t")
        ->where([
            't.status'      =>  1
        ]);

        return $getdata;
    }

    //
    public function pengajuan(Request $request)
    {
        $Config = new Config;

        $src = trim($request->search);
        $paging = trim($request->paging);
        $sortname = trim($request->sort_name);
        $status = trim($request->selected_status);
        
        //
        $getdata = $this->main();
        $getdata = $getdata->select(
            't.id', 't.kode', 't.date','t.progress', 't.detail',
            'u.name as user_name', 'u.type', 'u.noid',
            'cp.name as company_name',
            'us.name as bidang',
            'sp.name as seksi',
            'p.name as pelayanan'
        )
        ->leftJoin('users as u', function($join)
        {
            $join->on('u.id', '=', 't.user_id');
        })
        ->leftJoin('schools as cp', function($join)
        {
            $join->on('cp.id', '=', 'u.company_id');
        })
        ->leftJoin('user_sublevels as us', function($join)
        {
            $join->on('us.id', '=', 't.type');
        })
        ->leftJoin('sub_pelayanans as sp', function($join)
        {
            $join->on('sp.id', '=', 't.subtype');
        })
        ->leftJoin('pelayanans as p', function($join)
        {
            $join->on('p.id', '=', 't.pelayanan');
        })
        ->where([
            ['t.kode', 'like', '%' . $src . '%']
        ]);
        if( $status != "-1")
        {
            $getdata = $getdata->where([
                't.progress'        =>  $status
            ]);
        }

        $count = $getdata->count();

        if($count == 0)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        //
        $vdata = $getdata->orderBy('t.id', $sortname)
        ->take($Config->table(['paging'=>$paging])['paging_item'])
        ->skip($Config->table(['paging'=>$paging])['paging_limit'])
        ->get();

        foreach($vdata as $row)
        {

            $getreplay = DB::table('vw_ticket_replays as tr')
            ->select(
                'tr.id', 'tr.type', 'tr.date',
                'u.name as user_name'
            )
            ->leftJoin('users as u', function($join)
            {
                $join->on('u.id', '=', 'tr.user_id');
            })
            ->where([
                'tr.ticket_id'  =>  $row->id,
                'tr.status'     =>  1
            ])
            ->get();

            if( count($getreplay) > 0)
            {
                foreach($getreplay as $rowx)
                {
                    $listreplay[] = [
                        'id'        =>  $rowx->id,
                        'type'      =>  $rowx->type === 1 ? 'progress' : 'done',
                        'date'      =>  $Config->timeago($rowx->date),
                        'user'      =>  $rowx->user_name,
                        'color'     =>  $row->type === 1? 'orange' : 'green'
                    ];
                }
            }
            else
            {
                $listreplay = '';
            }

            $list[] = [
                'id'        =>  $row->id,
                'kode'      =>  $row->kode,
                'date'      =>  $Config->timeago($row->date),
                'user_name' =>  $row->user_name,
                'user_company'  =>  $row->company_name,
                'bidang'        =>  $row->bidang,
                'seksi'     =>  $row->seksi,
                'pelayanan'     =>  $row->pelayanan,
                'progress'      =>  (int)$row->progress,
                'detail'        =>  $row->detail,
                'type'          =>   ($row->noid === "" ? "" : $Config->typePegawai($row->type) . ':' . $row->noid),
                'replay'        =>  $listreplay
            ];
        }

        //
        $data = [
            'message'       =>  '',
            'response'      =>  [
                'list'          =>  $list,
                'paging'        =>  $paging,
                'total'         =>  $count,
                'countpage'     =>  ceil($count / $Config->table(['paging'=>$paging])['paging_item'] ),
                'status'            =>  'xxx'
            ]
        ];

        return response()->json($data, 200);
    }
}