<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
class UsersController extends Controller
{
    //all users
    public function users(){
        return response()->json(User::all());
    }

    //retrieve already login user
    public function getUser()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    //creating account for the user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('veewed')->accessToken;
        return response()->json(['success' => $success], $this->successStatus);
    }
    //logging user
    public function login(Request $request)
    {
        $credentials =$request->only('email','password');
        if (auth()->attempt($credentials)) {
            // $user = Auth::user();
            $token = auth()->user()->createToken('veewed')->accessToken;
            return response()->json(['success' => $token], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    //logout user
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    //update user details
    public function update(Request $request) {
        $user = Auth::user();
        $updated = $user->fill($request->all())->save();
        if($updated) {
            return response()->json(['message'=>'Your details have been updated successfully'],200);
        } else {
            return response()->json(['message'=>'Your details could not be updated'],500);
        }
    }

    //delete user
    public  function destroy(){
        $user =Auth::user();

        if($user->delete()) {
            return response()->json(['success'=>true,'message'=>'account deleted successfully','data'=>$user],200);
        } else {
            return response()->json(['success'=>false,'message'=>'account could not be deleted'],500);
        }
    }
}
