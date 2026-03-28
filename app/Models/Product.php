<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_type_id',
        'product_purpose',
        'product_quantity',
        'product_price',
        'selling_price',
        'purchase_price',
        'product_expiration_date',
        'last_updated_by',
        'vendor_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get selling price (harga jual) with fallback to product_price
     */
    public function getSellingPriceAttribute()
    {
        return $this->attributes['selling_price'] ?? $this->product_price;
    }

    /**
     * Get purchase price (harga beli) with fallback
     */
    public function getPurchasePriceAttribute()
    {
        return $this->attributes['purchase_price'] ?? $this->product_price;
    }
}
