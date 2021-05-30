<?php

namespace App\Models\Scheduling;

use App\Models\Admin\Professional;
use App\Models\Admin\Service;
use App\Models\User;
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


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function customer_address()
    {
        return $this->hasOne(CustomerAddress::class, 'id', 'customer_address');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service');
    }

    public function reserve_day()
    {
        return $this->hasMany(ReserveDay::class, 'reserve', 'id');
    }

    public function professional()
    {
        return $this->hasOne(Professional::class, 'id', 'professional');
    }
}
