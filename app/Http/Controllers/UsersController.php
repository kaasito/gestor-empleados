<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class UsersController extends Controller
{
    public function altaUsuario(Request $req){


        $password = $req->password;
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
        }
    }
    public function login(Request $req){
        //Validar los datos


        //Buscar al usuario por su email

        $usuario = User::where('email', $req->email)->first();
    }
}
