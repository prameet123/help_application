<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DoctorRegister;
use App\Models\RegisterAmbulance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $userData = [];
        $validate = Validator::make(
            $request->all(),
            [
                'email' => 'email|required|unique:users',
                'mobile_number' => 'required|unique:users',
                'name' => 'required|max:55',
                'password' => 'required|confirmed',
            ]
        );

        if ($validate->fails()) {

            $checker = $validate->errors('*');

            $arr['status'] = 0;
            $arr['message'] = 'error';
            $arr['data'] = $checker;

            return response()->json($arr, 200);
        }
        $insert = $request->all();
        $insert['password'] = Hash::make($request->password);
        $user = User::create($insert);

        $insert['password'] = Hash::make($request->password);
        if ($insert['user_type'] == 2) {
            $userData = $this->add_doctor($insert, $request, $user);
        }
        if ($insert['user_type'] == 3) {
            $userData = $this->add_ambulance($insert, $request, $user);
        }
        $accessToken = $user->createToken('authToken')->accessToken;

        $arr['status'] = 1;
        $arr['message'] = 'Success';
        $arr['access_token'] = $accessToken;
        $arr['data'] = $userData;

        return response($arr, 201);
    }

    public function login(Request $request)
    {
        $loginData = $request->all();
        if (array_key_exists('mobile_number', $loginData)) {
            $validate = Validator::make(
                $request->all(),
                [
                    'country_code' => 'required',
                    'mobile_number' => 'required',
                    'password' => 'required',
                ]
            );
        } else {
            $validate = Validator::make(
                $request->all(),
                [
                    'email' => 'email|required',
                    'password' => 'required',
                ]
            );
        }
        if ($validate->fails()) {

            $checker = $validate->errors('*');
            $arr['status'] = 0;
            $arr['message'] = 'Try again';
            $arr['data'] = $checker;

            return response()->json($arr, 400);
        }

        //return $loginData;
        if (!Auth::attempt($loginData)) {
            $arr['status'] = 0;
            $arr['message'] = 'This User does not exist, check your details';
            $arr['data'] = null;
            return response($arr, 400);
        }
        //get access token
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        $arr['status'] = 1;
        $arr['message'] = 'success';
        $arr['data'] = auth()->user();
        $arr['access_token'] = $accessToken;
        return response($arr, 200);
    }
    private function add_ambulance($insert, $request, $user)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'ambulance_number' => 'required',
                'ambulance_reg_number' => 'required',
                'driver_alternative_mobile' => 'required',
            ]
        );
        if (isset($insert['driver_image'])) {
            $validate = Validator::make(
                $request->all(),
                ['driver_image' => 'required|mimes:jpeg,jpg,gif,png|max:2048']
            );
        }
        if ($validate->fails()) {

            $checker = $validate->errors('*');

            $arr['status'] = 0;
            $arr['message'] = 'Something went wrong';
            $arr['data'] = $checker;


            return response()->json($arr, 200);
        }

        //check certificate available  or not
        if (isset($insert['driver_image'])) {

            $imageFileName = time() . '.' . $request->driver_image->extension();
            $request->driver_image->move(public_path('uploads'), $imageFileName);
        }
        $data['user_id'] = $user['id'];
        $data['driver_name'] = $insert['name'];
        $data['ambulance_number'] = $insert['ambulance_number'];
        $data['ambulance_reg_number'] = $insert['ambulance_reg_number'];
        $data['alternative_number'] = isset($insert['alternative_number']) ? $insert['alternative_number'] : '';

        $data['driver_image'] = isset($insert['driver_image']) ? $imageFileName : '';

        $ambulanceData = RegisterAmbulance::create($data);
        $userData = $ambulanceData;
        $userData['email'] = $user['email'];
        $userData['user_type'] = $user['user_type'];
        $userData['mobile_number'] = $user['mobile_number'];
        $userData['country_code'] = $user['country_code'];
        return $userData;
    }
    private function add_doctor($insert, $request, $user)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'specilization' => 'required|max:55',
                'work_experience' => 'required',
                'available_location_lat' => 'required',
                'available_location_long' => 'required',
                'qualification' => 'required',
                'doctor_folio_number' => 'required',
                'doctor_image' => 'required|mimes:jpeg,jpg,gif,png|max:2048',
            ]
        );
        if ($validate->fails()) {

            $checker = $validate->errors('*');

            $arr['status'] = 0;
            $arr['message'] = 'Something went wrong';
            $arr['data'] = $checker;


            return response()->json($arr, 200);
        }


        $imageFileName = time() . '.' . $request->doctor_image->extension();

        $request->doctor_image->move(public_path('uploads'), $imageFileName);

        //check certificate available  or not
        if (isset($insert['doctor_licence_certificate'])) {

            $certificateFileName = time() . '.' . $request->doctor_licence_certificate->extension();
            $request->doctor_licence_certificate->move(public_path('uploads'), $certificateFileName);
        }
        $data['user_id'] = $user['id'];
        $data['specilization'] = $insert['specilization'];
        $data['doctor_folio_number'] = $insert['doctor_folio_number'];
        $data['work_experience'] = $insert['work_experience'];

        $data['doctor_licence_certificate'] = isset($insert['doctor_licence_certificate']) ? $certificateFileName : '';
        $data['alternative_number'] = isset($insert['alternative_number']) ? $insert['alternative_number'] : '';

        $data['doctor_image'] = $imageFileName;
        $data['available_location_lat'] = $insert['available_location_lat'];
        $data['available_location_long'] = $insert['available_location_long'];

        $data['available_address'] = isset($insert['available_address']) ? $insert['available_address'] : '';

        $data['qualification'] = $insert['qualification'];

        $data['rating'] = isset($insert['rating']) ? $insert['rating'] : 5;

        $doctorData = DoctorRegister::create($data);
        $userData = $doctorData;
        $userData['email'] = $user['email'];
        $userData['user_type'] = $user['user_type'];
        $userData['mobile_number'] = $user['mobile_number'];
        $userData['country_code'] = $user['country_code'];
        return $userData;
    }
    public function change_password(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|max:55',
                'password' => 'required|confirmed',
            ]
        );
        if ($validate->fails()) {

            $checker = $validate->errors('*');

            $arr['status'] = 0;
            $arr['message'] = 'Something went wrong';
            $arr['data'] = $checker;


            return response()->json($arr, 200);
        }
        $insert = $request->all();
        $update['password'] = Hash::make($request->password);
        User::where('id', $insert['user_id'])->update($update);
        $arr['status'] = 1;
        $arr['message'] = 'Success';
        $arr['data'] = null;
        return response($arr, 200);
    }
}
