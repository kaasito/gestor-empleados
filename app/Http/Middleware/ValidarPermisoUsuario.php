<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class ValidarPermisoUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $req, Closure $next)
    {
    
    if($req->has('api_token')){

        $token = $req->input('api_token');
        $usuario = User::where('api_token', $token)->first(); //devuelve objeto

        if(!$usuario){
            return response("Api key no vale", 401);
        }else{
            
            $puesto = $usuario->puesto;
            switch ($puesto) {
                case 'empleado':
                    $permiso = 1;
                    break;
                case 'RRHH':
                    $permiso = 2;
                    break;
                case 'Directivo':
                    $permiso = 3;
                    break;
                default:
                    return response("No tiene puesto", 401); 
                    break;
                }

                if($permiso>1){
                    return $next($req);
                }else{
                    return response("No tienes permisos", 401);
                }
                
        }

    }else{

        return response("No api key", 401);
    }    
       
       
    }
}
