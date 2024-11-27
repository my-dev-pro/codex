<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'telephone', 'mobile', 'email',
        'age', 'gender', 'address',
        'date_of_birth', 'nationality', 'national_id', 'created_by',
    ];

    public function tests(): HasMany
    {
        return $this->hasMany(TestRequest::class , 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->where('role', 'doctor');
    }


}
