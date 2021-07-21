<?php

namespace App\Models\Admin;

use App\Models\Scheduling\Reserve;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory, LogsActivity;

     /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'service';

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
        return $this->hasOne(WorkingDay::class, 'id', 'working_day');
    }

    public function reserve()
    {
        return $this->hasMany(Reserve::class, 'service', 'id');
    }
}
