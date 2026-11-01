UPDATE transactions t
JOIN npsb_request_responses r
    ON r.rrn = t.rrn
   AND r.acquirer_institution_id = t.institution_code
SET t.merchant_mcc = r.merchant_mcc
WHERE (t.merchant_mcc IS NULL OR t.merchant_mcc = '')
  AND r.merchant_mcc IS NOT NULL
  AND r.merchant_mcc <> '';

-- Widen transactions money columns from 2/3 to 4 decimal digits so acquiring/
-- store/SSL portions calculated from commission rates (ReceivingTransactionService::
-- calculateCommission) stop losing precision at capture time.
ALTER TABLE `transactions`
    MODIFY COLUMN `amount` DECIMAL(15,4) NOT NULL DEFAULT 0,
    MODIFY COLUMN `tp_portion` DECIMAL(15,4) NOT NULL DEFAULT 0,
    MODIFY COLUMN `acquiring_portion` DECIMAL(15,4) NOT NULL DEFAULT 0,
    MODIFY COLUMN `store_portion` DECIMAL(15,4) NOT NULL DEFAULT 0,
    MODIFY COLUMN `refund_amount` DECIMAL(15,4) NULL DEFAULT NULL,
    MODIFY COLUMN `chargeback_amount` DECIMAL(15,4) NULL DEFAULT NULL;

-- New permission: lead.change_assigned_officer (Change Assigned Officer for a Lead)
insert into permissions (name, guard_name, module_name, platform, created_at, updated_at)
    values ('lead.change_assigned_officer', 'web', 'Lead', 'web', now(), now());
