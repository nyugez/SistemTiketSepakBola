<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class AdminTicketCategoryController extends Controller
{
    public function index()
    {
        $categories = TicketCategory::with('footballMatch')->latest()->paginate(15);
        return view('admin.ticket-categories.index', compact('categories'));
    }

    public function create()
    {
        $matches = FootballMatch::where('status', 'scheduled')->orderBy('match_date')->get();
        return view('admin.ticket-categories.create', compact('matches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'football_match_id' => 'required|exists:football_matches,id',
            'name'              => 'required|string|max:100',
            'price'             => 'required|numeric|min:0',
            'quota'             => 'required|integer|min:1',
        ]);

        TicketCategory::create($data);
        return redirect()->route('admin.ticket-categories.index')->with('success', 'Kategori tiket berhasil ditambahkan.');
    }

    public function edit(TicketCategory $ticketCategory)
    {
        // Sertakan pertandingan milik kategori ini walau statusnya bukan 'scheduled'
        $matches = FootballMatch::where('status', 'scheduled')
            ->orWhere('id', $ticketCategory->football_match_id)
            ->orderBy('match_date')
            ->get();
        return view('admin.ticket-categories.edit', compact('ticketCategory', 'matches'));
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $data = $request->validate([
            'football_match_id' => 'required|exists:football_matches,id',
            'name'              => 'required|string|max:100',
            'price'             => 'required|numeric|min:0',
            'quota'             => 'required|integer|min:1',
        ]);

        $ticketCategory->update($data);
        return redirect()->route('admin.ticket-categories.index')->with('success', 'Kategori tiket berhasil diperbarui.');
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        if ($ticketCategory->tickets()->exists()) {
            return redirect()->route('admin.ticket-categories.index')
                ->with('error', 'Kategori tiket tidak dapat dihapus karena sudah ada tiket yang terjual.');
        }

        $ticketCategory->delete();
        return redirect()->route('admin.ticket-categories.index')->with('success', 'Kategori tiket berhasil dihapus.');
    }
}
