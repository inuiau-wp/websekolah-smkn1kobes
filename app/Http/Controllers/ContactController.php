<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\SchoolProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display the contact page with school info, form, WhatsApp, and map.
     */
    public function index(): View
    {
        $profil = SchoolProfile::query()->firstOrCreate([], [
            'school_name' => 'SMKN 1 Kotabesi',
        ]);

        return view('contact.index', compact('profil'));
    }

    /**
     * Store a visitor message and notify the school via email.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        ContactMessage::create($validated);

        // Notifikasi email ke sekolah (gagal kirim tidak boleh menggagalkan penyimpanan)
        $profil = SchoolProfile::query()->first(['email', 'school_name']);
        $recipient = $profil?->email ?: config('mail.from.address');
        $body = "Pesan baru dari website {$profil?->school_name}.\n\n"
            ."Nama: {$validated['name']}\n"
            ."Email: {$validated['email']}\n"
            .('Subjek: '.($validated['subject'] ?? '-')."\n")
            ."Isi:\n{$validated['message']}\n\n"
            .'Balas langsung ke: '.$validated['email'];

        try {
            Mail::raw($body, function ($mail) use ($validated, $recipient) {
                $mail->to($recipient)
                    ->subject('Pesan Baru Website: '.($validated['subject'] ?? 'Tanpa subjek'))
                    ->replyTo($validated['email'], $validated['name']);
            });
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Terima kasih! Pesan Anda telah terkirim. Kami akan merespons secepatnya.');
    }
}
