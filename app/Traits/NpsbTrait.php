<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use App\Helpers\LogHelper;
use App\Models\NpsbRequestResponse;
use App\Models\Pan;

trait NpsbTrait
{
    /**
     * Validation rules for NPSB transaction notification
     */
    public function saveRequestResponse($request, $transactionType)
    {
        return NpsbRequestResponse::create([
            'request_id' => $request->requestID,
            'incoming_ip' => LogHelper::incomingIp(),
            'local_transaction_time' => $request->localTransactionTime,
            'local_transaction_date' => $request->localTransactionDate,
            'transmission_date_time' => $request->transmissionDateTime,
            'rrn' => $request->retrievalReferenceNumber,
            'system_trace_audit_number' => $request->systemTraceAuditNumber,
            'pos_entry_mode' => $request->posEntryMode,
            'transaction_serial_number' => $request->transactionSerialNumber ?? null,
            'acquirer_institution_id' => $request->acquirerInstitutionID,
            'amount' => $request->transactionAmount,
            'fee_amount' => $request->transactionFeeAmount ?? 0,
            'transaction_currency_code' => $request->transactionCurrencyCode,
            'transaction_type' => $transactionType,
            'pan' => $request->consumerPAN,
            'consumer_name' => $request->consumerName ?? null,
            'npsb_mid' => $request->merchantID,
            'merchant_name' => $request->merchantName,
            'merchant_city' => $request->merchantCity,
            'merchant_country_code' => $request->merchantCountryCode,
            'merchant_mcc' => $request->merchantMCC,
            'additional_data' => $request->additionalData ?? null,
            'request' => json_encode($request->all()),
        ]);
    }

    public function findPanByMerchantId($merchantID)
    {
        $pan = Pan::where('tag_27', $merchantID)
            ->where('status', StatusEnum::ACTIVE->value)
            ->where('is_default', true)
            ->first();
        return $pan;
    }

    public function generatePayload($localBankData)
    {
        $data = [
            'request_id' => $localBankData->requestID ?? null,
            'transaction_time' => $localBankData->localTransactionTime,
            'transaction_date' => $localBankData->localTransactionDate,
            'rrn' => $localBankData->retrievalReferenceNumber,
            'stan' => $localBankData->systemTraceAuditNumber ?? '000000',
            'transaction_ref_number' => $localBankData->bankTransactionID,
            'transaction_amount' => $localBankData->transactionAmount,
            'fee_amount' => $localBankData->transactionFeeAmount ?? 0,
            'currency_code' => $localBankData->transactionCurrencyCode ?? null,
            'consumer_pan' => $localBankData->consumerPAN,
            'consumer_name' => $localBankData->consumerName,
            'consumer_phone_number' => $localBankData->consumerPhoneNumber,
            'merchant_mcc' => $localBankData->merchantMCC,
            'store_id' => $localBankData->store_id,
            'store_label' => $localBankData->tag_62_first_data ?? null,
            'terminal_label' => $localBankData->tag_62_second_data ?? null,
            'order_id' => $localBankData->tag_62_bill_number_data ?? null,
            'country_code' => '880',
            'issuer_bank' => $localBankData->acquirerInstitutionID ?? null,
            'channel' => $localBankData->bank->swift_code ?? authorBank('swift_code'),
            'network_id' => $localBankData->network_id ?? null,
            'transaction_type' => $localBankData->transaction_type ?? null,
            'pan' => $localBankData->merchantID,
        ];
        return (object)$data;
    }


    public function parseStoreAndTerminalFromAdditional($additionalData)
    {
        $additionalData = json_decode($additionalData, true);
        $billNumber = $additionalData['01'] ?? null;
        $storeLabel = $additionalData['03'] ?? null;
        $terminalLabel = $additionalData['07'] ?? null;
        return [$storeLabel, $terminalLabel, $billNumber];
    }

    public function client()
    {
        return auth('bankClient')->user();
    }
}
