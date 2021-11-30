<?php
namespace App\Http\Controllers\access;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\users as tblUsers;
use App\schools as tblSchools;
use App\Http\Controllers\config\index as Config;
use App\email_sender_tables as tblEmailSenderTables;
use App\user_registers as tblUserRegisters;
use Illuminate\Support\Facades\Hash;

class registers extends Controller
{
    // register dinas
    public function disdik(Request $request)
    {
        //include
        $Config = new Config;

        // request
        $email = trim($request->email);
        $noid = trim($request->no_id);
        $status = trim($request->status_selected);

        //CHECKING FILE INSERT
        if( $request->file('file') == null)
        {
            return $this->cekingRegister(['type'=>'file']);
        }

        $cekemail = $this->cekingRegister([
            'type'      =>  'email',
            'email'     =>  $email
        ]);

        if( $cekemail != '')
        {
            return response()->json([
                'message'=>$cekemail,
                'focus' =>  'email'
            ],401);
        }

        if( $status != '4')
        {
            $cekingnoid = $this->cekingRegister([
                'type'      =>  'noid',
                'noid'     =>  $noid
            ]);
    
            if( $cekingnoid != '')
            {
                return response()->json([
                    'message'=>$cekingnoid,
                    'focus' =>  'no_id'
                ],401);
            }
        }

        //INSERT DATA PROCESS
        $insert = $this->insertRegister($request);
        return response()->json($insert, 200);

    }

    //register sekolah
    public function school(Request $request)
    {
     
        $email = trim($request->email);
        $noid = trim($request->no_id);
        $status = trim($request->status_selected);

        //CHECKING FILE INSERT
        if( $request->file('file') == null)
        {
            return $this->cekingRegister(['type'=>'file']);
        }

        //CHECKING EMAIL
        $cekemail = $this->cekingRegister([
            'type'      =>  'email',
            'email'     =>  $email
        ]);

        if( $cekemail != '')
        {
            return response()->json([
                'message'=>$cekemail,
                'focus' =>  'email'
            ],401);
        }

        if( $status != '4')
        {
            $cekingnoid = $this->cekingRegister([
                'type'      =>  'noid',
                'noid'     =>  $noid
            ]);
    
            if( $cekingnoid != '')
            {
                return response()->json([
                    'message'=>$cekingnoid,
                    'focus' =>  'no_id'
                ],401);
            }
        }

        //INSERT DATA PROCESS
        $insert = $this->insertRegister($request);

        //update SET bidang
        $cekschool = tblSchools::where([
            'id'        =>  trim($request->school_id)
        ])->first();

        //
        $nobidang = '100000';
        if( $cekschool->jenjang == '1') // paud / tk
        {   
            $nobidang = $nobidang . '1';
        }
        else if($cekschool->jenjang == '2')
        {
            $nobidang = $nobidang . '2';
        }
        else if($cekschool->jenjang == '3') //sd
        {
            $nobidang = $nobidang . '2';
        }
        else if($cekschool->jenjang == '4' || $cekschool->jenjang == '6' ) //smp atau sma
        {
            $nobidang = $nobidang . '3';
        }
        else
        {
            $nobidang = $nobidang . '4'; //smk
        }

        //UPDATE USERS FIELD SET BIDANG
        $updateUsers = tblUsers::where([
            'id'        =>  $insert['response']['id']
        ])->update([
            'set_bidang'        =>  $nobidang
        ]);

        return response()->json($insert, 200);
    }

    // CHECKING REGISTER
    public function cekingRegister($request)
    {
        if( $request['type'] == 'email')
        {

            $cekemail = tblUsers::where([
                'email'     =>  $request['email']
            ])->count();
            
            if( $cekemail > 0)
            {
                return 'Alamat Email telah terdaftar';
            }

            return '';
        }

        if( $request['type'] == 'noid' )
        {
            $ceknoid = tblUsers::where([
                'noid'      =>  $request['noid']
            ])->count();


            if( $ceknoid > 0)
            {
                return 'NRK/NIKI telah digunakan sebelumnya';
            }

            return '';
        }

        if( $request['type'] == 'file')
        {
            return response()->json([
                'message'       =>  'Proses registasi tidak diijinkan, harap lampirkan surat keterangan dengan format .pdf',
                'focus'         =>  'email'
            ],401);
        }
        
    }

