<?php

namespace App\Models\Scheduling;

use App\Models\Admin\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    use HasFactory;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'reserve';

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


    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service');
    }
}
