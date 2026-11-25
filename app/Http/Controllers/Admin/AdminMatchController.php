<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Stadium;
use Illuminate\Http\Request;

class AdminMatchController extends Controller
{
    public function index()
    {
        $matches = FootballMatch::with('stadium')->latest()->paginate(10);
        return view('admin.matches.index', compact('matches'));
    }

    public function create()
    {
        $stadiums = Stadium::orderBy('name')->get();
        return view('admin.matches.create', compact('stadiums'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'home_team'  => 'required|string|max:100',
            'away_team'  => 'required|string|max:100',
            'stadium_id' => 'required|exists:stadiums,id',
            'match_date' => 'required|date|after:now',
            'status'     => 'required|in:scheduled,ongoing,finished,cancelled',
        ]);

        FootballMatch::create($data);
        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil ditambahkan.');
    }

    public function edit(FootballMatch $match)
    {
        $stadiums = Stadium::orderBy('name')->get();
        return view('admin.matches.edit', compact('match', 'stadiums'));
    }

    public function update(Request $request, FootballMatch $match)
    {
        $data = $request->validate([
            'home_team'  => 'required|string|max:100',
            'away_team'  => 'required|string|max:100',
            'stadium_id' => 'required|exists:stadiums,id',
            'match_date' => 'required|date',
            'status'     => 'required|in:scheduled,ongoing,finished,cancelled',
        ]);

        $match->update($data);
        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil diperbarui.');
    }

    public function destroy(FootballMatch $match)
    {
        if ($match->ticketCategories()->exists()) {
            return redirect()->route('admin.matches.index')
                ->with('error', 'Pertandingan tidak dapat dihapus karena sudah memiliki kategori tiket. Hapus kategori tiketnya terlebih dahulu.');
        }

        $match->delete();
        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil dihapus.');
    }
}
