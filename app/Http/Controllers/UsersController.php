<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class UsersController extends Controller
{

    public function registrar(Request $req){

        
        $validator = Validator::make(json_decode($req->getContent(),), [
            'nombre' => 'required|unique:posts|max:255',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect('post/create')
                        ->withErrors($validator)
                        ->withInput();
        }

            $respuesta = ["status" => 1, "msg" => ""];
            $datos = $req->getContent();
            $datos = json_decode($datos);
            $usuario = new User();
            $usuario->nombre = $datos->nombre;
            $usuario->email = $datos->email;

            $usuario->password = $datos->password;
            $usuario->foto_perfil = $datos->foto_perfil;
            try{
                $respuesta["status"] = 1;
                $respuesta["msg"] = "Usuario creado con éxito";
                $usuario->save();
            }catch(\Exception $e){
                $respuesta["msg"] = $e ->getMessage();
                $respuesta["status"] = 0;
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
        //Validar los datos


        //Buscar al usuario por su email

        $usuario = User::where('email', $req->email)->first();
    }
}
