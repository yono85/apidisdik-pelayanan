<?php

/** @var \Laravel\Lumen\Routing\Router $router */
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//
$router->group(['prefix' => 'api',  'middleware' =>'cekrequest'],function($router){
    $router->post('/login', 'access\manage@login');
    $router->post('/forgetpassword', 'access\manage@forgetpassword');

    //registers
    $router->post('/registers/adm-disdik', 'access\registers@disdik');
    $router->post('/registers/adm-sch', 'access\registers@school');

    // data
    $router->get('/data/getbidang', 'data\user\component@bidang');
    $router->get('/data/getpelayanan', 'data\user\component@pelayanan');
    $router->get('/data/ticket/getbidang', 'data\user\component@bidangByUser');
    $router->get('/data/subpelayanan', 'data\user\component@subPelayanan');

});

$router->group(['prefix'=>'api', 'middleware'=>['cekrequest','cekKeyAccount']],function($router)
{

    // REGISTERS
    $router->get('/registers/success', 'access\registers@success');
    $router->post('/registers/resend-verify', 'access\registers@resendVerify');

    //VERIFICATION
    $router->get('/registers/verification', 'access\registers@pageVerification');
    $router->post('/account/verification', 'access\registers@verification');

    // CHANGE PASSWORD
    $router->get('/account/checkresetpassword', 'account\index@checkresetpassword');
    $router->post('/account/changedpassword', 'account\index@resetPassword');


    //SEND EMAIL
    // $router->post('/send/email/verify', 'email\manage@registers');
    // $router->post('/send/emailid', 'email\manage@main');


    //SCHOOLS
    $router->get('/schools/list', 'schools\index@lists');


    // TICKET
    $router->post('/ticket/create', 'ticket\manage@create');
    $router->get('/ticket/pengajuan/table', 'ticket\table@pengajuan');
    $router->get('/ticket/show', 'ticket\manage@show');
    $router->post('/ticket/progress', 'ticket\manage@progress');
    $router->post('/ticket/replay', 'ticket\manage@replay');

    
    // MENU
    $router->get('/menu/aside', 'menu\aside@main');
    // $router->post('/registers/verify-email', 'access\manage@registerVerify');
});


// FOR SEND EMAIL
$router->group(['prefix'=>'api/send/email', 'middleware'=>['cekrequest','cekKeyAccount']],function($router)
{
    $router->post('/', 'email\manage@main');
});


//
$router->group(['prefix'=>'api', 'middleware'=>['cekrequest','auth']],function($router)
{
    $router->get('/profile', 'access\manage@profile');
    $router->post('/logout', 'access\manage@logout');
});


//
$router->group(['prefix' => '/inject',  'middleware' => ['cekrequest','cekKeyAccount']],function($router){
    // $router->group(['prefix' => 'api',  'middleware' => ['cekrequest','cekKeyAccount']], function($router){
    $router->post('/resetpassword', 'data\inject@resetpassword');
});



// TESTING
$router->group(['prefix'=>'testing'],function($router)
{
    $router->get('/email', 'models\email@dataRegisters');
    $router->post('/upload', 'upload\index@test');
    $router->get('/view-table', 'models\email@test');
});
