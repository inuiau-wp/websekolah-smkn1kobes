<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get(route('contact.index'));

        $response->assertOk();
        $response->assertSee('Kirim Pesan');
        $response->assertSee('Informasi Kontak');
    }

    public function test_visitor_can_send_a_message(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Pengirim Uji Coba',
            'email' => 'pengirim@example.com',
            'subject' => 'Pertanyaan PPDB',
            'message' => 'Halo, saya ingin bertanya tentang pendaftaran.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Pengirim Uji Coba',
            'email' => 'pengirim@example.com',
            'subject' => 'Pertanyaan PPDB',
            'is_read' => false,
        ]);
    }

    public function test_send_message_is_validated(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => '',
            'email' => 'bukan-format-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_message_is_stored_even_if_mail_fails(): void
    {
        Mail::shouldReceive('raw')->andThrow(new \Exception('SMTP server down'));

        $response = $this->post(route('contact.store'), [
            'name' => 'Pengirim Uji Coba',
            'email' => 'pengirim@example.com',
            'message' => 'Pesan tetap tersimpan meski email gagal.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertDatabaseCount('contact_messages', 1);
    }
}
