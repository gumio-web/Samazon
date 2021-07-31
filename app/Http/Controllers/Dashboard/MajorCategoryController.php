<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\MajorCategory;
use Illuminate\Http\Request;

class MajorCategoryController extends Controller
{
    public function index()
    {
        $major_categories = MajorCategory::paginate(15);

        return view('dashboard.major_categories.index', compact('major_categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $major_category = new MajorCategory;
        $major_category->name = $request->name;
        $major_category->description = $request->description;
        $major_category->save();

        //return redirect('/dashboard/major_categories');
        return redirect()->route('dashboard.major_categories.index');
    }

    public function show(MajorCategory $major_category)
    {
        //
    }

    public function edit(MajorCategory $major_category)
    {
        return view('dashboard.major_categories.edit', compact('major_category'));
    }

    public function update(Request $request, MajorCategory $major_category)
    {
        $major_category->name = $request->name;
        $major_category->description = $request->description;
        $major_category->update();

        //return redirect('/dashboard/major_categories');
        return redirect()->route('dashboard.major_categories.index');
    }

    public function destroy(MajorCategory $major_category)
    {
        $major_category->delete();

        //return reditect('/dashboard/major_categories');
        return redirect()->route('dashboard.major_categories.index');
    }
}
