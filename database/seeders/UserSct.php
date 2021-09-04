<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Controllers\config\index as Config;
use App\users as tblUsers;
use Illuminate\Support\Facades\Hash;
use DB;

class UserSct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //cek
        $cek = tblUsers::where([
            'email'     =>  'yono@admin.com'
        ])->count();

        if( $cek == 0)
        {
            $Config = new Config;
            $newid = $Config->createnewid([
                'value'     =>  DB::table('users')->count(),
                'length'    =>  15
            ]);
    
            //
            $add            =   new tblUsers;
            $add->id        =   $newid;
            $add->token     =   md5('bukaduonks');
            $add->name      =   'Yono Cahyono';
            $add->gender    =   1;
            $add->birth     =   '1985-11-05';
            $add->email     =   'yono@admin.com';
            $add->password  =   Hash::make('buka85');
            $add->level     =   9;
            $add->sublevel  =   0;
            $add->company_id    =   0;
            $add->register_status   =   1;
            $add->status            =   1;
            $add->save();
        }
    }
}
