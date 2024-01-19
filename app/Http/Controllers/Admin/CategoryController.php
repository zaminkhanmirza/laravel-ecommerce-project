<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        $keyword = $request->get('keyword');

        if (!empty($keyword)) {
            $categories = $categories->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('slug', 'like', '%' . $keyword . '%');
        }

        $categories = $categories->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        $request->validate([]);
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sourcePath = public_path() . '/temp/' . $tempImage->name;
                $destinationPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sourcePath, $destinationPath);

                $category->image = $newImageName;
                $category->save();

                // $img = Image::make($sourcePath);
                // $img->resize(450, 600);
                // $dPath = public_path() . '/uploads/category/thumb' . $newImageName;
                // $img->save($dPath);
            }

            $request->session()->flash('success', 'Category added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit()
    {
        return view('admin.category.edit');
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
