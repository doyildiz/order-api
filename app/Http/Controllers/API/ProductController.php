<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        try {
            $take = 5;
            $skip = 0;

            if ($request->get('page')){
                $skip = $take * ($request->get('page') - 1);
            }

            $products = Product::query()
                ->take($take)
                ->skip($skip)
                ->get();

            return response()->json(
                ProductResource::collection($products), 200
            );

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json($exception->getMessage(), 500);
        }
    }
}
