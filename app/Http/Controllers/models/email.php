<?php
namespace App\Http\Controllers\models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\email_sender_tables as tblEmailSenderTables;
use App\users as tblUsers;
use App\email_senders as tblEmailSenders;
use App\email_templates as tblEmailTemplates;
use App\Http\Controllers\config\index as Config;
use App\user_resetpasswords as tblUserResetPasswords;

class email extends Controller
{
    //
    public function main($request)
    {
        //
        $Config = new Config;

        //data
        $data = $request;

        //
        $newid = $Config->createnewid([
            'value'         =>  tblEmailSenderTables::count(),
            'length'        =>  15
        ]);

        $token = md5($newid);
        
        $addnew             =   new tblEmailSenderTables;
        $addnew->id         =   $newid;
        $addnew->token      =   $token;
        $addnew->sender_id  =   $data['sender']['id'];
        $addnew->template   =   json_encode($data['template']);
        $addnew->body       =   $data['body'];
        $addnew->from       =   json_encode($data['from']);
        $addnew->to         =   json_encode($data['to']);
        $addnew->user_id    =   $data['users']['id'];
        $addnew->sending    =   0;
        $addnew->sending_info = '';
        $addnew->status     =   1;
        $addnew->save();

        if($addnew)
        {
            return $data = [
                'message'       =>  'success',
                'token'         =>  $token,
                'id'            =>  $newid
            ];
        }
        else
        {
            return $data = [
                'message'       =>  'error'
            ];
        }
    }


    public function dataSender()
    {
        $Config = new Config;

        //email senders
        $getsender = tblEmailSenders::where([
            'id'            =>  $Config->configApps()['email']['sender_id']
        ])->first();

        $sender =   [
            'id'        =>  $getsender->id,
            'name'      =>  $getsender->label,
            'email'     =>  $getsender->email
        ];

        return $sender;
    }

    //EMAIL VERIFICATION
    public function EmailRegisters($request)
    {
        //
        $Config = new Config;

        //
        $getdata = tblUsers::from('users as u')
        ->select(
            'u.id','u.email','u.name',
            'ur.token'
        )
        ->where([
            'u.id'        => $request['user_id']
        ])
        ->leftJoin('user_registers as ur', function($join)
        {
            $join->on('ur.user_id', '=', 'u.id')
            ->where([
                'ur.status' =>  1
            ]);
        })
        ->first();

        $users  =   [
            'id'        =>  $getdata->id,
            'name'      =>  $getdata->name,
            'email'     =>  $getdata->email
        ];

        //get template
        $gettemp = tblEmailTemplates::where([
            'type'        =>  '1', //access
            'sub_type'       =>  '1', //registrasi
            'status'    =>  1
        ])->first();

        //content
        $content = $gettemp->content;
        $content = str_replace('{url_home}', $Config->apps()['URL'], $content);
        $content = str_replace('{url_logo}', $Config->apps()['LOGO'], $content);
        $content = str_replace('{apps_name}', $Config->apps()['NAME'], $content);
        $content = str_replace('{name}', strtoupper($users['name']), $content);
        $content = str_replace('{url_help}', $Config->apps()['URL'].'/help', $content);
        $content = str_replace('{url}', $Config->apps()['URL'] . '/registers/verification?token=' . $getdata->token, $content); //url

        //
        $sender = $this->dataSender();

        $data = [
            'sender'    =>  [
                'id'        =>  $sender['id']
            ],
            'to'        =>  [
                'name'          =>  $users['name'],
                'email'         =>  $users['email']
            ],
            'template'      =>  [
                'title'         =>  $gettemp->title . ' ' . $Config->apps()['NAME'],
                'subject'       =>  $gettemp->subject . ' ' . $Config->apps()['NAME']
            ],
            'body'          =>  $content,
            'from'      =>  [
                'name'      =>  $sender['name'],
                'email'     =>  $sender['email']
            ],
            'users'      =>  [
                'id'        =>  $users['id']
            ]
        ];


        return response()->json($data,200);
        // $insertEmail = $this->main($data);

        // return $insertEmail;

    }

    //EMAIL FORGET PASSWORD
    public function emailForgetPassword($request)
    {
        //
        $Config = new Config;

        //
        $getdata = tblUsers::from('users as u')
        ->select(
            'u.id','u.email','u.name',
            'ur.token'
        )
        ->where([
            'u.id'        => $request['user_id']
        ])
        ->leftJoin('user_resetpasswords as ur', function($join)
        {
            $join->on('ur.user_id', '=', 'u.id')
            ->where([
                'ur.status' =>  1
            ]);
        })
        ->first();

        $users  =   [
            'id'        =>  $getdata->id,
            'name'      =>  $getdata->name,
            'email'     =>  $getdata->email
        ];

        //get template
        $gettemp = tblEmailTemplates::where([
            'type'        =>  '1', //access
            'sub_type'       =>  '3', //registrasi
            'status'    =>  1
        ])->first();

        //content
        $content = $gettemp->content;
        $content = str_replace('{url_home}', $Config->apps()['URL'], $content);
        $content = str_replace('{url_logo}', $Config->apps()['LOGO'], $content);
        $content = str_replace('{apps_name}', $Config->apps()['NAME'], $content);
        $content = str_replace('{name}', strtoupper($users['name']), $content);
        $content = str_replace('{url_help}', $Config->apps()['URL'].'/help', $content);
        $content = str_replace('{url}', $Config->apps()['URL'] . '/resetpassword?token=' . $getdata->token, $content); //url

        //
        $sender = $this->dataSender();

        $data = [
            'sender'    =>  [
                'id'        =>  $sender['id']
            ],
            'to'        =>  [
                'name'          =>  $users['name'],
                'email'         =>  $users['email']
            ],
            'template'      =>  [
                'title'         =>  $gettemp->title . ' ' . $Config->apps()['NAME'],
                'subject'       =>  $gettemp->subject . ' ' . $Config->apps()['NAME']
            ],
            'body'          =>  $content,
            'from'      =>  [
                'name'      =>  $sender['name'],
                'email'     =>  $sender['email']
            ],
            'users'      =>  [
                'id'        =>  $users['id']
            ]
        ];


        $insertEmail = $this->main($data);

        return $insertEmail;

    }

    public function test(Request $request)
    {
        $id = trim($request->id);

        $get = [
            'user_id'   =>  $id
        ];

        return $this->EmailRegisters($get);
    }
}