<?php

namespace App\Models\Scheduling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveDay extends Model
{
    use HasFactory;


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


    public function reserve()
    {
        return $this->hasOne(Reserve::class, 'id', 'reserve');
    }
}
