<?php

namespace App\Helpers;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransHelper
{
    public static function createPaymentUrl(array $params): string
    {

        $gtex= gtext();


        // Set konfigurasi Midtrans dari config
        Config::$serverKey = $gtex['midtrans_server_key'];
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat transaksi Snap
        $transaction = Snap::createTransaction($params);

        // Return URL untuk redirect user ke halaman pembayaran
        return $transaction->redirect_url;
    }
}
