<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceType extends Model
{
    use HasFactory, LogsActivity;

     /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'service_type';

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


    public function working_day()
    {
        return $this->hasMany(WorkingDay::class, 'service_type', 'id');
    }
}
