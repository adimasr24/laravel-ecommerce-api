<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index() {
        $products = Product::with('category')->when(request()->q, function($products) {
            $products = $products->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        return new ProductResource(true, 'list products', $products);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'title'         => 'required|unique:products',
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // create product
        $product = Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_domain')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'discount'      => $request->discount
        ]);

        if($product) {
            return new ProductResource(true, 'successfully added product', $product);
        }

        return new ProductResource(false, 'failed to add product', null);
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id) {
        $product = Product::whereId($id)->first();

        if($product) {
            return new ProductResource(true, 'detail product', $product);
        }

        return new ProductResource(false, 'detail product not found', null);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $product
     * @return void
     */
    public function update(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:products,title,'.$product->id,
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // check image update
        if($request->file('image')) {
            // remove old image
            Storage::disk('local')->delete('public/products/'.basename($product->image));
            
            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());
            
            // update product with new image
            $product->update([
                'image'         => $image->hashName(),
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'category_id'   => $request->category_id,
                'description'   => $request->weight,
                'price'         => $request->price,
                'stock'         => $request->stock,
                'discount'      => $request->discount
            ]);

            // update product without image
            $product->update([
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'category_id'   => $request->category_id,
                'user_id'       => auth()->guard('api_admin')->user()->id,
                'description'   => $request->description,
                'weight'        => $request->weight,
                'price'         => $request->price,
                'stock'         => $request->stock,
                'discount'      => $request->discount
            ]);

            if($product) {
                return new ProductResource(true, 'successfully updated product', $product);
            }
            
            return new ProductResource(false, 'failed to update product', null);
        }
        
    }
    
    /**
     * destroy
     *
     * @param  mixed $product
     * @return void
     */
    public function destroy(Product $product) {
        // remove old image
        Storage::disk('local')->delete('public/products/'.basename($product->image));

        if($product->delete()) {
            return new ProductResource(true, 'successfully deleted product', $product);
        }

        return new ProductResource(false, 'failed to delete product', null);
    }

}
