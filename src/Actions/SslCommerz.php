<?php

namespace Hridoy\SslCommerz\Actions;

class SslCommerz
{
    protected $storeId;
    protected $storePassword;
    protected $baseUrl;
    protected $siteUrl;
    protected $successUrl;
    protected $failUrl;
    protected $cancelUrl;
    protected $ipnUrl;

    public function __construct()
    {
        $this->storeId = config('sslcommerz.store_id');
        $this->storePassword = config('sslcommerz.store_password');
        $this->baseUrl = config('sslcommerz.url.api');
        $this->siteUrl = config('sslcommerz.url.site');
        $this->successUrl = config('sslcommerz.url.success');
        $this->failUrl = config('sslcommerz.url.fail');
        $this->cancelUrl = config('sslcommerz.url.cancel');
        $this->ipnUrl = config('sslcommerz.url.ipn');
    }
}
