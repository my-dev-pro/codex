<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TestRequest extends Model
{
    use HasUuids;
    protected $fillable = [
        'name', 'note', 'status', 'is_paid', 'doctor_id', 'patient_id',
    ];

    public function doctorInfo(): HasOne
    {
        if ( Auth()->user()->role == 'doctor' ) {
            return $this->hasOne(User::class)->where(['id' => Auth()->user()->getAuthIdentifier()]);
        }
        return $this->hasOne(User::class)->where(['role' => 'doctor']);
    }

    public function doctorPatients(): HasOne
    {
        if (Auth()->user()->role == 'doctor') {
            return $this->hasOne(Patient::class)->where(['created_by' => Auth()->user()->getAuthIdentifier()]);
        }
        return $this->hasOne(Patient::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id')->where('role', 'doctor');
    }

    public function results(): HasOne
    {
        return $this->hasOne(TestResult::class, 'test_id', 'id');
    }

    public function notifications(): HasOne
    {
        return $this->hasOne(Notification::class, 'test_id', 'id');
    }
}
