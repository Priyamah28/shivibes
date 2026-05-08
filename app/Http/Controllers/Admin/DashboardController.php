<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $products = Product::count();
        $revenue = (float) Order::sum('total_amount');

        return view('admin.dashboard', [
            'stats' => [
                'orders_today' => $ordersToday,
                'pending_orders' => $pendingOrders,
                'products' => $products,
                'revenue_month' => $revenue,
            ],
        ]);
    }
}
