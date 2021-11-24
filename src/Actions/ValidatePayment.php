<?php

namespace Hridoy\SslCommerz\Actions;

class ValidatePayment extends SslCommerz
{
    protected $trxId;
    protected $amount;
    protected $currency;
    protected $data;
    protected $result;

    public function __construct(string $trxId, int $amount, string $currency, array $data)
    {
        parent::__construct();
        $this->trxId = $trxId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->data = $data;
    }

    public function validate()
    {
        if ($this->trxId != "" && $this->amount != 0) {
            if ($this->hashVerify()) {

                $val_id = urlencode($this->data['val_id']);
                $store_id = urlencode($this->storeId);
                $store_passwd = urlencode($this->storePassword);
                $requested_url = $this->baseUrl.config('sslcommerz.validation_url') . "?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json";

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
                        if ($this->currency == "BDT") {
                            if (trim($this->trxId) == trim($tran_id) && (abs($this->amount - $amount) < 1) && trim($this->currency) == trim('BDT')) {
                                return $this->data;
                            } else {
                                return false;
                            }
                        } else {
                            if (trim($this->trxId) == trim($tran_id) && (abs($this->amount - $currency_amount) < 1) && trim($this->currency) == trim($currency_type)) {
                                return $this->data;
                            } else {
                                return false;
                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function hashVerify(): bool
    {
        if (isset($this->data) && isset($this->data['verify_sign']) && isset($this->data['verify_key'])) {
            $pre_define_key = explode(',', $this->data['verify_key']);
            $new_data = array();
            if (!empty($pre_define_key)) {
                foreach ($pre_define_key as $value) {
                    $new_data[$value] = ($this->data[$value]);
                }
            }
            $new_data['store_passwd'] = md5($this->storePassword);
            ksort($new_data);
            $hash_string = "";
            foreach ($new_data as $key => $value) {
                $hash_string .= $key . '=' . ($value) . '&';
            }
            $hash_string = rtrim($hash_string, '&');
            if (md5($hash_string) == $this->data['verify_sign']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
