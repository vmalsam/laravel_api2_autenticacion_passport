<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AuthController extends Controller
{
    public function register (Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json(
                ["error"=>$validator->errors()], 422
            );
        }

        $input = $request->all();
        $input["password"] = bcrypt($request->get("password"));
        $user = User::create($input);
        $token = $user->createToken('MyApp')->accessToken;

        

        return response()->json(
            [
                "token" => $token,
                "user" => $user
            ],200
        );
    }


    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $token =  $user->createToken('MyApp')-> accessToken; 
            $name =  $user->name;
   
            //return $this->sendResponse($success, 'User login successfully.');
            return response()->json(
                [
                    "token" => $token,
                    "user" => $user,
                    'message' => 'Succesfullly LOGIN user!'
                ],200
            );
        } 
        else{ 
            //return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            return response()->json(
                [
                    'message' => 'UNAUTHORIZED!'
                ],401
            );
        } 
    }

     // Logout API (GET)
     public function logout(Request $request) {

        auth()->user()->token()->revoke();

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ]);
    }


     // Profile API (GET)
     public function user(){
        
        $userdata = Auth::user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }
}
