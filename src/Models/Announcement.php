<?php

namespace Rupadana\FilamentAnnounce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;


class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'custom_color',
        'title',
        'body',
        'icon',
        'users',
    ];

    protected $casts = [
        'users' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
