<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\Request;

class FootballMatchController extends Controller
{
    /**
     * Menampilkan daftar semua pertandingan sepak bola yang dijadwalkan (public)
     */
    public function index()
    {
        $matches = FootballMatch::with('stadium')
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->paginate(9);

        return view('matches.index', compact('matches'));
    }

    /**
     * Menampilkan detail satu pertandingan spesifik beserta relasi kategori tiket dan stadion
     *
     * @param int|string $id ID pertandingan
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Mengambil data pertandingan spesifik beserta relasi stadion dan kategori tiket
        $match = FootballMatch::with([
            'stadium',
            'ticketCategories' => function ($query) {
                $query->orderBy('price', 'asc');
            },
        ])->findOrFail($id);

        return view('matches.show', compact('match'));
    }
}
