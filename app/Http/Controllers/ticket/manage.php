<?php
namespace App\Http\Controllers\ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\tickets as tblTickets;
use App\ticket_replays as tblTicketReplays;
use App\schools as tblSchools;
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

    public function createvisit(Request $request)
    {

        $getschool = tblSchools::where([
            "id"            =>  trim($request->school_id)
        ])->first();

        //
        $visit = [
            "name"          =>  trim($request->visit_name),
            "nik"           =>  trim($request->visit_nik),
            "phone"         =>  trim($request->visit_phone),
            "address"       =>  trim($request->visit_address)           
        ];

        //
        $school = [
            "name"          =>  $getschool->name,
            "npsn"          =>  $getschool->npsn,
            "nosurat"       =>  trim($request->nosurat),
            "tglsurat"      =>  trim($request->tglsurat),
            "isisurat"      =>  trim($request->isisurat)
        ];

        $field = [
            "visit"     =>  $visit,
            "school"    =>  $school
        ];

        $datanew = [
            "level"             =>  trim($request->level),
            "bidang"            =>  trim($request->bidang_selected),
            "seksi"              => trim($request->seksi_selected),
            "pelayanan"         =>  trim($request->pelayanan_selected),
            "detail"            =>  trim($request->text),
            "user_id"           =>  trim($request->user_id),
            "field"             =>  $field
        ];

        $create = new \App\Http\Controllers\models\ticket;
        $create = $create->visit($datanew);

        //
        $data = [
            'message'       =>  'Tiket berhasil dibuat',
            "response"      =>  $datanew
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
            't.id', 't.type_ticket', 't.field', 't.progress', 't.kode', 't.date', 't.detail','t.token',
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


        if( $getdata->type_ticket == "1")
        {
            $username   =   json_decode($getdata->field)->visit->name;
            $company    =   json_decode($getdata->field)->school->name;
        }
        else
        {
            $username = $getdata->user_name;
            $company = $getdata->company_name;
        }
        //
        $data = [
            'message'       =>  '',
            'response'       =>  [
                'id'            =>  $getdata->id,
                'status'        =>  $getdata->progress,
                'date'          =>  $Config->timeago($getdata->date),
                'user_name'     =>  $username,
                'pelayanan'     =>  $getdata->pelayanan,
                'detail'        =>  $getdata->detail,
                'user_id'       =>  $getdata->user_id,
                'user_type'     =>  $getdata->type_ticket === 1 ? "" : ($getdata->noid === "" ? "" : $Config->typePegawai($getdata->utype) . ':' . $getdata->noid),
                'user_company'  =>  $company,
                'replay'        =>  $this->showreply($getdata->id)
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
            'message'       =>  '',
            'list'          =>  $this->showreply($request->ticket_id)
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

        //upload file
        if( $request->file('file') != "")
        {
            $modelUpload = new \App\Http\Controllers\models\upload;
            $modelUpload = $modelUpload->main([
                'file'      =>  $request->file('file'),
                'type'      =>  2, //replay,
                'folder'    =>  'upload/replay/',
                'user_id'   =>  $request->user_id
            ]);

            $updatereply = tblTicketReplays::where([
                'id'        =>  $add['id']
            ])
            ->update([
                'url_file'      =>  $modelUpload['url']
            ]);

        }

        //notif

        //update
        $update = tblTickets::where([
            'id'        =>  trim($request->ticket_id)
        ])
        ->update([
            'progress'      =>  2
        ]);

        $data = [
            'message'       =>  '',
            'list'          =>  $this->showreply($request->ticket_id)
        ];

        return response()->json($data, 200);
    }

    //
    public function showreply($request)
    {
        $Config = new Config;

        //
        $getreplay = DB::table('vw_ticket_replays as tr')
        ->select(
            'tr.id', 'tr.type', 'tr.date', 'tr.text', 'tr.url_file',
            'u.name as user_name'
        )
        ->leftJoin('users as u', function($join)
        {
            $join->on('u.id', '=', 'tr.user_id');
        })
        ->where([
            'tr.ticket_id'  =>  $request,
            'tr.status'     =>  1
        ])
        ->get();

        if( count($getreplay) > 0)
        {
            foreach($getreplay as $row)
            {
                $replay[] = [
                    'id'        =>  $row->id,
                    'type'      =>  $row->type === 1 ? 'progress' : 'done',
                    'date'      =>  $Config->timeago($row->date),
                    'user'      =>  $row->user_name,
                    'color'     =>  $row->type === 1? 'orange' : 'green',
                    'detail'    =>  $row->text,
                    'url'       =>  $row->url_file
                ];
            }
        }
        else
        {
            $replay = '';
        }

        return $replay;
    }


    //PRINT
    public function print(Request $request)
    {
        $Config = new Config;

        $token = trim($request->token);

        $getdata = tblTickets::from("tickets as t")
        ->select(
            "t.kode","t.date", "t.detail", "t.field",
            "us.name as bidang",
            "sp.name as seksi",
            "u.name as teller_name"
        )
        ->leftJoin("user_sublevels as us", function($join)
        {
            $join->on("us.id", "=", "t.type");
        })
        ->leftJoin("sub_pelayanans as sp", function($join)
        {
            $join->on("sp.id", "=", "t.subtype");
        })
        ->leftJoin("users as u", function($join)
        {
            $join->on("u.id", "=", "t.user_id");
        })
        ->where([
            "t.token"          =>  $token
        ])->first();


        if( $getdata == null)
        {
            $data = [
                "message"       =>  "Data tidak ditemukan"
            ];
    
            return response()->json($data,404);

        }

        //
        $data = [
            "message"       =>  "",
            "response"      =>  [
                "code"          =>  $getdata->kode,
                "detail"        =>  $getdata->detail,
                "bidang"        =>  $getdata->bidang,
                "seksi"         =>  $getdata->seksi,
                "date"          =>  date("d/m/Y H:i", strtotime($getdata->date)),
                "teller"        =>  $getdata->teller_name
            ]
        ];

        return response()->json($data,200);
    }
}