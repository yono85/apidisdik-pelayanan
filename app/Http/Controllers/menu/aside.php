<?php
namespace App\Http\Controllers\menu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class aside extends Controller
{
    //
    public function main(Request $request)
    {
        // $data = [
        //     'level'         =>  $request->level,
        //     'sublevel'      =>  $request->sublevel,
        // ];

        // $create = $this->createaside($data);

        // return response()->json($create, 200);


        $data = $this->level();
        $data = [
            'menu'  =>  $data[$request->level]['menu'],
            'submenu'   =>  $data[$request->level]['submenu']
        ];

        $temp = $this->tempaside($data);
        return response()->json($temp,200);
    }

    //
    public function createaside($request)
    {
        $level = $request['level'];
        $sublevel = $request['sublevel'];


        $asidelevel = (int)$level === '9' ? $this->admin() : $this->user();

        // return $asidelevel;
        $data = [
            'menu'      =>  $asidelevel[$sublevel]['menu'],
            'submenu'   =>  $asidelevel[$sublevel]['submenu']
        ];

        //getaside template
        $asidetemp = $this->tempaside($data);

        return $asidetemp;
    }


    public function user()
    {
        $data = [
            '1'         =>  [ //administator
                'menu'          =>  'dashboard,tiket,report,config',
                'submenu'       =>  [
                    'tiket'     =>  'pengajuan,riwayat',
                    'config'    =>  'account'
                ]
            ]
        ];

        return $data;
    }

    public function admin()
    {
        $data = [
            '0'         =>  [ //administator
                'menu'          =>  'dashboard,tiket,report,config',
                'submenu'       =>  [
                    'tiket'     =>  'pengajuan,riwayat',
                    'config'    =>  'account'
                ]
            ]
        ];

        return $data;
    }


    public function level()
    {
        
        // level 9 admin, level 1 admin dinas, level 2 sudin, 3 sekolah, 0 user
        $data = [
            '1'         =>  [ 
                'menu'          =>  'dashboard,tiket,report,signout',
                'submenu'       =>  [
                    'tiket'     =>  'permintaan',
                    'config'    =>  'account'
                ]
            ],
            '3'         =>  [
                'menu'          =>  'dashboard,tiket,report,signout',
                'submenu'       =>  [
                    'tiket'     =>  'pengajuan',
                    'config'    =>  'account'
                ]
            ],
            '9'         =>  [
                'menu'          =>  'dashboard,tiket,report,users,config,signout',
                'submenu'       =>  [
                    'tiket'     =>  'pengajuan,permintaan',
                    'config'    =>  'account'
                ]
            ]
        ];

        return $data;

    }

    public function tempaside($request)
    {
        $menu = explode(",", $request['menu']);
        $submenu = $request['submenu'];

        //
        $cmenu = $this->menu();
        $csubmenu = $this->submenu();

        //
        foreach($menu as $m)
        {
            $vmenu = $cmenu[$m];

            if( $vmenu['type'] != '')
            {
                $vsubmenu = explode(",", $submenu[$m]);
                $child = [];

                foreach($vsubmenu as $s)
                {
                    
                    $child[] = [
                        'title'         =>  $csubmenu[$s]['title'],
                        'url'           =>  $csubmenu[$s]['url'],
                    ];
                }
            }
            else
            {
                $child = '';
            }

            //
            $list[] = [
                'title'     =>  $vmenu['title'],
                'type'      =>  $vmenu['type'],
                'url'       =>  $vmenu['url'],
                'arrow'     =>  $vmenu['arrow'],
                'icon'      =>  $vmenu['icon'],
                'class'     =>  $vmenu['class'],
                'child'     =>  $child
            ];
        }

        //
        return $list;
    }

    //menu
    public function menu()
    {

        $data = [
                'dashboard'     =>  [
                    'title'         =>  'Dashboard',
                    'icon'          =>  'icon fa flaticon2-line-chart',
                    'type'          =>  '',
                    'arrow'         =>  '',
                    'url'           =>  '/dashboard',
                    'child'         =>  '',
                    'class'         =>  ''
                ],//
                'tiket'     =>  [
                    'title'         =>  'Tiket',
                    'icon'          =>  'icon fas fa-ticket-alt',
                    'type'          =>  'collaps',
                    'arrow'         =>  'icon icon-keyboard_arrow_down arrow-icon',
                    'child'         =>  '',
                    'url'           =>  '',
                    'class'         =>  ''
                ],
                'report'     =>  [
                    'title'         =>  'Laporan',
                    'icon'          =>  'icon fa flaticon2-line-chart',
                    'type'          =>  '',
                    'arrow'         =>  '',
                    'url'           =>  '#',
                    'child'         =>  '',
                    'class'         =>  ''
                ],
                'users'     =>  [
                    'title'         =>  'Pengguna',
                    'icon'          =>  'icon fas fa-user-cog',
                    'type'          =>  '',
                    'arrow'         =>  '',
                    'url'           =>  '/dashboard/pengguna',
                    'child'         =>  '',
                    'class'         =>  ''
                ],
                'config'     =>  [
                    'title'         =>  'Pengaturan',
                    'icon'          =>  'icon fa flaticon-cogwheel',
                    'type'          =>  'collaps',
                    'arrow'         =>  'icon icon-keyboard_arrow_down arrow-icon',
                    'child'         =>  '',
                    'url'           =>  '',
                    'class'         =>  ''
                ],
                'signout'     =>  [
                    'title'         =>  'Keluar',
                    'icon'          =>  'icon sli_icon-logout',
                    'type'          =>  '',
                    'arrow'         =>  '',
                    'url'           =>  '#',
                    'child'         =>  '',
                    'class'         =>  'account-signout'
                ],
        ];


        return $data;
        

    }

    //sub menu
    public function submenu()
    {

        $data = [
            'pengajuan'         =>  [ //marketing
                'title'             =>  'Pengajuan',
                'url'               =>  '/dashboard/ticket/pengajuan'
            ],
            'permintaan'         =>  [ //marketing
                'title'             =>  'Permintaan',
                'url'               =>  '/dashboard/ticket/permintaan'
            ],
            'riwayat'         =>  [ //marketing
                'title'             =>  'History',
                'url'               =>  '#'
            ],
            'account'        =>  [ //admin produsen, distributor
                'title'             =>  'Account',
                'url'               =>  '/dashboard/config/account'
            ]
        ];


        return $data;
    }
}