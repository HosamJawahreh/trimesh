<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingOrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'printing_order_id',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_province',
        'billing_city',
        'billing_zip_code',
        'billing_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_province',
        'shipping_city',
        'shipping_zip_code',
        'shipping_country',
    ];

    /**
     * Get the printing order that owns the address
     */
    public function printingOrder()
    {
        return $this->belongsTo(PrintingOrder::class);
    }
}
