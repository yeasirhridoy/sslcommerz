<?php

namespace Hridoy\SslCommerz\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InitiatePayment extends SslCommerz
{
    protected $data;
    protected $currency;
    protected $productCategory;
    protected $emiOption;
    protected $productProfile;
    protected $address;
    protected $city;
    protected $country;
    protected $shippingMethod;
    protected $transactionId;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->data = json_decode($request->cart_json, true);
        $this->currency = $request->currency ?? config('sslcommerz.currency');
        $this->productCategory = $request->product_category ?? config('sslcommerz.product_category');
        $this->emiOption = $request->emi_option ?? config('sslcommerz.emi_option');
        $this->productProfile = $request->product_profile ?? config('sslcommerz.emi_option');
        $this->address = $request->address ?? config('sslcommerz.address');
        $this->city = $request->city ?? config('sslcommerz.city');
        $this->country = $request->country ?? config('sslcommerz.country');
        $this->transactionId = uniqid(config('sslcommerz.transaction_id_prefix'));
        $this->shippingMethod = $request->shipping_method ?? config('sslcommerz.shipping_method');
    }

    public function initiate()
    {
        $setLocalhost = config('sslcommerz.localhost');
        $form = $this->buildForm();
        $url = $this->baseUrl . config('sslcommerz.initiate_url');

        $curl = curl_init();

        if (!$setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // The default value for this option is 2. It means, it has to have the same name in the certificate as is in the URL you operate against.
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // When the `verify` value is 0, the connection succeeds regardless of the names in the certificate.
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $form);

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNo = curl_errno($curl);
        curl_close($curl);

        if ($code == 200 & !($curlErrorNo)) {
            $response = json_decode($response, true);
            if (isset($response['GatewayPageURL']) && $response['GatewayPageURL'] != '') {
                if ($this->baseUrl != null && $this->baseUrl == 'https://securepay.sslcommerz.com') {
                    $response = ['status' => 'SUCCESS', 'data' => $response['GatewayPageURL'], 'logo' => $response['storeLogo']];
                } else {
                    $response = ['status' => 'success', 'data' => $response['GatewayPageURL'], 'logo' => $response['storeLogo']];
                }
                return $response;
            } else {
                return ['status' => 'fail', 'data' => null, 'message' => $response['failedreason']];
            }
        } else {
            $response = [
                "error" => "FAILED TO CONNECT WITH SSLCOMMERZ API"
            ];
            return response($response, $code);
        }
    }

    private function buildForm(): array
    {
        $form = [];
        $form['store_id'] = $this->storeId;
        $form['store_passwd'] = $this->storePassword;
        $form['total_amount'] = $this->data['total_amount'];
        $form['currency'] = $this->currency;
        $form['tran_id'] = $this->transactionId;
        $form['product_category'] = $this->productCategory;
        $form['success_url'] = $this->siteUrl . $this->successUrl;
        $form['fail_url'] = $this->siteUrl . $this->failUrl;
        $form['cancel_url'] = $this->siteUrl . $this->cancelUrl;
        $form['ipn_url'] = $this->siteUrl . $this->ipnUrl;
        $form['emi_option'] = $this->emiOption;
        $form['cus_name'] = $this->data['cus_name'];
        $form['cus_email'] = $this->data['cus_email'];
        $form['cus_phone'] = $this->data['cus_phone'];
        $form['product_name'] = $this->data['product_name'];
        $form['product_profile'] = $this->productProfile;
        $form['cus_add1'] = $this->address;
        $form['cus_city'] = $this->city;
        $form['cus_country'] = $this->country;
        $form['shipping_method'] = $this->shippingMethod;
        return $form;
    }
}
