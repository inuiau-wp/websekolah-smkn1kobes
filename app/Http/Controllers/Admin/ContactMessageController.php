<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Daftar semua pesan kontak dari pengunjung website.
     */
    public function index(Request $request): View
    {
        $messages = ContactMessage::query()
            ->when($request->boolean('unread_only'), fn ($q) => $q->where('is_read', false))
            ->orderBy('is_read')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
        ];

        return view('admin.kontak.index', compact('messages', 'stats'));
    }

    /**
     * Tandai pesan sudah dibaca.
     */
    public function markRead(Request $request, ContactMessage $kontak): RedirectResponse
    {
        $kontak->update(['is_read' => true]);

        return redirect()->route('admin.kontak.index')->with('status', 'Pesan dari '.$kontak->name.' ditandai sudah dibaca.');
    }

    /**
     * Hapus pesan kontak.
     */
    public function destroy(ContactMessage $kontak): RedirectResponse
    {
        $name = $kontak->name;
        $kontak->delete();

        return redirect()->route('admin.kontak.index')->with('status', 'Pesan dari '.$name.' berhasil dihapus.');
    }
}
