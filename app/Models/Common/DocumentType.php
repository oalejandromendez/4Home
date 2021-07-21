<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentType extends Model
{
    use HasFactory, LogsActivity;

     /**
     * Tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'document_type';

    /**
     * LLave primaria del modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Atributos del modelo que no pueden ser asignados en masa.
     *
     * @var array
     */
    protected $guarded = [
        'name',
        'created_at',
        'updated_at'
    ];

    protected static $logAttributes = ['*'];

}
