<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tickets as tblTickets;
use App\ticket_replays as tblTicketReplays;
use App\Http\Controllers\config\index as Config;

class ticket extends Controller
{
    //
    public function main($request)
    {
        $Config = new Config;

        //create
        $count = tblTickets::count();
        $newid = $Config->createnewid([
            'value'     =>  $count,
            'length'    =>  15
        ]);

        //
        $token = md5($newid);

        //
        $addnew                 =   new tblTickets;
        $addnew->id             =   $newid;
        $addnew->type_ticket    =   0;
        $addnew->kode           =   date('ymd', time() ) . $Config->createuniq(['length'=>6,'value'=>$count]);
        $addnew->token          =   $token;
        $addnew->level          =   trim($request->level);
        $addnew->type           =   trim($request->bidang_selected);
        $addnew->subtype        =   trim($request->seksi_selected);
        $addnew->pelayanan      =   trim($request->pelayanan_selected);
        $addnew->detail         =   trim($request->text);
        $addnew->field          =   "";
        $addnew->url_file       =   "";
        $addnew->user_id        =   trim($request->user_id);
        $addnew->progress       =   0;
        $addnew->date           =   date('Y-m-d H:i:s', time());
        $addnew->status         =   1;
        $addnew->save();
    }

    public function visit($request)
    {
        $Config = new Config;

        //create
        $count = tblTickets::count();
        $newid = $Config->createnewid([
            'value'     =>  $count,
            'length'    =>  15
        ]);

        //
        $token = md5($newid);

        //
        $addnew                 =   new tblTickets;
        $addnew->id             =   $newid;
        $addnew->type_ticket    =   1;
        $addnew->kode           =   date('ymd', time() ) . $Config->createuniq(['length'=>6,'value'=>$count]);
        $addnew->token          =   $token;
        $addnew->level          =   trim($request["level"]);
        $addnew->type           =   trim($request["bidang"]);
        $addnew->subtype        =   trim($request["seksi"]);
        $addnew->pelayanan      =   trim($request["pelayanan"]);
        $addnew->detail         =   trim($request["detail"]);
        $addnew->field          =   json_encode($request["field"]);
        $addnew->url_file       =   "";
        $addnew->user_id        =   trim($request["user_id"]);
        $addnew->progress       =   0;
        $addnew->date           =   date('Y-m-d H:i:s', time());
        $addnew->status         =   1;
        $addnew->save();
    }


    public function replay($request)
    {
        $Config = new Config;

        //create
        $count = tblTicketReplays::count();
        $newid = $Config->createnewid([
            'value'     =>  $count,
            'length'    =>  15
        ]);

        //
        $token = md5($newid);

        //
        $addnew                 =   new tblTicketReplays;
        $addnew->id             =   $newid;
        $addnew->type           =   $request['type'];
        $addnew->token          =   $token;
        $addnew->ticket_id      =   $request['ticket_id'];
        $addnew->text           =   $request['text'];
        $addnew->url_file       =   $request['url_file'];
        $addnew->user_id        =   $request['user_id'];
        $addnew->date           =   date('Y-m-d H:i:s', time());
        $addnew->status         =   1;
        $addnew->save();

        return $data = [
            'id'        =>  $newid
        ];

    }
}