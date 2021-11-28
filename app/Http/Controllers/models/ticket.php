<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tickets as tblTickets;
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
}