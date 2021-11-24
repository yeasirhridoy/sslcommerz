<?php

namespace Hridoy\SslCommerz;

use Hridoy\SslCommerz\Actions\InitiatePayment;
use Hridoy\SslCommerz\Actions\ValidatePayment;
use Hridoy\SslCommerz\Contracts\SslCommerzContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SslCommerzPay implements SslCommerzContract
{

    public function initiate(Request $request)
    {
        $data = json_decode($request->cart_json, true);
        $rules = [
            'total_amount' => 'required|numeric|min:10',
            'emi_option' => 'in:0,1',
            'cus_name' => 'required|string',
            'cus_email' => 'required|email',
            'cus_phone' => 'required|regex:/(01)[3-9]{1}[0-9]{8}/|size:11',
            'product_name' => 'required|string',
            'cus_address' => 'string',
            'cus_city' => 'string',
            'cus_country' => 'string',
            'shipping_method' => 'in:YES,NO,Courier'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return (new InitiatePayment($request))->initiate();

        } else {
            return response($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function validate(Request $request, string $trxId, int $amount, string $currency)
    {
        $data = $request->all();
        return (new ValidatePayment($trxId, $amount, $currency, $data))->validate();
    }
}
