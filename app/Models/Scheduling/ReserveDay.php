<?php

namespace App\Models\Scheduling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ReserveDay extends Model
{
    use HasFactory, LogsActivity;


    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'reserve_days';

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


    public function reserve()
    {
        return $this->hasOne(Reserve::class, 'id', 'reserve');
    }
}
