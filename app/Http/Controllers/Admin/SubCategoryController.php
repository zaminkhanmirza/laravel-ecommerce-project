<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function create() {
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        // dd($categories);
        return view('admin.sub_category.create', $data);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
        ]);

        
        if ($validator->passes()) {
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->category_id = $request->category;
            $subCategory->status = $request->status;
            $subCategory->save();

            // if (!empty($request->image_id)) {
            //     $tempImage = TempImage::find($request->image_id);
            //     $extArray = explode('.', $tempImage->name);
            //     $ext = last($extArray);

            //     $newImageName = $category->id . '.' . $ext;
            //     $sourcePath = public_path() . '/temp/' . $tempImage->name;
            //     $destinationPath = public_path() . '/uploads/category/' . $newImageName;
            //     File::copy($sourcePath, $destinationPath);

            //     $category->image = $newImageName;
            //     $category->save();
            // }

            $request->session()->flash('success', 'Sub-Category added successfully');

            // event(new CategoryCreated(2));

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function index(Request $request)
    {
        $subCategories = SubCategory::latest();
        // dd($subCategories->category);
        $keyword = $request->get('keyword');

        if (!empty($keyword)) {
            $subCategories = $subCategories->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('slug', 'like', '%' . $keyword . '%');
        }

        $subCategories = $subCategories->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
            // echo"<pre>";
            // print_r($subCategories);
            // echo"</pre>";
            // dd($subCategories);


        return view('admin.sub_category.list', compact('subCategories'));
    }

    public function edit($subCategoryId)
    {
        $subCategory = SubCategory::find($subCategoryId);
        $categories = Category::all();
        if (empty($subCategoryId)) {
            return redirect()->route('categories.list')->with('error', 'Sub-Category not found');
        }
        return view('admin.sub_category.edit', compact('subCategory', 'categories'));
    }

    public function update($subCategoryId, Request $request)
    {
        $subCategory = SubCategory::find($subCategoryId);
        if (empty($subCategoryId)) {
            $request->session()->flash('error', 'Category not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found!'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'status' => 'required',
            'category' => 'required',
        ]);

        $request->validate([]);
        if ($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Sub-Category updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($subCategoryId, Request $request)
    {
        $subCategory = SubCategory::find($subCategoryId);
        if (empty($subCategory)) {
            $request->session()->flash('error', 'Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found'
            ]);
        }
        
        // File::delete(public_path() . '/uploads/category/' . $subCategory->image);

        $subCategory->delete();

        $request->session()->flash('success', 'Sub-Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub-Category Deleted Successfully'
        ]);
    }
}
