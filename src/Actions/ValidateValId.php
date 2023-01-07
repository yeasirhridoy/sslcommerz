<?php

namespace Hridoy\SslCommerz\Actions;

class ValidateValId extends SslCommerz
{
    protected $valId;
    protected $result;

    public function __construct(string $valId)
    {
        parent::__construct();
        $this->valId = $valId;
    }

    public function validate()
    {
        $valId = $this->valId;
        $store_id = urlencode($this->storeId);
        $store_passwd = urlencode($this->storePassword);
        $requested_url = $this->baseUrl.config('sslcommerz.validation_url') . "?val_id=" . $valId . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json";
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $requested_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        if (config('sslcommerz.localhost')) {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
        } else {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 2);
        }

        $result = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && !(curl_errno($handle))) {
            $result = json_decode($result);
            $this->result = $result;
            $status = $result->status;
            $tran_date = $result->tran_date;
            $tran_id = $result->tran_id;
            $val_id = $result->val_id;
            $amount = $result->amount;
            $store_amount = $result->store_amount;
            $bank_tran_id = $result->bank_tran_id;
            $card_type = $result->card_type;
            $currency_type = $result->currency_type;
            $currency_amount = $result->currency_amount;

            # ISSUER INFO
            $card_no = $result->card_no;
            $card_issuer = $result->card_issuer;
            $card_brand = $result->card_brand;
            $card_issuer_country = $result->card_issuer_country;
            $card_issuer_country_code = $result->card_issuer_country_code;

            # API AUTHENTICATION
            $APIConnect = $result->APIConnect;
            $validated_on = $result->validated_on;
            $gw_version = $result->gw_version;

            # GIVE SERVICE
            if ($status == "VALID" || $status == "VALIDATED") {
                return $this->result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
