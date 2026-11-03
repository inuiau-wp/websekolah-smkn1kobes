<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Ambil satu nilai dengan default; hasil dibungkus cache sederhana.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $all = static::allCached();

        return $all[$key] ?? $default;
    }

    /**
     * Simpan (upsert) satu pasangan key-value.
     */
    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }

    /**
     * @return array<string, string>
     */
    public static function allCached(): array
    {
        return Cache::remember('settings.all', 600, fn (): array => static::query()
            ->pluck('value', 'key')
            ->all());
    }
}
