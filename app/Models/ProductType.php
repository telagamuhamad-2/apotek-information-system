<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductType extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'product_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_type_name',
        'product_type_prefix'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_type_id');
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
