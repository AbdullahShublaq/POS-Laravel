<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\Rules\PositiveNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_products'])->only('index');
        $this->middleware(['permission:create_products'])->only(['create', 'store']);
        $this->middleware(['permission:update_products'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_products'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $products = Product::when($request->search, function ($query) use ($request) {
            return $query->whereTranslationLike('name', '%' . $request->search . '%');
        })->when($request->category_id, function ($query) use ($request) {
            return $query->where('category_id', $request->category_id);
        })->latest()->paginate(10);

        $categories = Category::all();
        return view('dashboard.products.index')->with('products', $products)->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('dashboard.products.create')->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'category_id' => 'required',
            'image' => 'image',
            'purchase_price' => ['required', new PositiveNumber],
            'sale_price' => ['required', new PositiveNumber],
            'stock' => ['required', new PositiveNumber],
        ];
        foreach (config('translatable.locales') as $locale) {
            $rules += [$locale . '.name' => 'required|unique:product_translations,name'];
            $rules += [$locale . '.description' => 'required'];
        }
        $request->validate($rules);

        $request_data = $request->except(['image']);
        if ($request->image) {
            Image::make($request->image)->resize(300, NULL, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/uploads/product_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        Product::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
        $categories = Category::all();
        return view('dashboard.products.edit')->with('product', $product)->with('categories', $categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
        //
        $rules = [
            'category_id' => 'required',
            'image' => 'image',
            'purchase_price' => ['required', new PositiveNumber],
            'sale_price' => ['required', new PositiveNumber],
            'stock' => ['required', new PositiveNumber],
        ];
        foreach (config('translatable.locales') as $locale) {
            $rules += [$locale . '.name' => ['required', Rule::unique('product_translations', 'name')->ignore($product->id, 'product_id')]];
            $rules += [$locale . '.description' => 'required'];
        }

        $request->validate($rules);

        $request_data = $request->except(['image']);
        if ($request->image) {
            if ($product->image != 'default.png') {
                Storage::disk('public_uploads')->delete('/product_images/' . $product->image);
            }

            Image::make($request->image)->resize(300, NULL, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/uploads/product_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        $product->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        //
        if ($product->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/product_images/' . $product->image);
        }
        $product->delete();

        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.products.index');


    }
}
