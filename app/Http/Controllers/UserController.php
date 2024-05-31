<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponseTrait;
    public function homePage(){
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $authData = ['user' => $authUser];

            switch ($authUser->role) {
                case 'patient':
                    $patient = Patient::where('user_id', $authUser->id)->first();
                    if ($patient) {
                        $authData['patientData'] = $patient;
                    }
                    break;
                case 'pharmacy':
                    $pharmacy = Pharmacy::where('user_id', $authUser->id)->first();
                    if ($pharmacy) {
                        $authData['pharmacyData'] = $pharmacy;
                    }
                    break;

                default:
                    return $this->errorResponse('Invalid role', 400);
            }

            return $this->successResponse(
                $authData,
                'User Information Retrieved Successfully',
                200
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function doctorList(){
        try {
            $pharmacies = Pharmacy::with('user')->limit(10)->get();
            if (!$pharmacies){
            return $this->errorResponse('No Pharmacies found', 500);
            }
            return $this->successResponse(
                $pharmacies,
                'Pharmacies Retrieved Successfully',
                200
            );
        }catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

}
