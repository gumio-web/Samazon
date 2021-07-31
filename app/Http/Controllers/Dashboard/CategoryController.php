<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\MajorCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(15);
        $major_categories = MajorCategory::all();

        return view('dashboard.categories.index', compact('categories', 'major_categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->major_category_id = $request->major_category_id;
        $category->major_category_name = MajorCategory::find($request->major_category_id)->name;
        $category->save();

        //return redirect('/dashboard/categories');
        return redirect()->route('dashboard.categories.index');
    }

    public function show($id)
    {
        //
    }

    public function edit(Category $category)
    {
        $major_categories = MajorCategory::all();

        return view('dashboard.categories.edit', compact('category', 'major_categories'));
    }

    public function update(Request $request, Category $category)
    {
        $category->name = $request->name;
        $category->description = $request->description;
        $category->major_category_id = $request->major_category_id;
        $category->major_category_name = MajorCategory::find($request->major_category_id)->name;
        $category->update();

        //return redirect('/dashboard/categories');
        return redirect()->route('dashboard.categories.index');

    }

    public function destroy(Category $category)
    {
        $category->delete();

        //return redirect('/dashboard/categories');
        return redirect()->route('dashboard.categories.index');
    }
}
