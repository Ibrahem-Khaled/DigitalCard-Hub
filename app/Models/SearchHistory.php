<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query',
        'results_count',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'results_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the search history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
