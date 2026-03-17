<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductIncoming extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'product_incomings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_name',
        'product_code',
        'product_type_id',
        'product_purpose',
        'product_quantity',
        'product_each_price',
        'product_total_price',
        'vendor_name',
        'last_updated_by',
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
}
