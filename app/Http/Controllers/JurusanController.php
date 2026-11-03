<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;

class JurusanController extends Controller
{
    /**
     * Display a listing of the available study programs.
     */
    public function index()
    {
        $jurusan = Jurusan::all();

        return view('jurusan.index', compact('jurusan'));
    }

    /**
     * Display the study program detail page.
     */
    public function show(Jurusan $jurusan)
    {
        return view('jurusan.show', compact('jurusan'));
    }
}
