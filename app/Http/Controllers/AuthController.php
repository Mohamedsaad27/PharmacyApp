<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\User;
use App\Rules\UniquePhoneNumber;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|string|in:doctor,patient,pharmacy',
                'phone_number' => ['required', 'string', 'max:15', new UniquePhoneNumber],
                'country' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()){
                return response()->json($validator->errors(), 422);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role'=>$request->role
            ]);
            switch ($request->role)
            {
                case 'patient':
                    Patient::create([
                        'user_id' => $user->id,
                        'phone_number' => $request->phone_number,
                        'country' => $request->country,
                        'city' => $request->city,
                        'state' => $request->state,
                    ]);
                    break;
                case 'pharmacy':
                    Pharmacy::create([
                        'user_id' => $user->id,
                        'phone_number' => $request->phone_number,
                        'country' => $request->country,
                        'city' => $request->city,
                        'state' => $request->state,
                    ]);
                    break;
            }
            $token = JWTAuth::fromUser($user);
            $user['token'] = $token;
            return  $this->successResponse($user,'User Registered Successfully',201);
        }catch (\Exception $exception){
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required','email'],
                'password' => ['required','string'],
            ]);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(),422);
            }

            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse('Invalid email or Password',401);
            }
            $user = auth()->user();

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'User logged in successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),500);
        }
    }
    public function logout(Request $request){
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'User successfully logged out', 200);
        }catch (\Exception $exception){
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }

}
