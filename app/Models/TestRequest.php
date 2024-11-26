<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TestRequest extends Model
{
    protected $fillable = [
        'name', 'note', 'status', 'is_paid', 'doctor_id', 'patient_id',
    ];

    public function doctor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'id', 'patient_id');
    }

}
