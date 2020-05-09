<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Category;
use App\Client;
use App\Http\Controllers\Controller;
use App\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_orders'])->only('index');
        $this->middleware(['permission:create_orders'])->only(['create', 'store']);
        $this->middleware(['permission:update_orders'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_orders'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function create(Client $client)
    {
        //
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(10);
        return view('dashboard.clients.orders.create', compact('client', 'categories', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Client $client
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Client $client)
    {
        //
        $request->validate([
            'products' => 'required|array',
        ]);

        OrderController2::attach_order($request, $client);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @param Order $order
     * @param Client $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Client $client, Order $order)
    {
        //
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(10);
        return view('dashboard.clients.orders.edit', compact('order', 'client', 'categories', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Order $order
     * @param Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Client $client, Order $order)
    {
        //
        $request->validate([
            'products' => 'required|array',
        ]);

        OrderController2::detach_order($order);
        OrderController2::attach_order($request, $client);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order $order
     * @param Client $client
     * @return void
     */
    public function destroy(Order $order, Client $client)
    {
        //
    }

}
