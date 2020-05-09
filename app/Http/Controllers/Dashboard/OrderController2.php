<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;

class OrderController2 extends Controller
{
    //
    public static function products(Order $order){
        $products = $order->products;

        return view('dashboard.orders._products', compact('order', 'products'));
    }
}
