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
        ->select(
            't.id', 't.type_ticket', 't.kode', 't.date','t.progress', 't.detail', 't.field',
            'u.name as user_name', 'u.type', 'u.noid', 't.token',
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
        });

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
        $user_id = trim($request->user_id);
        
        //
        $getdata = $this->main();
        $getdata = $getdata
        ->where([
            ['t.type_ticket', '=', 0],
            ['t.user_id', '=', $user_id],
            ['t.kode', 'like', '%' . $src . '%'],
            ['t.status', '=', 1]
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
                $listreplay = [];
                foreach($getreplay as $rowx)
                {
                    $listreplay[] = [
                        'id'        =>  $rowx->id,
                        'type'      =>  $rowx->type === 1 ? 'progress' : 'done',
                        'date'      =>  $Config->timeago($rowx->date),
                        'user'      =>  $rowx->user_name,
                        'color'     =>  $rowx->type === 1 ? 'orange' : 'green'
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
                'replay'        =>  $listreplay,
                'token'         =>  $row->token
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

    // PERMINTAAN
    public function permintaan(Request $request)
    {
        $Config = new Config;

        $src = trim($request->search);
        $paging = trim($request->paging);
        $sortname = trim($request->sort_name);
        $status = trim($request->selected_status);
        $user_id = trim($request->user_id);
        $ulevel = trim($request->level);
        $seksi = trim($request->seksi);
        
        //
        $getdata = $this->main();
        $getdata = $getdata
        ->where([
            ['t.kode', 'like', '%' . $src . '%'],
            ['t.status', '=', 1]
        ]);
        if( $status != "-1")
        {
            $getdata = $getdata->where([
                't.progress'        =>  $status
            ]);
        }
        if( $ulevel != '9')
        {
            $getdata = $getdata->where([
                't.subtype'     =>  $seksi
            ]);
        }

        //count
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
                $listreplay = [];
                foreach($getreplay as $rowx)
                {
                    $listreplay[] = [
                        'id'        =>  $rowx->id,
                        'type'      =>  $rowx->type === 1 ? 'progress' : 'done',
                        'date'      =>  $Config->timeago($rowx->date),
                        'user'      =>  $rowx->user_name,
                        'color'     =>  $rowx->type === 1 ? 'orange' : 'green'
                    ];
                }
            }
            else
            {
                $listreplay = '';
            }

            if($row->type_ticket == "1")
            {
                $username = json_decode($row->field)->visit->name;
                $company = json_decode($row->field)->school->name;
            }
            else
            {
                $username = $row->user_name;
                $company = $row->company_name;
            }
            $list[] = [
                'id'        =>  $row->id,
                'visit'     =>  $row->type_ticket === 1 ? "true" : "false",
                'kode'      =>  $row->kode,
                'date'      =>  $Config->timeago($row->date),
                'user_name' =>  $username,
                'user_company'  =>  $company,
                'bidang'        =>  $row->bidang,
                'seksi'     =>  $row->seksi,
                'pelayanan'     =>  $row->pelayanan,
                'progress'      =>  (int)$row->progress,
                'detail'        =>  $row->detail,
                'type'          =>  $row->type_ticket === 1 ? "" : ($row->noid === "" ? "" : $Config->typePegawai($row->type) . ':' . $row->noid),
                'replay'        =>  $listreplay,
                'admin'         =>  $row->type_ticket === 1 ? $row->user_name : ""
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

    // VISIT
    public function visit(Request $request)
    {
        $Config = new Config;

        $src = trim($request->search);
        $paging = trim($request->paging);
        $sortname = trim($request->sort_name);
        $status = trim($request->selected_status);
        $user_id = trim($request->user_id);
        $ulevel = trim($request->level);
        $seksi = trim($request->seksi);
        
        //
        $getdata = $this->main();
        $getdata = $getdata
        ->where([
            ['t.type_ticket', '=', 1],
            ['t.kode', 'like', '%' . $src . '%'],
            ['t.status', '=', 1]
        ]);
        if( $status != "-1")
        {
            $getdata = $getdata->where([
                't.progress'        =>  $status
            ]);
        }
        if( $ulevel != '9')
        {
            $getdata = $getdata->where([
                't.user_id'     =>  $user_id
            ]);
        }

        //count
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
                $listreplay = [];
                foreach($getreplay as $rowx)
                {
                    $listreplay[] = [
                        'id'        =>  $rowx->id,
                        'type'      =>  $rowx->type === 1 ? 'progress' : 'done',
                        'date'      =>  $Config->timeago($rowx->date),
                        'user'      =>  $rowx->user_name,
                        'color'     =>  $rowx->type === 1 ? 'orange' : 'green'
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
                'type'          =>  "",
                "visit"         =>  json_decode($row->field)->visit,
                "school"         =>  json_decode($row->field)->school,
                'replay'        =>  $listreplay,
                "token"         =>  $row->token
            ];
        }

        $calcdata = $getdata->get();
        $sum_waiting = [];
        $sum_progress = [];
        $sum_done = [];
        foreach($calcdata as $row)
        {
            if($row->progress == 2)
            {
                $sum_done[] = $row->progress;
            }

            if($row->progress == 1)
            {
                $sum_hold[] = $row->progress;
            }

            if($row->progress == 0)
            {
                $sum_waiting[] = $row->progress;
            }

        }
        //
        $data = [
            'message'       =>  '',
            'response'      =>  [
                'list'          =>  $list,
                'paging'        =>  $paging,
                'total'         =>  $count,
                'countpage'     =>  ceil($count / $Config->table(['paging'=>$paging])['paging_item'] ),
                'result'        =>  [
                    "done"          =>  count($sum_done),
                    "progress"      =>  count($sum_progress),
                    "waiting"       =>  count($sum_waiting)
                ],
                'status'            =>  'xxx'
            ]
        ];

        return response()->json($data, 200);
    }

    public function data(Request $request)
    {
        $Config = new Config;

        $src = trim($request->search);
        $paging = trim($request->paging);
        $sortname = trim($request->sort_name);
        $status = trim($request->selected_status);
        $user_id = trim($request->user_id);
        
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
                        'color'     =>  $rowx->type === 1 ? 'orange' : 'green'
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