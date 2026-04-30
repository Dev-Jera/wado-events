<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSettings extends Model
{
    protected $table = 'homepage_settings';

    protected $fillable = [
        'featured_count',
        'trending_count',
        'upcoming_count',
        'trending_days',
        'show_featured',
        'show_trending',
        'show_upcoming',
        'empty_heading',
        'empty_sub',
    ];

    protected $casts = [
        'show_featured'   => 'boolean',
        'show_trending'   => 'boolean',
        'show_upcoming'   => 'boolean',
        'featured_count'  => 'integer',
        'trending_count'  => 'integer',
        'upcoming_count'  => 'integer',
        'trending_days'   => 'integer',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'featured_count' => 4,
            'trending_count' => 4,
            'upcoming_count' => 8,
            'trending_days'  => 14,
            'show_featured'  => true,
            'show_trending'  => true,
            'show_upcoming'  => true,
            'empty_heading'  => 'No upcoming events right now',
            'empty_sub'      => 'We\'re working on bringing new events. Check back soon.',
        ]);
    }
}
