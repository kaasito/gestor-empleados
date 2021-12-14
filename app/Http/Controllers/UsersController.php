<?php

namespace App\Http\Controllers;

use App\Mail\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            $usuario->password = $datos->password;
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
        
        
       /* $password = $req->password;
        $valido = true;

        if($password){

            if(!preg_match("/[a-z]{6,}/", $password))
                $valido = true;
        }else{
            $valido = false; 
        }

        $email = $req->email;

        if(User::where('email', $email)->first())
            $valido = false;


        $validator = Validator::make(json_decode($req->getContent(),), [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ]);

       

       // $usuario->password = Hash::make($req->da);


        if ($validator->fails()) {
            return redirect('post/create')
                        ->withErrors($validator)
                        ->withInput();
        }*/
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
           Mail::to($usuario->email)->send(new Notification("
           Confirme cambio de contraseña", "Se ha realizado un cambio en su contraseña", "hola sofi"));
           $respuesta["password"] = "Nueva contraseña: ".$password;
        }else{
            $respuesta["msg"] = 401;
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