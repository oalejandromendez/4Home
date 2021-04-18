<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

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


    public function working_day()
    {
        return $this->hasOne(WorkingDay::class, 'id', 'working_day');
    }
}
