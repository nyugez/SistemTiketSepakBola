<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'   => User::where('role', 'user')->count(),
            'total_matches' => FootballMatch::count(),
            'total_orders'  => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_price'),
            'pending_orders'=> Order::where('payment_status', 'pending')->count(),
            'paid_orders'   => Order::where('payment_status', 'paid')->count(),
            'banned_users'  => User::where('role', 'user')->where('is_banned', true)->count(),
        ];

        $latestOrders = Order::with(['user', 'tickets.ticketCategory.footballMatch'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingMatches = FootballMatch::with('stadium')
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestOrders', 'upcomingMatches'));
    }
}
