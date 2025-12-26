<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintingOrder extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'quote_id',
        'viewer_type',
        'viewer_link',
        'total_price',
        'total_volume',
        'total_files',
        'technology',
        'material',
        'color',
        'quality',
        'files_data',
        'shipping_method',
        'shipping_charge',
        'payment_method',
        'payment_status',
        'payment_details',
        'customer_note',
        'status',
        'notes'
    ];

    protected $casts = [
        'files_data' => 'array',
        'payment_details' => 'array',
        'total_price' => 'decimal:2',
        'total_volume' => 'decimal:2',
        'shipping_charge' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function address()
    {
        return $this->hasOne(PrintingOrderAddress::class);
    }

    public static function generateOrderNumber(): string
    {
        return 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
