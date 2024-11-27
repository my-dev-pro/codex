<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasUuids;
    protected $fillable = [
        'result_path', 'note', 'test_id',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class);
    }


}
