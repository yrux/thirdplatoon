<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\{Product, EcommerceCategory, User, Order};
use Auth;
use DB;
class EcommerceController extends Controller
{
    public function index()
    {

        return Inertia::render('Ecommerce/panel',['isOnEcomPage'=>true]);
    }
    public function summary () {
        $productCount = Product::where('user_id',Auth::user()->id)->count();
        $categoryCount = EcommerceCategory::count();
        $vendorsCount = User::vendors()->count();
        $data=\collect(DB::select("SELECT MONTH(created_at) as month,SUM(orders.total) AS total_orders FROM orders GROUP BY MONTH(created_at)"));
        $payments=\collect(DB::select("SELECT payment_status,COUNT(*) AS total_orders FROM orders GROUP BY payment_status"));
        $result = [];
        for($i=0;$i<12;$i++){
            $monthName = date('F', mktime(0, 0, 0, ($i+1), 10));
            $result[$monthName] = $data->where('month',($i+1))->first();
        }
        // $ordersCount = Order::count();
        $ordersCount=Order::with('items')
        ->join('order_products','orders.id', '=', 'order_products.order_id')
        ->join('products','order_products.product_id', '=', 'products.id')
        ->where('products.user_id',Auth::user()->id)
        ->count();
        return response()->json([
            'productCount'=>$productCount,
            'categoryCount'=>$categoryCount,
            'vendorsCount'=>$vendorsCount,
            'ordersCount'=>$ordersCount,
            'user'=>Auth::user(),
            'month_sales'=>$result,
            'year'=>date('Y'),
            'payments'=>$payments
        ]);
    }
}
