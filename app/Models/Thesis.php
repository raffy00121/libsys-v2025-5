<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    use hasFactory;

    protected $casts = [
        'researchers' => 'array',
    ];

    protected $guarded = [];

    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class);
    }
}
