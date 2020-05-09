<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_categories'])->only('index');
        $this->middleware(['permission:create_categories'])->only(['create', 'store']);
        $this->middleware(['permission:update_categories'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_categories'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Category
     */
    public function index(Request $request)
    {
        //
        $categories = Category::when($request->search, function ($query) use ($request){
            return $query->whereTranslationLike('name', '%'. $request->search . '%');
        })->latest()->paginate(10);

        return view('dashboard.categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('dashboard.categories.create');
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
        $rules = [];
        foreach (config('translatable.locales') as $locale){
            $rules += [$locale . '.name' => ['required', Rule::unique('category_translations','name')]];
        }
        $request->validate($rules);

        Category::create($request->all());

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.categories.index');
    }

//    /**
//     * Display the specified resource.
//     *
//     * @param  \App\Category $category
//     * @return \Illuminate\Http\Response
//     */
//    public function show(Category $category)
//    {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
        return view('dashboard.categories.edit')->with('category', $category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        $rules = [];
        foreach (config('translatable.locales') as $locale){
            $rules += [$locale . '.name' => [
                'required',
                Rule::unique('category_translations','name')->ignore($category->id, 'category_id')
            ]];
        }
        $request->validate($rules);

        $category->update($request->all());

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        //
        $category->delete();

        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.categories.index');
    }
}
