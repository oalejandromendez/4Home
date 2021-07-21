<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkingDay extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'working_day';

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


    protected $guarded = [];


    protected static $logAttributes = ['*'];


    public function service_type()
    {
        return $this->hasOne(ServiceType::class, 'id', 'service_type');
    }

    public function service()
    {
        return $this->hasMany(Service::class, 'working_day', 'id');
    }
}
