<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * login
     *
     * @param  mixed $request Array | []
     * @return void
     */

    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => false, 'message' => ''], 422);
        }

        $user = User::whereEmail($request->email)->first();
        if ($user) {
            if(Auth::attempt($request->only(['email','password']))){
                $user['token'] = $user->createToken('user')->plainTextToken;
                return response()->json(['data' => $user, 'status' => true, 'message' => 'Login Success']);
            }
            return response()->json(['error' => ['password' => "Password doesn't Match"], 'status' => false, 'message' => ""], 401);
        }

        //User Not Found Create User
        $insert = [
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        $user = User::create($insert);
        $user['token'] = $user->createToken('user');
        return response()->json(['data' => [], 'status' => true, 'message' => 'Account Created'], 201);
    }
}
