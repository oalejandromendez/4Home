<?php

namespace App\Models\Scheduling;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerType extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'customer_type';

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


    public function user()
    {
        return $this->hasMany(User::class, 'customer_type', 'id');
    }

}
