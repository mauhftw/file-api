<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use StdClass;

class AuthController extends Controller {



    public function __construct() {

        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }




    public function authenticate(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => ['error' =>'invalid_credentials']], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['message' => ['error' => 'could_not_create_token']], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }


    public function getAuthenticatedUser() {

    try {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => ['error' =>  'user_not_found']], 404);
        }

    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

        return response()->json(['message' => ['error' => 'token_expired']], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

        return response()->json(['message' => ['error' => 'token_invalid']], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

        return response()->json(['message' => ['error' => 'token_absent']], $e->getStatusCode());

    }

    // the token is valid and we have found the user via the sub claim
    $newUser = new stdclass;
    $newUser->id = $user->id;
    $newUser->name = $user->name;
    $newUser->email = $user->email;

    //return response()->json(compact('user'));
    return response()->json(['data' => $newUser],200);
}



}
