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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

// $router->group(['middleware'=>'cekingrequest'],function($router)
// {

//     $router->get('/', function()
//     {
//         return 'ok';
//     });

// });


//
$router->group(['prefix' => 'api',  'middleware' => 'cekrequest'],function($router){
    $router->post('/login', 'access\manage@login');
});


//
$router->group(['prefix'=>'api', 'middleware'=>['cekrequest','auth']],function($router)
{
    $router->get('/profile', 'access\manage@profile');
    $router->post('/logout', 'access\manage@logout');
});


//testing
// $router->post('/testing/upload', 'testing\upload\index@image');

$router->get('/', 'front\index@main');

//any get url
$router->get('/{any}', 'error\page\index@get');
$router->post('/{any}', 'error\page\index@post');

// Route::get('/{any}', 'errors\page\index@main')->where('any', '.*');
// Route::post('/{any}', 'errors\page\index@main')->where('any', '.*');
