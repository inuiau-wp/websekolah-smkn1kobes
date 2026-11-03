<?php

namespace App\Http\Controllers\Concerns;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Fase 8h-p — lampiran materi pada berita:
 * - .md   → isinya dikonversi jadi HTML (isi textarea/`content`), file TIDAK disimpan.
 *           (JS tidak aktif/fallback: server-side pakai league/commonmark, GFM.)
 * - .pdf  → disimpan; halaman publik menampilkan embed.
 * - .docx → disimpan; halaman publik menampilkan tombol unduh.
 */
trait HandlesNewsAttachment
{
    protected function applyAttachment(Request $request, News $news): void
    {
        if ($request->boolean('remove_attachment')) {
            $this->deleteAttachment($news);
            $news->file_path = null;
            $news->file_original_name = null;
            $news->file_type = null;
        }

        if (! $request->hasFile('attachment')) {
            return;
        }

        $file = $request->file('attachment');
        $extension = strtolower((string) $file->getClientOriginalExtension());

        // File Markdown: dibaca, dikonversi jadi HTML untuk isi berita — tidak disimpan
        if ($extension === 'md' || $extension === 'markdown') {
            $converter = new GithubFlavoredMarkdownConverter(['html_input' => 'strip']);
            $news->content = $converter->convert((string) $file->getContent())->getContent();

            return;
        }

        // PDF / DOCX: disimpan sebagai lampiran (mengganti lampiran lama)
        $this->deleteAttachment($news);
        $news->file_path = $file->store('news/files', 'public');
        $news->file_original_name = $file->getClientOriginalName();
        $news->file_type = $extension;
    }

    protected function deleteAttachment(News $news): void
    {
        if ($news->file_path) {
            Storage::disk('public')->delete($news->file_path);
        }
    }
}
