<?php

namespace App\Models;

use App\Models\Common\DocumentType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Scheduling\CustomerAddress;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_document',
        'identification',
        'name',
        'lastname',
        'email',
        'password',
        'age',
        'address',
        'phone',
        'mobile',
        'contact_name',
        'billing_address',
        'customer_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];

    public function type_document()
    {
        return $this->hasOne(DocumentType::class, 'id', 'type_document');
    }

    public function customer_address()
    {
        return $this->hasMany(CustomerAddress::class, 'user', 'id');
    }
}
