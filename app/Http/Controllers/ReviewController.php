<?php

namespace App\Http\Controllers;

use App\Review;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $review = new Review;
        $review->content = $request->content;
        $review->product_id = $product->id;
        $review->user_id = Auth::id();
        $review->score = $request->score;
        $review->save();

        return redirect()->route('products.show', compact('product'));
    }
}
