<?php

namespace Hridoy\SslCommerz\Contracts;

use Illuminate\Http\Request;

interface SslCommerzContract
{
    public function initiate(Request $request);

    public function validate(Request $request, string $trxId, int $amount, string $currency);
}
