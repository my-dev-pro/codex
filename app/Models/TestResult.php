<?php

namespace App\Models;

use App\Observers\ResultObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ResultObserver::class)]
class TestResult extends Model
{
    use HasUuids;
    protected $fillable = [
        'result_path', 'note', 'test_id',
    ];

    protected $casts = [
        'result_path' => 'string',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class, 'test_id');
    }


}
