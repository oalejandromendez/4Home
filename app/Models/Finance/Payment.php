<?php

namespace App\Models\Finance;

use App\Models\Scheduling\Reserve;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'payment';

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
