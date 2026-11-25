<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use Illuminate\Http\Request;

class AdminStadiumController extends Controller
{
    public function index()
    {
        $stadiums = Stadium::withCount('footballMatches')->latest()->paginate(10);
        return view('admin.stadiums.index', compact('stadiums'));
    }

    public function create()
    {
        return view('admin.stadiums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'city'     => 'required|string|max:100',
            'address'  => 'required|string',
            'capacity' => 'required|integer|min:100',
        ]);

        Stadium::create($data);
        return redirect()->route('admin.stadiums.index')->with('success', 'Stadion berhasil ditambahkan.');
    }

    public function edit(Stadium $stadium)
    {
        return view('admin.stadiums.edit', compact('stadium'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'city'     => 'required|string|max:100',
            'address'  => 'required|string',
            'capacity' => 'required|integer|min:100',
        ]);

        $stadium->update($data);
        return redirect()->route('admin.stadiums.index')->with('success', 'Stadion berhasil diperbarui.');
    }

    public function destroy(Stadium $stadium)
    {
        if ($stadium->footballMatches()->exists()) {
            return redirect()->route('admin.stadiums.index')
                ->with('error', 'Stadion tidak dapat dihapus karena masih dipakai oleh pertandingan.');
        }

        $stadium->delete();
        return redirect()->route('admin.stadiums.index')->with('success', 'Stadion berhasil dihapus.');
    }
}
