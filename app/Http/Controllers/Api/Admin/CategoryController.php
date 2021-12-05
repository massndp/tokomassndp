<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        return new CategoryResource(true, 'List data category', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        //create
        $category = Category::create([
            'name' => $request->name,
            'image' => $image->hashName(),
            'slug' => Str::slug($request->name, '-')
        ]);

        return new CategoryResource(true, 'Data category berhasil dibuat', $category);
    }

    public function show($id)
    {
        $category = Category::whereId($id)->first();
        if ($category) {
            return new CategoryResource(true, 'Detail data category', $category);
        }

        return new CategoryResource(false, 'Detail data category tidak ditemukan ', null);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //chek image update
        if ($request->file('image')) {
            //remove old image
            $category = Category::find($id);
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            //update category with new image
            $category = Category::find($id)->update([
                'name' => $request->name,
                'image' => $image->hashName(),
                'slug' => Str::slug($request->name, '-')
            ]);
        }

        //update without image
        $category = Category::find($id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data category berhasil diupdate', $category);
        }

        return new CategoryResource(false, 'Data category gagal diupdate', null);
    }

    public function destroy(Category $category)
    {
        //remove image
        Storage::disk('local')->delete('public/categories/' . basename($category->image));

        if ($category->delete()) {
            return new CategoryResource(true, 'Data category berhasil dihapus', null);
        }

        return new CategoryResource(false, 'Data category gagal dihapus', null);
    }
}
