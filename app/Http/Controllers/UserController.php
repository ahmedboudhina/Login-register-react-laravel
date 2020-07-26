<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $status_code  =  200;

    public function userSignUp(Request $request) {
        $validator              =        Validator::make($request->all(), [
                 "name"   =>  "required",
              "lastname"  => "required",
                 "email"  =>  "required|email",
              "password"  =>  "required",
                 "phone"  =>   "required"
        ]);

        if($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "validation_error", "errors" => $validator->errors()]);
        }

        


        $last_name  =  $request->lastname;
        $first_name  =  $request->name;
       

        $userDataArray  = array(
            "first_name" => $first_name,
            "last_name"  => $last_name,
            "full_name" => $first_name." ".$last_name,
            "email" =>   $request->email,
            "password" => md5($request->password),
            "phone" =>  $request->phone
        );

        // recuperer les données de users avec l'email déjà donné si existe
        $user_status = User::where("email", $request->email)->first();
        // verifier si l'email existe dans la base
        if(!is_null($user_status)) {
           return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! email already registered"]);
        }

        // ajouter les données au table users 
        $user = User::create($userDataArray);

        // si l'insertion de données est effectuer avec succes
        if(!is_null($user)) {
            return response()->json(["status" => $this->status_code, "success" => true, "message" => "Registration completed successfully", "data" => $user]);
        }

        // si l'insertion n'est pas effectuer avec succes
        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "failed to register"]);
        }
    }


    // ------------ [ User Login ] -------------------
    public function userLogin(Request $request) {

        $validator  = Validator::make($request->all(),
            [
                "email" => "required|email",
                "password" => "required"
            ]
        );

        if($validator->fails()) {
            return response()->json(["status" => "failed", "validation_error" => $validator->errors()]);
        }


            $status    =   User::where("email", $request->email)->where("password", md5($request->password))->first();

            // si l'utilisateur trouvé
            if(!is_null($status)) {
                $user  =  $this->userDetail($request->email);

                return response()->json(["status" => $this->status_code, "success" => true, "message" => "You have logged in successfully", "data" => $user]);
            }

            else {
                return response()->json(["status" => "failed", "success" => false, "message" => "Unable to login. Incorrect password."]);
            }
    
    }

    // ------------------ [ User Detail ] ---------------------
    public function userDetail($email) {
        $user  = array();
        if($email != "") {
            $user  =  User::where("email", $email)->first();
            return $user;
        }
    }
}