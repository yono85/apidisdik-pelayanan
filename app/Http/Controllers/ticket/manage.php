<?php
namespace App\Http\Controllers\ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\tickets as tblTickets;
use App\ticket_replays as tblTicketReplays;
use DB;
class manage extends Controller
{
    //
    public function create(Request $request)
    {
        $Config = new Config;

        //
        if( trim($request->type) == 'new')
        {
            $req = $this->new($request);
        }
        else
        {
            $req = $this->edit($request);
        }

        return $req;
    }

    public function new($request)
    {

        // CREATE NEW
        $create = new \App\Http\Controllers\models\ticket;
        $create = $create->main($request);

        //
        $data = [
            'message'       =>  'Tiket berhasil dibuat'
        ];

        return response()->json($data,200);
    }

    public function edit($request)
    {
        $data = [
            'message'       =>  'Tiket berhasil disunting'
        ];

        return response()->json($data,200);
    }

    //show
    public function show(Request $request)
    {
        $Config = new Config;

        //
        $getdata = tblTickets::from("tickets as t")
        ->select(
            't.id', 't.progress', 't.kode', 't.date', 't.detail','t.token',
            'u.id as user_id','u.name as user_name', 'u.type as utype', 'u.noid',
            'p.name as pelayanan',
            'cp.name as company_name'
        )
        ->leftJoin('users as u', function($join)
        {
            $join->on('u.id', '=', 't.user_id');
        })
        ->leftJoin('pelayanans as p', function($join)
        {
            $join->on('p.id', '=', 't.pelayanan');
        })
        ->leftJoin('schools as cp', function($join)
        {
            $join->on('cp.id', '=', 'u.company_id');
        })
        ->where([
            't.id'        =>  trim($request->id)
        ])->first();


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
            'tr.ticket_id'  =>  $getdata->id,
            'tr.status'     =>  1
        ])
        ->get();

        if( count($getreplay) > 0)
            {
                foreach($getreplay as $rowx)
                {
                    $replay[] = [
                        'id'        =>  $rowx->id,
                        'type'      =>  $rowx->type === 1 ? 'progress' : 'done',
                        'date'      =>  $Config->timeago($rowx->date),
                        'user'      =>  $rowx->user_name,
                        'color'     =>  $rowx->type === 1? 'orange' : 'green'
                    ];
                }
            }
            else
            {
                $replay = '';
            }


        //
        $data = [
            'message'       =>  '',
            'response'       =>  [
                'id'            =>  $getdata->id,
                'status'        =>  $getdata->progress,
                'date'          =>  $Config->timeago($getdata->date),
                'user_name'     =>  $getdata->user_name,
                'pelayanan'     =>  $getdata->pelayanan,
                'detail'        =>  $getdata->detail,
                'user_id'       =>  $getdata->user_id,
                'user_type'          =>  ($getdata->noid === "" ? "" : $Config->typePegawai($getdata->utype) . ':' . $getdata->noid),
                'user_company'  =>  $getdata->company_name,
                'replay'        =>  $replay
            ]
        ];


        return response()->json($data, 200);
    }


    // PROGRESS
    public function progress(Request $request)
    {
        $Config = new Config;


        $send = [
            'type'          =>  1,
            'user_id'       =>  trim($request->user_id),
            'ticket_id'     =>  trim($request->ticket_id),
            'text'          =>  '',
            'url_file'      =>  ''
        ];

        $add = new \App\Http\Controllers\models\ticket;
        $add = $add->replay($send);

        //notif

        //update
        $update = tblTickets::where([
            'id'        =>  trim($request->ticket_id)
        ])
        ->update([
            'progress'      =>  1
        ]);

        $data = [
            'message'       =>  ''
        ];

        return response()->json($data, 200);
    }


    public function replay(Request $request)
    {

        $send = [
            'type'          =>  2,
            'user_id'       =>  trim($request->user_id),
            'ticket_id'     =>  trim($request->ticket_id),
            'text'          =>  trim($request->text),
            'url_file'      =>  ''
        ];

        $add = new \App\Http\Controllers\models\ticket;
        $add = $add->replay($send);

        //notif

        //update
        $update = tblTickets::where([
            'id'        =>  trim($request->ticket_id)
        ])
        ->update([
            'progress'      =>  2
        ]);

        $data = [
            'message'       =>  ''
        ];

        return response()->json($data, 200);
    }
}