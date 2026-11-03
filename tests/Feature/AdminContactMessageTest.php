<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_message_list_and_unread_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        ContactMessage::factory()->create(['name' => 'Orang Tua A', 'is_read' => false]);
        ContactMessage::factory()->create(['name' => 'Orang Tua B', 'is_read' => true]);

        $response = $this->actingAs($admin)->get(route('admin.kontak.index', ['unread_only' => 1]));

        $response->assertOk();
        $response->assertSee('Orang Tua A');
        $response->assertDontSee('Orang Tua B');
    }

    public function test_admin_can_mark_message_as_read(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = ContactMessage::factory()->create(['name' => 'Ahmad', 'is_read' => false]);

        $this->actingAs($admin)->patch(route('admin.kontak.read', $message))
            ->assertRedirect(route('admin.kontak.index'));

        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'is_read' => true]);
    }

    public function test_admin_can_delete_message(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = ContactMessage::factory()->create(['name' => 'Ahmad']);

        $this->actingAs($admin)->delete(route('admin.kontak.destroy', $message))
            ->assertRedirect(route('admin.kontak.index'));

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_non_admin_cannot_access_message_list(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('admin.kontak.index'))->assertNotFound();
        $this->actingAs($guru)->patch(route('admin.kontak.read', ContactMessage::factory()->create()))->assertNotFound();
        $this->actingAs($guru)->delete(route('admin.kontak.destroy', ContactMessage::factory()->create()))->assertNotFound();
    }
}
