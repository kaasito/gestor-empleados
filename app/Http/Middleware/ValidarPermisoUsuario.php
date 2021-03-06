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
    $jdata = $req->getContent();
    $datos = json_decode($jdata);
    if($datos->api_token){

        
        $token = $datos->api_token;
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
                case 'directivo':
                    $permiso = 3;
                    break;
                default:
                    return response("No tiene puesto", 401); 
                    break;
                }

                if($permiso>1){
                    return $next($req); //Pasar a la siguiente condicion o en su defecto Controller
                }else{
                    return response("No tienes permisos para hacer eso, tu puesto de trabajo es ".$puesto, 401);
                }       
        }
    }else{
        return response("No api key", 401);
    }    
       
       
    }
}
