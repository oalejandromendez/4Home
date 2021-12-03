<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Novelty extends Model
{
    use LogsActivity, HasFactory;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'novelties';

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


    public function professional()
    {
        return $this->hasOne(Professional::class, 'id', 'professional');
    }

}
