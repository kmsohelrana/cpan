<?php

namespace App\Models;

use Yajra\Oci8\Eloquent\OracleEloquent;

/**
 * Base Model for Oracle Database tables.
 *
 * Extend this class when creating models backed by an Oracle database.
 *
 * Example:
 *   class Customer extends OracleModel
 *   {
 *       protected $connection = 'oracle'; // or 'oracle2', 'oracle3'
 *       protected $table = 'CUSTOMERS';
 *       protected $primaryKey = 'CUSTOMER_ID';
 *       protected $sequence = 'CUSTOMERS_SEQ'; // optional Oracle sequence
 *   }
 */
abstract class OracleModel extends OracleEloquent
{
    /**
     * The connection name for the model.
     * Defaults to the primary Oracle connection.
     *
     * @var string|null
     */
    protected $connection = 'oracle';
}

