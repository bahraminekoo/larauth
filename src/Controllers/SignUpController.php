<?php

namespace Bahraminekoo\Larauth\Controllers;

use Bahraminekoo\Larauth\Models\Person;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bahraminekoo\Larauth\Requests\UserInfo;
use Mail;
use Hash;
use Bahraminekoo\Larauth\Mail\VerifyEmail;
use Bahraminekoo\Larauth\Traits\Normalizable;

class SignUpController extends Controller
{

    use Normalizable;

    public function register(UserInfo $request)
    {

        $email = $request->input('email');
        $hash = Hash::make($email);
        $user = new Person();
        $user->email = $email;
        $user->password = $request->input('password');
        $user->token = $hash;
        $user->save();

        Mail::to($email)->send(new VerifyEmail($email, $request->input('password'), $hash));

        return response()->json([
            'status' => true,
            'message' => ['register successful, please check out your email and click on the verify link'],
            'data' => $user->normalize(),
        ]);
    }

    public function verifyEmail(Request $request, $email, $hash)
    {

        $user = Person::where('email' => $email, 'token' => $hash)->firstOrFail();
        $user->verified = 1;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => ['activation successful, now you can log into your account'],
            'data' => $user->normalize(),
        ]);

    }
}

























