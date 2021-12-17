<?php

namespace App\Http\Controllers;

use App\Mail\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function registrar(Request $req){

        
        $validator = Validator::make(json_decode($req->getContent(), true), [
            'name' => 'required',
            'puesto' => 'required',
            'password' => 'required|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[A-Za-z0-9]).{6,}/',
            'email' => 'required|unique:users',
            'salario' => 'required',
            'biografia' => 'required',
        ]);
        
        if ($validator->fails()) {
            $respuesta["msg"] = $validator->errors()->first();
            $respuesta["status"] = 0;
        } else {

            $respuesta = ["status" => 1, "msg" => ""];
            $datos = $req->getContent();
            $datos = json_decode($datos);
            $usuario = new User();
            $usuario->name = $datos->name;
            $usuario->puesto = $datos->puesto;
            $usuario->password = Hash::make($datos->password);
            $usuario->email = $datos->email;
            $usuario->salario = $datos->salario;
            $usuario->biografia = $datos->biografia;
            try{
                $respuesta["status"] = 1;
                $respuesta["msg"] = "Usuario creado con éxito";
                $usuario->save();
            }catch(\Exception $e){
                $respuesta["msg"] = $e ->getMessage();
                $respuesta["status"] = 0;
            }
        }

            return response()->json($respuesta);
    }
    public function login(Request $req){
          
        $jdatos = $req->getContent();
        $datos = json_decode($jdatos);

        $usuario = User::where('email', $datos->email)->first();
        if($usuario && Hash::check($datos->password, $usuario->password)){
            $token = Hash::make(now().$usuario->email); //creación del token
            $usuario->api_token = $token;
            $usuario->save();
            $respuesta["token"] = $token;
        }else{
            $respuesta["msg"] = 401;
        }
        return response()->json($respuesta);
    }
    public function recuperarPass(Request $req){
        $jdatos = $req->getContent();
        $datos = json_decode($jdatos);

        $usuario = User::where('email', $datos->email)->first();
        if($usuario){
           $password = generarPass("abcdefghijklmnopqrstuvwxyz1234567890¿?!¡_",8);
           $usuario->password = Hash::make($password);
           $usuario->save();
           Mail::to($usuario->email)->send(new Notification("Cambio de contraseña", "Si no ha sido usted quien ha efectuado este cambio pongase en contacto con nuestro servicio técnico", "Su nueva contraseña es ".$password));
           $respuesta["password"] = "Nueva contraseña: ".$password;
        }else{
            $respuesta["msg"] = 401;
        }
        return response()->json($respuesta);
    }
    public function listarEmpleados(Request $req){
        $jdatos = $req->getContent();
        $datos = json_decode($jdatos);

        $usuario = User::where('api_token', $datos->api_token)->first();

        // $respuesta["datos"] = DB::table('users')->get();
        $empleados = User::where('puesto', 'empleado')->get();
        
        $id = 1;
        
        if($usuario->puesto == "directivo"){
            $rrhh = User::where('puesto', 'RRHH')->get();
            foreach ($rrhh as  $empleado) {
                $respuesta[$id]["nombre"] = $empleado->name;
                $respuesta[$id]["puesto"] = $empleado->puesto;
                $respuesta[$id]["salario"] = $empleado->salario;
                $id++;
            }
        }
        foreach ($empleados as $empleado) {
            $respuesta[$id]["nombre"] = $empleado->name;
            $respuesta[$id]["puesto"] = $empleado->puesto;
            $respuesta[$id]["salario"] = $empleado->salario;
            $id++;
        }
       
        return response()->json($respuesta);
    }
    public function verEmpleado(Request $req){
        $jdatos = $req->getContent();
        $datos = json_decode($jdatos);
        $peticiario = User::where('api_token', $datos->api_token)->first();
        $usuario = User::where('email', $datos->email)->first();
        if($peticiario->puesto == "directivo"){
            $respuesta["Nombre"] = $usuario->name;
            $respuesta["Email"] = $usuario->email;
            $respuesta["Puesto"] = $usuario->puesto;
            $respuesta["Salario"] = $usuario->salario;
            $respuesta["Biografía"] = $usuario->biografia;
        }
        if($peticiario->puesto == "RRHH" && $usuario->puesto == "empleado"){
                $respuesta["Nombre"] = $usuario->name;
                $respuesta["Email"] = $usuario->email;
                $respuesta["Puesto"] = $usuario->puesto;
                $respuesta["Salario"] = $usuario->salario;
                $respuesta["Biografía"] = $usuario->biografia;
        }else if($peticiario->puesto == "RRHH" && ($usuario->puesto == "directivo" || $usuario->puesto == "RRHH")){
                $respuesta["msg"]= "El usuario no es un empleado y no tienes permiso para ver sus detalles";
        }
        
         return response()->json($respuesta);
    }
    public function verPerfil(Request $req){
        $jdatos = $req->getContent();
        $datos = json_decode($jdatos);
       

        $usuario = User::where('api_token', $datos->api_token)->first();

        if($usuario->api_token){
            $respuesta["Nombre"] = $usuario->name;
            $respuesta["Email"] = $usuario->email;
            $respuesta["Puesto"] = $usuario->puesto;
            $respuesta["Salario"] = $usuario->salario;
            $respuesta["Biografía"] = $usuario->biografia;
        }else{
            $respuesta["msg"] = "No se ha logueado";
        }
        return response()->json($respuesta);
        
    }
    public function modificarEmpleado(Request $req){
        
        $datos = $req->getContent();
        $datos = json_decode($datos);
        $correo = $req->input('correo', '');
        $peticiario = User::where('api_token', $datos->api_token)->first();
        $usuario = User::where('email', $correo)->first();
        
        if($peticiario->puesto == "directivo"){//el peticiario es directivo 
            if($usuario->puesto == "directivo"){//el usuario es directivo tambien
                if($usuario->email == $peticiario->email){//el usuario es la misma persona que hace la petición
                    if(isset($datos->name))
                    $usuario->name = $datos->name;

                    if(isset($datos->name))
                    $usuario->puesto = $datos->puesto;

                    if(isset($datos->name))
                    $usuario->password = Hash::make($datos->password);
            
                    if(isset($datos->name))
                    $usuario->email = $datos->email;
                
                    if(isset($datos->name))
                    $usuario->salario = $datos->salario;

                    if(isset($datos->name))
                    $usuario->biografia = $datos->biografia;
        
                    $usuario->save();
                    $respuesta["msg"] = "Usuario editado con éxito";
                    
                }else{//el usuario es otro directivo
                    $respuesta["msg"] = "No puedes editar a otro directivo";
                }
            }   
            if($usuario->puesto == "empleado"){
                if(isset($datos->name))
                $usuario->name = $datos->name;

                if(isset($datos->name))
                $usuario->puesto = $datos->puesto;

                if(isset($datos->name))
                $usuario->password = Hash::make($datos->password);
        
                if(isset($datos->name))
                $usuario->email = $datos->email;
            
                if(isset($datos->name))
                $usuario->salario = $datos->salario;

                if(isset($datos->name))
                $usuario->biografia = $datos->biografia;
    
                $usuario->save();
                $respuesta["msg"] = "Usuario editado con éxito";
            }  
            if($usuario->puesto == "RRHH"){
                if(isset($datos->name))
                $usuario->name = $datos->name;

                if(isset($datos->name))
                $usuario->puesto = $datos->puesto;

                if(isset($datos->name))
                $usuario->password = Hash::make($datos->password);
        
                if(isset($datos->name))
                $usuario->email = $datos->email;
            
                if(isset($datos->name))
                $usuario->salario = $datos->salario;

                if(isset($datos->name))
                $usuario->biografia = $datos->biografia;
    
                $usuario->save();
                $respuesta["msg"] = "Usuario editado con éxito";
            }  
        }
        if($peticiario->puesto == "RRHH"){//el peticiario es RRHH
               if($usuario->puesto == "empleado"){//el usuario es empleado
                    if(isset($datos->name))
                        $usuario->name = $datos->name;

                    if(isset($datos->name))
                        $usuario->puesto = $datos->puesto;

                    if(isset($datos->name))
                        $usuario->password = Hash::make($datos->password);
            
                    if(isset($datos->name))
                        $usuario->email = $datos->email;
                
                    if(isset($datos->name))
                        $usuario->salario = $datos->salario;

                    if(isset($datos->name))
                        $usuario->biografia = $datos->biografia;
        
                    $usuario->save();
                    $respuesta["msg"] = "Usuario editado con éxito";
               }else{
                    $respuesta["msg"] = "Solo puedes editar a trabajadores que sean empleados";
               } 
        }
        if($peticiario->puesto == "empleado"){//el peticiario es RRHH
            $respuesta["msg"] = "Los empleados no pueden modificar otros usuarios"; 
     }
        return response()->json($respuesta);
    }
}

function generarPass($opciones, $lengt = 5){
    
    $charactersLenght = strlen($opciones);
    $randomString = '';
    for ($i=0; $i < $lengt; $i++) {
        $randomString .= $opciones[rand(0, $charactersLenght - 1)];
    }
    return $randomString;
}