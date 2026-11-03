<?php

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'jurusan_id',
        'cover_image',
        'file_path',
        'file_original_name',
        'file_type',
        'author_id',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Slug dibuat otomatis dari judul (manual, tanpa paket tambahan)
        static::creating(function (News $news) {
            if (! $news->slug) {
                $news->slug = static::uniqueSlug($news->title);
            }
        });

        static::updating(function (News $news) {
            if (! $news->slug) {
                $news->slug = static::uniqueSlug($news->title);
            }
        });

        // Status published otomatis set tanggal tayang + sanitasi konten rich-text
        static::saving(function (News $news) {
            $news->content = static::sanitizeContent($news->content);

            if ($news->status === 'published' && ! $news->published_at) {
                $news->published_at = now();
            }
        });
    }

    private static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'berita';
        $slug = $base;
        $i = 1;

        while (static::withTrashed()->where('slug', $slug)->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    /**
     * Sanitasi konten rich-text (TinyMCE) sebelum disimpan:
     * hanya tag & atribut yang diizinkan yang diterima.
     */
    public static function sanitizeContent(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        $allowed = 'p|br|strong|b|em|i|u|s|ul|ol|li|h1|h2|h3|h4|h5|h6|blockquote|a|table|thead|tbody|tr|td|th|img|pre|code|span|div';

        // Buang script/style/iframe/event handler
        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*/?>#is', '', $html) ?? $html;

        // Hanya izinkan tag yang di-whitelist
        $html = preg_replace_callback('#(<\/?)([a-z0-9]+)((?:\s+[a-z-]+(?:="[^"]*"|=\'[^\']*\'|[^\s>])?)*)\s*(\/?)>#is', function ($m) use ($allowed) {
            if (! preg_match('/^('.$allowed.')$/i', $m[2])) {
                return '';
            }

            $attrs = '';
            if ($m[2] === 'a') {
                if (preg_match('/\bhref="([^"]*)"/i', $m[3], $href)) {
                    $url = $href[1];
                    if (preg_match('~^(https?://|mailto:|/|#)~i', $url)) {
                        $attrs .= ' href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener noreferrer"';
                    }
                }
            } elseif ($m[2] === 'img') {
                if (preg_match('/\bsrc="([^"]*)"/i', $m[3], $src) && preg_match('#^(https?://|/)#i', $src[1])) {
                    $attrs .= ' src="'.htmlspecialchars($src[1], ENT_QUOTES, 'UTF-8').'"';
                }
                if (preg_match('/\balt="([^"]*)"/i', $m[3], $alt)) {
                    $attrs .= ' alt="'.htmlspecialchars($alt[1], ENT_QUOTES, 'UTF-8').'"';
                }
                // ukuran (width/height) dari editor dipertahankan agar tampil sesuai pengaturan
                foreach (['width', 'height'] as $dimension) {
                    if (preg_match('/\b'.$dimension.'="(\d{1,4})"/i', $m[3], $value) && (int) $value[1] > 0) {
                        $attrs .= ' '.$dimension.'="'.$value[1].'"';
                    }
                }
            } elseif ($m[2] === 'blockquote') {
                // Fase 8h-r — embed post Instagram: bad minimal data & kategori instagram-media
                if (preg_match('/class="([^"]*)"/i', $m[3], $class) && str_contains($class[1], 'instagram-media')) {
                    $attrs .= ' class="instagram-media"';
                }
                if (preg_match('/data-instgrm-permalink="([^"]*)"/i', $m[3], $permalink)
                    && preg_match('~^https:\/\/(www\.)?instagram\.com\/(p|reel|reels)\/~i', $permalink[1])) {
                    $attrs .= ' data-instgrm-permalink="'.htmlspecialchars($permalink[1], ENT_QUOTES, 'UTF-8').'"';
                }
            }

            // Fase 8h-t — pertahankan perataan dari editor (dibersihkan dulu, hanya deklarasi aman)
            $attrs .= static::sanitizeStyle($m[3], $m[2]);

            return $m[1].$m[2].$attrs.$m[4].'>';
        }, $html) ?? $html;

        return trim($html);
    }

    /**
     * Bersihkan atribut style: hanya deklarasi yang aman yang dipertahankan
     * (perataan teks, float gambar, margin, ukuran).
     */
    private static function sanitizeStyle(string $attributeString, string $tag): string
    {
        if (! preg_match('/\bstyle="([^"]*)"/i', $attributeString, $style)) {
            return '';
        }

        $safe = [];
        foreach (explode(';', (string) $style[1]) as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === '') {
                continue;
            }

            // perataan teks (paragraf/heading/div), float & margin (gambar), width/height (segera)
            if (preg_match('/^text-align:\s*(left|center|right|justify)$/i', $declaration)) {
                $safe[] = strtolower($declaration);
            } elseif ($tag === 'img'
                && preg_match('/^(float:\s*(left|right)|(margin-left|margin-right|width|height):\s*(\d+(?:\.\d+)?)(px|%|em)|(display):\s*block|(margin-left|margin-right):\s*auto)$/i', $declaration, $css)) {
                $declaration = strtolower($declaration);
                // margin & display dipasangkan sesuai intent perataan gambar (auto = center, blok = center)
                $safe[] = $declaration;
            }
        }

        return $safe === [] ? '' : ' style="'.htmlspecialchars(implode('; ', $safe), ENT_QUOTES, 'UTF-8').'"';
    }

    /**
     * URL gambar cover yang siap dipakai <img>.
     * Data lama/seed yang berupa URL penuh dipakai apa adanya.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return str_starts_with($this->cover_image, 'http')
            ? $this->cover_image
            : asset('storage/'.$this->cover_image);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: hanya berita yang sudah tayang.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Daftar kategori berita/modul yang tersedia.
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'news' => 'Berita',
            'announcement' => 'Pengumuman',
            'agenda' => 'Agenda',
            'materi' => 'Modul Materi',
            'tutorial' => 'Tutorial',
        ];
    }

    /**
     * Scope: filter kategori (whitelist dari categories()).
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (! array_key_exists($category ?? '', static::categories())) {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Label kategori dalam bahasa Indonesia.
     */
    public function categoryLabel(): string
    {
        return static::categories()[$this->category] ?? 'Berita';
    }
}
