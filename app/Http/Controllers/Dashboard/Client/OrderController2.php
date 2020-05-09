<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class OrderController2 extends Controller
{
    //
    public static function attach_order($request, $client)
    {
        $order = $client->orders()->create([]);
        $order->products()->attach($request->products);
        $total_price = 0;

        foreach ($request->products as $id => $quantity) {
            $product = Product::FindOrFail($id);
            $total_price += $product->sale_price * $quantity['quantity'];

            $product->update([
                'stock' => $product->stock - $quantity['quantity']
            ]);
        }

        $order->update([
            'total_price' => $total_price
        ]);
    }

    public static function detach_order($order)
    {
        foreach ($order->products as $product) {
            $product->update([
                'stock' => $product->stock + $product->pivot->quantity
            ]);
        }

        $order->delete();
    }
}
