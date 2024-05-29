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
                case 'doctor':
                    $doctor = Doctor::where('user_id', $authUser->id)->first();
                    if ($doctor) {
                        $authData['doctorData'] = $doctor;
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
            $doctors = Doctor::with('user')->limit(10)->get();
            if (!$doctors){
            return $this->errorResponse('No doctors found', 500);
            }
            return $this->successResponse(
                $doctors,
                'Doctors Retrieved Successfully',
                200
            );
        }catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

//    public function updatePersonalDetails(Request $request)
//{
//        try {
//            $authUser = Auth::user();
//            if (!$authUser) {
//                return $this->errorResponse('Unauthorized', 401);
//            }
//            switch ($authUser->role) {
//                case 'patient':
//                    $patient = Patient::where('user_id', $authUser->id)->first();
//                  $patient->update([
//                      'address' => $request->address ? $request->address : $patient->address,
//                      'phone_number' => $request->phone_number ? $request->phone_number : $patient->phone_number,
//                  ]);
//                    break;
//                case 'pharmacy':
//                    $pharmacy = Pharmacy::where('user_id', $authUser->id)->first();
//                    $pharmacy->update([
//                        'country' => $request->country ? $request->country : $pharmacy->country,
//                        'city' => $request->city ? $request->city : $pharmacy->city,
//                        'state' => $request->state ? $request->state : $pharmacy->state,
//                        'license_number' => $request->license_number ? $request->license_number : $pharmacy->license_number,
//                        'phone_number' => $request->phone_number ? $request->phone_number : $pharmacy->phone_number,
//                    ]);
//                    break;
//                case 'doctor':
//                    $doctor = Doctor::where('user_id', $authUser->id)->first();
//                    $doctor->update([
//                        'specialization' => $request->specialization ? $request->specialization : $doctor->specialization,
//                        'address' => $request->address ? $request->address : $doctor->address,
//                        'phone_number' => $request->phone_number ? $request->phone_number : $doctor->phone_number,
//                    ]);
//                    break;
//                default:
//                    return $this->errorResponse('Invalid role', 400);
//            }
//        }catch (\Exception $exception) {
//            return $this->errorResponse(['message' => $exception->getMessage()], 500);
//        }
//    }


}
