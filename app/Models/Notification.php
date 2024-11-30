<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'receiver', 'is_sent', 'test_id',
    ];

    protected $casts = [
        'receiver' => 'array',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class , 'test_id');
    }
}
