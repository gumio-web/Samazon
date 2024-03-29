<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Product;

class WebController extends Controller
{
    public function index()
    {
        $categories = Category::all()->sortBy('major_category_name');
        $major_category_names = Category::pluck('major_category_name')->unique();
        $recent_products = Product::orderBy('created_at', 'desc')->take(4)->get();
        $recommended_products = Product::where('recommend_flag', true)->take(3)->get();

        return view('web.index', compact('major_category_names', 'categories', 'recent_products', 'recommended_products'));
    }
}
