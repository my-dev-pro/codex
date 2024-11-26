<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'telephone', 'mobile', 'email',
        'age', 'gender', 'address',
        'date_of_birth', 'nationality', 'national_id_number',
    ];

    public function tests(): HasMany
    {
        return $this->hasMany(TestRequest::class , 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
