<?php
namespace App\Http\Controllers\ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\config\index as Config;
use App\tickets as tblTickets;

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
}