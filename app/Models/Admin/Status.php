<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Status extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'status';

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

    public function professional()
    {
        return $this->hasMany(Professional::class, 'status', 'id');
    }
}
