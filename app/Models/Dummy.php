<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Dummy extends Model implements HasMedia
{
    use  HasSlug, InteractsWithMedia, HasTranslations;
    protected $fillable = [
        'dummy_name',
        'dummy_description',
        'dummy_image',
        'dummy_translations',
        'dummy_slug',
    ];

    public array $translatable = ['dummy_translations'];

    // ──────────────────────────────────────────
    // Sluggable
    // ──────────────────────────────────────────

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('dummy_slug');
    }

    public function getRouteKeyName(): string
    {
        return 'dummy_slug';
    }

    // ──────────────────────────────────────────
    // Media
    // ──────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('dummies')
            ->singleFile();
    }
}