    // INSERT DATA PROCESS
    public function insertRegister($request)
    {
        //
        $Config = new Config;

        //INSERT TABLE USERS
        $adduser = new \App\Http\Controllers\models\users;
        $adduser = $adduser->addNew($request);
        

        //INSERT TABLE USER REGISTER
        $addregister = new \App\Http\Controllers\models\users;
        $addregister = $addregister->userRegister([
            'user_id'       =>  $adduser['id']
        ]);

        //INSERT TABLE UPLOAD FILE
        $modelUpload = new \App\Http\Controllers\models\upload;
        $modelUpload = $modelUpload->main([
            'file'      =>  $request->file('file'),
            'type'      =>  1, //registers,
            'folder'    =>  'upload/',
            'user_id'   =>  $adduser['id']
        ]);

        
        //notification
        $notification = new \App\Http\Controllers\notification\manage;
        $notification = $notification->newUsers(['user_id'=>$adduser['id']]);


        //create email sender table
        $newEmailSender = new \App\Http\Controllers\models\email;
        $newEmailSender = $newEmailSender->EmailRegisters(['user_id'=>$adduser['id']]);

        $data = [
            'message'   =>  '',
            'response'  =>  [
                'URL'           =>  $Config->apps()['URL'] . '/registers/success?token=' . $adduser['token'],
                'id'            =>  $adduser['id']
            ]
        ];

        return $data;
    }

    //SUCCESS REGISTERS
    public function success(Request $request)
    {
        $token = $request->header('key');

        $cek = tblUsers::from('users as u')
        ->select(
            'u.id','u.name','u.email','u.register_status'
        )
        ->leftJoin('user_registers as ur', function($join)
        {
            $join->on('ur.user_id', '=', 'u.id')
            ->where(['ur.user_id'=>1]);
        })
        ->where([
            'u.token'     =>  $token
        ])->first();


        if( $cek == null || $cek->register_status == 1)
        {
            $data = [
                'message'       =>  'Data tidak ditemukan'
            ];

            return response()->json($data, 404);
        }

        //ceking
        $counsending = tblEmailSenderTables::where([
            'user_id'       =>  $cek->id
        ])->count();

        //cek sending duration day
        $day = date('Y-m-d', time());
        $ceklimit = tblEmailSenderTables::where([
            ['user_id', '=', $cek->id],
            ['created_at', 'like', '%' . $day . '%']
        ])->count();

        if( $counsending === 1)
        {
            $ceksending = tblEmailSenderTables::where([
                'user_id'       =>  $cek->id
            ])->first();

            $sending = $ceksending->status === 1 ? 'true' : 'false';
        }
        else
        {
            $sending = 'false';
        }

        $data = [
            'message'       =>  '',
            'response'      =>  [
                'users'         =>  $cek,
                'token'         =>  $token,
                'config'        =>  [
                    'sending'       =>  $sending,
                    'token_sending'    =>  $sending === "false" ? "" : $ceksending->token,
                    'message'       =>  $ceklimit >= 3 ? 'Permintaan verifikasi akun melebihi batas permintaan maks 3x dalam 1 hari':'',
                    'ceklimit'      =>  $ceklimit >= 3 ? 'on' : 'off'
                ]
            ]
        ];

        return response()->json($data, 200);
    }

