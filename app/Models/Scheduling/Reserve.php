<?php

namespace App\Models\Scheduling;

use App\Models\Admin\Professional;
use App\Models\Admin\Service;
use App\Models\Finance\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Reserve extends Model
{
    use HasFactory, LogsActivity;

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


    protected static $logAttributes = ['*'];


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

    public function supervisor()
    {
        return $this->hasOne(Professional::class, 'id', 'supervisor');
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'reserve', 'id');
    }

    /**
     * Obtiene las reservas del dia siguiente al actual para enviar por correo a los profesionales
     *
     * @var array
     */
    public static function reservesProfessionals($date, $day)
    {
        $query = self::query();
        $query->select('reserve.reference', DB::raw('CONCAT(users.name, " ", users.lastname) as client'),
            DB::raw('CONCAT( professional.name, " ", professional.lastname) as fullname'), 'professional.email', 'customer_address.address',
            'working_day.name as workingDay');
        $query->leftJoin('reserve_days', 'reserve.id', '=', 'reserve_days.reserve');
        $query->leftJoin('service', 'service.id', '=', 'reserve.service');
        $query->leftJoin('working_day', 'working_day.id', '=', 'service.working_day');
        $query->leftJoin('users', 'users.id', '=', 'reserve.user');
        $query->leftJoin('customer_address', 'customer_address.id', '=', 'reserve.customer_address');
        $query->leftJoin('professional', 'professional.id', '=', 'reserve.professional');
        $query->leftJoin('status', 'professional.status', '=', 'status.id');
        $query->where('reserve.status', 4);
        $query->where('status.openSchedule', 1);
        $query->whereRaw("(date = '".$date."' OR (date IS NULL AND day = ".$day."))");
        return $query->get();
    }
}
