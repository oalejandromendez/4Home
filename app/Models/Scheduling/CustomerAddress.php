<?php

namespace App\Models\Scheduling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerAddress extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'customer_address';

    /**
     * LLave primaria del modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;


    protected $fillable = [
        'user',
        'address'
    ];

    protected static $logAttributes = ['*'];

}
