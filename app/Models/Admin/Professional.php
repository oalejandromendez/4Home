<?php

namespace App\Models\Admin;

use App\Models\Scheduling\Reserve;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Professional extends Model
{
    use LogsActivity;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'professional';

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

    /**
     * Atributos del modelo que no pueden ser asignados en masa.
     *
     * @var array
     */
    protected $guarded = [];


    protected static $logAttributes = ['*'];


    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position');
    }

    public function reserve()
    {
        return $this->hasMany(Reserve::class, 'professional', 'id');
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status');
    }


}
