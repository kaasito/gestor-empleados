<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidarPermisoUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
    
    if($req->has('api_token')){

        $token = $req->input('api_token');
        $usuario = User::where('api_token', $token)->first();

        if(!$usuario){
            return response("Apikey no vale", 401);
        }else{
             $request->usuario = $usuario;
             return $next($request);
        }

    }else{

        return respone("No api key", 401);
    }    
       
       
    }
}
