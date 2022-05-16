<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index () {
        // get categories
        $categories = Category::when(request()->q, function($categories){
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        return new CategoryResource(true, 'list categories', $categories);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'  => 'required|unique:categories',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // create category
        $category = Category::create([
            'image' => $image->hashName(),
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-'),
        ]);

        if($category) {
            return new CategoryResource(true, 'successfully added category', $category);
        }

        return new CategoryResource(false, 'failed to add category', null);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $category = Category::whereId($id)->first();

        if($category) {
            return new CategoryResource(true, 'detail data category', $category);
        }

        return new CategoryResource(false, 'detail data category not found', null);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category) {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|unique:categories,name,'.$category->id,
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->file('image')) {
            // remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // update category with new image
            $category->update([
                'image' => $image->hashName(),
                'name'  => $request->name,
                'slug'  => Str::slug($request->name, '-'),
            ]);
        }

        // update category without image
        $category->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-'),
        ]);

        if($category) {
            return new CategoryResource(true, 'successfully updated category', $category);
        }

        return new CategoryResource(false, 'failed to update category', null);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category) {
        // remove image
        Storage::disk('local')->delete('public/categories/.basename($category->image)');
        
        if($category->delete()) {
            return new CategoryResource(true, 'successfully deleted category', null);
        }

        return new CategoryResource(false, 'failed to delete category', null);
    }
}
