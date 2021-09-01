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
});


//
$router->group(['prefix'=>'api', 'middleware'=>['cekrequest','auth']],function($router)
{
    $router->get('/profile', 'access\manage@profile');
    $router->post('/logout', 'access\manage@logout');
});


