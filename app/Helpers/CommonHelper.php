<?php

namespace App\Helpers;

use App\Models\NpsbInstitution;
use Illuminate\Support\Facades\DB;

class CommonHelper
{
    public static function generateTransactionId(): string
    {
        try {
            DB::beginTransaction();
            $transaction_id = DB::select('call usp_generate_transaction_id("' . now()->toDateString() . '", @transaction_id)')[0]->_transaction_id;
            if (empty($transaction_id)) throw new \Exception("Failed to generate invoice number.");
            DB::commit();
            return $transaction_id;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception("Failed to generate invoice number.");
        }
    }

    public static function getNpsbInstitute($issuerCode): ?object
    {
        return NpsbInstitution::where('issuer_code', $issuerCode)->first();
    }
}
