<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Auth, Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = array(
            'email' =>$request->email,
            'password' => $request->password
        );

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $userObj = User::with('customer_address')
            ->whereHas('type_document', function (Builder $query) use($user, $request) {
                $query->where('id', $user['type_document']);
            })
            ->where('identification', $user['identification'])->first();

        $user = (!empty($userObj)) ? $userObj : $user;

        if ($user->status == 0) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user'         => $user
        ], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>  'Successfully logged out']);
    }
}
