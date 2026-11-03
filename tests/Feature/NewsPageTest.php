<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_page_can_be_rendered(): void
    {
        $news = News::factory()->create(['title' => 'Judul Berita Uji Coba']);

        $response = $this->get(route('news.index'));

        $response->assertOk();
        $response->assertSee('Judul Berita Uji Coba');
    }

    public function test_news_index_page_can_filter_by_category(): void
    {
        News::factory()->create(['title' => 'Berita Ujian', 'category' => 'news']);
        News::factory()->create(['title' => 'Pengumuman Ujian', 'category' => 'announcement']);

        $response = $this->get(route('news.index', ['category' => 'announcement']));

        $response->assertOk();
        $response->assertSee('Pengumuman Ujian');
        $response->assertDontSee('Berita Ujian');
    }

    public function test_news_detail_page_can_be_rendered(): void
    {
        $news = News::factory()->create(['title' => 'Detail Berita Uji Coba']);

        $response = $this->get(route('news.show', $news->slug));

        $response->assertOk();
        $response->assertSee('Detail Berita Uji Coba');
    }

    public function test_draft_news_is_hidden_from_public_pages(): void
    {
        $draft = News::factory()->create(['title' => 'Draf Tersembunyi', 'status' => 'draft']);
        News::factory()->create(); // berita published lain

        $this->get(route('news.index'))->assertOk()->assertDontSee('Draf Tersembunyi');
        $this->get(route('news.show', $draft->slug))->assertNotFound();
    }

    public function test_news_index_page_shows_empty_state_when_no_news(): void
    {
        $response = $this->get(route('news.index'));

        $response->assertOk();
        $response->assertSee('Belum ada konten');
    }
}
