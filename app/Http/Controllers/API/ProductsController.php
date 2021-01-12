<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;


class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $data = Products::all();
        foreach ($data as $val) {
            $images = ProductImages::select('image')->where('drug_id', $val['id'])->get();;
            $resultData[] = [
                "id" => $val['id'], "drugstore_details_id" => $val['drugstore_details_id'],
                "drug_name" => $val['drug_name'], "ip_power" => $val['ip_power'], "drug_company_name" => $val['drug_company_name'],
                "manufacturing_date" => $val['manufacturing_date'], "expiry_date" => $val['expiry_date'], "mfg_lic_no" => $val['mfg_lic_no'],
                "price_per_piece" => $val['price_per_piece'], "notes" => $val['notes'], "created_at" => date("Y-m-d h:m:s", strtotime($val['created_at'])),
                "updated_at" =>$val['updated_at'],
                "is_active" => $val['is_active'], "images" => $images
            ];
        }
        if (!empty($data)) {
            $arr['status'] = 1;
            $arr['message'] = 'data found';
            $arr['data'] = $resultData;
        } else {
            $arr['status'] = 1;
            $arr['message'] = 'data not found';
            $arr['data'] = null;
        }
        return response($arr, 200);
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $validate = Validator::make(
            $request->all(),
            [
                'drugstore_details_id' => 'required',
                'drug_name' => 'required',
                'ip_power' => 'required',
                'drug_company_name' => 'required',
                'manufacturing_date' => 'required',
                'expiry_date' => 'required',
                'mfg_lic_no' => 'required',
                'price_per_piece' => 'required|numeric',
                'notes' => 'required',
            ]
        );

        if ($validate->fails()) {

            $checker = $validate->errors('*');

            $arr['status'] = 0;
            $arr['message'] = 'error';
            $arr['data'] = $checker;

            return response($arr, 200);
        }
        $input['manufacturing_date'] = date("Y-m-d", strtotime($input['manufacturing_date']));
        $input['expiry_date'] = date("Y-m-d", strtotime($input['expiry_date']));
        $drug = Products::create($input);
        if (isset($input['drug_images'])) {
            foreach ($input['drug_images'] as $val) {
                $imageFileName = time() . '.' . $val->getClientOriginalName();
                $val->move(public_path('uploads'), $imageFileName);
                $data = ["drug_id" => $drug['id'], "image" => $imageFileName];
                ProductImages::create($data);
            }
        }
        $arr['status'] = 1;
        $arr['message'] = 'Successfully add drug';
        $arr['data'] = $drug;

        return response($arr, 201);
    }
}
