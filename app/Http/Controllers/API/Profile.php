<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DoctorRegister;
use App\Models\RegisterAmbulance;
use Illuminate\Support\Facades\Validator;

class Profile extends Controller
{
    public function index()
    {
        $arr['status'] = 0;
        $arr['message'] = 'error:user_id missing';
        $arr['data'] = null;
        return response($arr, 200);
    }

    public function show($user_id)
    {
        $userDetails = [];
        $users = User::find($user_id);
        if ($users['user_type'] == 1 || $users['user_type'] == 0) {
            $userDetails = $users;
        }
        if ($users['user_type'] == 2) {
            //return $users['user_type'];
            $userDetails = User::join('tbl_doctor_details', 'tbl_doctor_details.user_id', '=', 'users.id')
                ->where('tbl_doctor_details.is_active', 1)
                ->where('tbl_doctor_details.user_id', $user_id)
                ->get(['users.email', 'users.mobile_number', 'tbl_doctor_details.*']);
        }
        if ($users['user_type'] == 3) {
            $userDetails = User::join('tbl_ambulance_details', 'tbl_ambulance_details.user_id', '=', 'users.id')
                ->where('tbl_ambulance_details.is_active', 1)
                ->get(['users.email', 'users.mobile_number', 'tbl_ambulance_details.*']);
        }
        $arr['status'] = 1;
        $arr['message'] = 'Success';
        $arr['data'] = $userDetails;
        return response($arr, 200);
    }

    public function update(Request $request, $user_id)
    {
        $input = $request->all();
        $userArray = ["user_id" => $user_id];
        $validate = Validator::make(
            $userArray,
            [
                'user_id' => 'required',
            ]
        );
        if (isset($input['doctor_image'])) {
            $validate = Validator::make(
                $request->all(),
                ['doctor_image' => 'required|mimes:jpeg,jpg,gif,png|max:2048']
            );
        }
        if ($validate->fails()) {

            $checker = $validate->errors('*');
            $arr['status'] = 0;
            $arr['message'] = 'Try again';
            $arr['data'] = $checker;

            return response($arr, 400);
        }
        $updateDate = [];
        if (isset($input['mobile_number'])) {
            $updateDate['mobile_number'] = $input['mobile_number'];
            $validate = Validator::make(
                $input,
                [
                    'mobile_number' => 'unique:users',
                ]
            );
        }
        if (isset($input['email'])) {
            $updateDate['email'] = $input['email'];
            $validate = Validator::make(
                $input,
                [
                    'email' => 'unique:users',
                ]
            );
        }
        if (isset($input['name'])) {
            $updateDate['name'] = $input['name'];
        }
        if ($validate->fails()) {

            $checker = $validate->errors('*');
            $arr['status'] = 0;
            $arr['message'] = 'Try again';
            $arr['data'] = $checker;

            return response($arr, 400);
        }
        User::where('id', $userArray['user_id'])->update($updateDate);
        $users = User::find($user_id);
        if ($users['user_type'] == 2) {
            $doctorData = [];
            if (isset($insert['doctor_image'])) {

                $imageFileName = time() . '.' . $request->doctor_image->extension();
                $request->doctor_image->move(public_path('uploads'), $imageFileName);
            }
            if (isset($insert['doctor_licence_certificate'])) {

                $certificateName = time() . '.' . $request->doctor_licence_certificate->extension();
                $request->doctor_licence_certificate->move(public_path('uploads'), $certificateName);
            }
            if (isset($input['specilization'])) {
                $doctorData['specilization'] = $input['specilization'];
            }
            if (isset($input['doctor_folio_number'])) {
                $doctorData['doctor_folio_number'] = $input['doctor_folio_number'];
            }
            if (isset($input['doctor_licence_certificate'])) {
                $doctorData['doctor_licence_certificate'] = $certificateName;
            }
            if (isset($input['work_experience'])) {
                $doctorData['work_experience'] = $input['work_experience'];
            }
            if (isset($input['alternative_number'])) {
                $doctorData['alternative_number'] = $input['alternative_number'];
            }
            if (isset($input['available_location_lat'])) {
                $doctorData['available_location_lat'] = $input['available_location_lat'];
            }
            if (isset($input['available_location_long'])) {
                $doctorData['available_location_long'] = $input['available_location_long'];
            }
            if (isset($input['qualification'])) {
                $doctorData['qualification'] = $input['qualification'];
            }
            if (isset($input['available_address'])) {
                $doctorData['available_address'] = $input['available_address'];
            }
            if (isset($input['doctor_image'])) {
                $doctorData['doctor_image'] = $imageFileName;
            }

            if (!empty($doctorData))
                DoctorRegister::where('user_id', $userArray['user_id'])->update($doctorData);
        }
        if ($users['user_type'] == 3) {
            $ambulanceData = [];
            if (isset($input['ambulance_number'])) {
                $ambulanceData['ambulance_number'] = $input['ambulance_number'];
            }
            if (isset($input['ambulance_reg_number'])) {
                $ambulanceData['ambulance_reg_number'] = $input['ambulance_reg_number'];
            }
            if (isset($input['driver_alternative_mobile'])) {
                $ambulanceData['driver_alternative_mobile'] = $input['driver_alternative_mobile'];
            }
            if (!empty($ambulanceData))
                RegisterAmbulance::where('user_id', $input['user_id'])->update($ambulanceData);
        }
        $arr['status'] = 1;
        $arr['message'] = 'Success';
        $arr['data'] = null;

        return response($arr, 200);
    }
}
