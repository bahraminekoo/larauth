<?php
namespace Bahraminekoo\Larauth\Controllers;

use Config;
use Validator;
use App\User;
use App\InvitationCode;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;
use App\Traits\Helper;
use App\Services\User\UserServiceInterface;
use App\Repositories\User\UserInterface;
use Twilio\Rest\Client;

class SignUpController extends Controller
{
    use Helper;

    protected $userService;

    protected $user;

    public function __construct(UserServiceInterface $userService, UserInterface $user)
    {
        $this->userService = $userService;
        $this->user = $user;
    }

    public function sendVerifyEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:person',
            'formId' => 'required',
            'invitationCode' => 'required'
        ];

        $this->validate($request, $rules);

        $data = $request->only([
            'email',
            'formId',
        ]);

        $invitationCode = $request->input('invitationCode');
        $identityToken = 'email.'.$request->input('email');

        $validator = $this->verifyInvitationCode($invitationCode, $identityToken);
        if (!env('APP_DEBUG') && $validator) {
            $this->throwValidationException($request, $validator);
        }

        $result = $this->userService->sendCodeToEmail($data['email'], $data['formId']);
        $response = [
            'status' => true,
            'message' => $result['message'],
            'data' => $result['data']
        ];
        if (env('APP_DEBUG')) {
            $response['debug'] = $result['debug'];
        }
        return response()->json($response, $result['code']);
    }

    public function verifyCode(Request $request)
    {
        $data = $request->only([
            'email',
            'code',
            'formId',
        ]);

        $rules = [
            'email' => 'required|email',
            'code' => 'required',
            'formId' => 'required',
        ];

        $this->validate($request, $rules);

        $token = $this->generateToken();
        $result = $this->userService->verifyCode(
            $data['code'],
            $data['formId'],
            $token,
            $data['email']
        );

        if ($result['code'] == 200) {
            $response = [
                'status' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ];

            if (env('APP_DEBUG')) {
                $response['debug'] = [
                    'token' => $token
                ];
            }

            return response()->json($response, $result['code']);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'],
        ], $result['code']);

    }

    public function verifyCodePhone(Request $request)
    {
        $data = $request->only([
            'phone',
            'code',
            'formId',
        ]);

        $rules = [
            'phone' => 'required',
            'code' => 'required',
            'formId' => 'required',
        ];

        $this->validate($request, $rules);

        $token = $this->generateToken();
        $result = $this->userService->verifyCodePhone(
            $data['code'],
            $data['formId'],
            $token,
            $data['phone']
        );

        if ($result['code'] == 200) {
            $response = [
                'status' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ];

            if (env('APP_DEBUG')) {
                $response['debug'] = [
                    'token' => $token
                ];
            }

            return response()->json($response, $result['code']);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'],
        ], $result['code']);

    }

    public function register(Request $request)
    {
        $data = $request->only([
            'email',
            'password',
            'formId',
            'token',
            //'countryId',
        ]);

        $rules = [
            'email' => 'required|email|unique:person,email',
            // 'countryId' => 'required|exists:countries,id',
            'password' => 'required',
            'formId' => 'required',
            'token' => 'required',
        ];

        $this->validate($request, $rules);

        $user = $this->user->addUser($data);
        if(is_array($user) && array_key_exists('code', $user)) {

            return response()->json($user, $user['code']);
        }

        // update invitation code;
        $invitationCode = InvitationCode::getConsumedCode('email.'.$user->email);
        if ($invitationCode) {
            $invitationCode->consumed = 1;
            $invitationCode->consumer_id = $user->getKey();
            $invitationCode->save();
        }

        $token = auth()->login($user);

        return response()->json([
            'status' => true,
            'message' => [ 'login successful'],
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        ]);
    }

    public function registerPhone(Request $request)
    {
        $data = $request->only([
            'phone',
            'password',
            'formId',
            'token',
            'countryId',
        ]);

        $rules = [
            'countryId' => 'required|exists:countries,id',
            'phone' => 'required|unique:person,phone',
            'password' => 'required',
            'formId' => 'required',
            'token' => 'required',
        ];

        $this->validate($request, $rules);

        $user = $this->user->addUserPhone($data);

        // update invitation code;
        $invitationCode = InvitationCode::getConsumedCode('phone.'.$user->phone);
        if ($invitationCode) {
            $invitationCode->consumed = 1;
            $invitationCode->consumer_id = $user->getKey();
            $invitationCode->save();
        }

        $token = auth()->login($user);
        // $token = $user->getToken();
        $normalized = $user->normalize();

        return response()->json([
            'status' => true,
            'message' => [ 'login successful'],
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        ]);
    }

    private function verifyInvitationCode($invitationCode, $identity)
    {
        $validator = Validator::make([], []); // Empty data and rules fields

        $codeEntity = InvitationCode::where('code', $invitationCode)->first();
        if (is_null($codeEntity)) {
            $validator->errors()->add('invitationCode', 'Invalid invitation code.');
            return $validator;
        }
        if ($codeEntity->consumed) {
            $validator->errors()->add('invitationCode', 'Invitation code has been consumed.');
            return $validator;
        }
        $cachedConsumer = $codeEntity->cachedConsumer;
        if ( $cachedConsumer && $cachedConsumer !== $identity) {
            $validator->errors()->add('invitationCode', 'Invtation code and email does not match');
            return $validator;
        } else {
            $codeEntity->cachedConsumer = $identity;
            return ;
        }
    }
}

























