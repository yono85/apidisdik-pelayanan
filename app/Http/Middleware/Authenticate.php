<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use DB;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        if ($this->auth->guard($guard)->guest()) //jika token tidak valid / expire
        {
            $data = [
                'message'       =>  'Token tidak valid'
            ];
            return response($data, 401);
        }
        else
        {

            if( $request->header('Authorization') == null) //jika header tidak ada Authorization
            {
                $data = [
                    'message'       =>  'Missing Authorization in Header request'
                ];
                return response($data, 401);
            }
            else
            {

                $token = explode(' ', $request->header('Authorization'))[1];
                $ceking = DB::table('logins')
                ->where([
                    'token_jwt'     =>  $token,
                    'status'        =>  0
                ])->count();
    
                if( $ceking > 0) //jika user login di tempat yg berbeda
                {
                    $this->auth->guard($guard)->logout();
                    $data = [
                        'message'       =>  'Session login telah habis'
                    ];
                    return response($data, 401);
                }
            }

            
        }


        return $next($request);
    }
}