    //RESEND NOTIF VERIFY
    public function resendVerify(Request $request)
    {
        $Config = new Config;

        $token = trim($request->token);

        //
        $getUsers = tblUsers::where([
            'token'         =>  $token
        ])->first();

        //
        $day = date('Y-m-d', time());


        //
        $ceking = tblUserRegisters::where([
            ['user_id', '=', $getUsers->id],
            ['created_at', 'like', '%' . $day . '%' ]
        ])->count();

        //REJECT IF REQ OVER 3X 
        if( $ceking > 2)
        {
            $data = [
                'message'       =>  'Permintaan verifikasi akun melebihi batas permintaan maks 3x dalam 1 hari',
                'status'        =>  'on'
            ];

            return response()->json($data, 401);
        }

        //CHANGE STATUS 1 TO 0
        $updateUserReg = tblUserRegisters::where([
            'user_id'       =>  $getUsers->id,
            'status'        =>  1
        ])->update([
            'status'        =>  0
        ]);

        //TABLE EMAIL
        $updateUserReg = tblEmailSenderTables::where([
            'user_id'       =>  $getUsers->id,
            'status'        =>  1
        ])->update([
            'status'        =>  0
        ]);


        //ADD USER REGISTERS
        $newid = $Config->createnewid([
            'value'         =>  tblUserRegisters::count(),
            'length'        =>  15
        ]);

        $addnew             =   new tblUserRegisters;
        $addnew->id         =   $newid;
        $addnew->token      =   md5($newid);
        $addnew->user_id    =   $getUsers->id;
        $addnew->status     =   1;
        $addnew->save();


        //
        $newEmailSender = new \App\Http\Controllers\models\email;
        $newEmailSender = $newEmailSender->EmailRegisters(['user_id'=>$getUsers->id]);

        
        $data = [
            'message'       =>  '',
            'status'        =>  'off'
        ];

        return response()->json($data, 200);

    }

    //PAGE VERIFICATION
    public function pageVerification(Request $request)
    {
        $Config = new Config;

        $token = $request->token;

        //CEK TOKEN REGISTERS
        $cek = tblUserRegisters::from('user_registers as ur')
        ->select(
            'register_status'
        )
        ->leftJoin('users as u', function($join)
        {
            $join->on('u.id', '=', 'ur.user_id');
        })
        ->where([
            'ur.token'             =>  $token,
            'ur.status'            =>  1
        ])->first();


        if( $cek == null || $cek->register_status == 1)
        {
            return response()->json([
                'message'   =>  'Data tidak ditemukan'
            ],404);
        }


        $data = [
            'message'       =>  '',
            'response'      =>  [
                'token'         =>  $token,
                'ergstatus'     =>  $cek->register_status
            ]
        ];

        return response()->json($data, 200);

    }


    //VERIFICATION
    public function verification(Request $request)
    {
        $Config = new Config;

        $username = trim($request->username);
        $password = trim($request->password);
        $token = trim($request->token);

        //CEK
        $cek = tblUsers::where([
            'username'      =>  $username
        ])->count();

        if( $cek > 0)
        {
            $data = [
                'message'   =>  'Username telah digunakan sebelumnya',
                'focus'     =>  'username'
            ];

            return response()->json($data,403);
        }

        //update
        $getdata = tblUserRegisters::from('user_registers as ur')
        ->select(
            'u.id', 'u.email'
        )
        ->leftJoin('users as u', function($join)
        {
            $join->on('u.id','=','ur.user_id');
        })
        ->where([
            'ur.token'     =>  $token
        ])->first();

        //
        $upregister = tblUserRegisters::where([
            'token'     =>  $token,
            'status'        =>  1
        ])
        ->update([
            'status'        =>  0
        ]);


        $upuser = tblUsers::where([
            'id'                    =>  $getdata->id,
            'register_status'       =>  0
        ])
        ->update([
            'username'              =>  $username,
            'password'              =>  Hash::make($password),
            'register_status'       =>  1
        ]);

        //LOGIN
        $datalogin = [
            'email'     =>  $getdata->email,
            'password'  =>  $password
        ];

        $login = new \App\Http\Controllers\access\manage;
        $login = $login->truelogin($datalogin);


        return response()->json($login,200);

    }
}