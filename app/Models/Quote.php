<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the quote
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the files for the quote
     */
    public function files(): HasMany
    {
        return $this->hasMany(QuoteFile::class);
    }

    /**
     * Calculate the total price from all files
     */
    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->files()->sum('calculated_price');
        $this->save();
    }
}
