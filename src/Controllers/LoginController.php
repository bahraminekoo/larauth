<?php

namespace Bahraminekoo\Larauth\Controllers;

use Bahraminekoo\Larauth\Models\Person;
use App\Http\Controllers\Controller;
use Bahraminekoo\Larauth\Requests\LoginInfo;
use Bahraminekoo\Larauth\Traits\Normalizable;
use Illuminate\Http\Request;
use Hash;

class LoginController extends Controller
{

    use Normalizable;

    public function login(LoginInfo $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = Person::where('email', $email)->firstOrFail();

        if (Hash::check($password, $user->password)) {

            return response()->json([
                'status' => true,
                'message' => ['login successful'],
                'data' => $user->normalize(),
            ]);

        }

        return response()->json([
            'status' => false,
            'message' => ['login unsuccessful'],
        ], 403);

    }
}