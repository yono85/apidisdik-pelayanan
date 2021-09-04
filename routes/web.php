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
$router->group(['prefix' => 'api',  'middleware' => 'cekrequest'],function($router){
    $router->post('/login', 'access\manage@login');
    $router->post('/forgetpassword', 'access\manage@forgetpassword');
});


//
$router->group(['prefix'=>'api', 'middleware'=>['cekrequest','auth']],function($router)
{
    $router->get('/profile', 'access\manage@profile');
    $router->post('/logout', 'access\manage@logout');
});


$router->group(['prefix' => '/inject',  'middleware' => ['cekrequest','cekKeyAccount']],function($router){
    // $router->group(['prefix' => 'api',  'middleware' => ['cekrequest','cekKeyAccount']], function($router){
    $router->post('/resetpassword', 'data\inject@resetpassword');
});