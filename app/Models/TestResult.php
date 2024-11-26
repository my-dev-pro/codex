<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'result_path', 'note', 'test_id',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class);
    }


}
