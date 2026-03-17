<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductOutgoing extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'product_outgoings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_type_id',
        'product_purpose',
        'product_quantity',
        'product_each_price',
        'product_total_price',
        'customer_name',
        'last_updated_by',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
