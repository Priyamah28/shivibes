<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateInquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $products = Product::count();
        $lowStock = Product::where('stock', '<=', 5)->count();
        $revenueMonth = (float) Order::whereMonth('created_at', now()->month)->sum('total_amount');
        $totalCustomers = User::count();
        $pendingInquiries = CorporateInquiry::where('status', 'pending')->count();

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'orders_today' => $ordersToday,
                'pending_orders' => $pendingOrders,
                'products' => $products,
                'low_stock' => $lowStock,
                'revenue_month' => $revenueMonth,
                'customers' => $totalCustomers,
                'pending_inquiries' => $pendingInquiries,
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}
