<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryCollection;
use App\Http\Requests\Category\CategoryStoreRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent');

        $categories = $this->filterAndResponse($request, $query);
        return (new CategoryCollection($categories))->response()->setStatusCode(Response::HTTP_OK);
    }


    public function store(CategoryStoreRequest $request)
    {
        $category = new Category();
        $category->title = $request->title;
        $category->parent_id = $request->parent_id != '' ? $request->parent_id : null;

        if ($category->save()) {
            return response()->json(['success' => 1, 'message' => 'Created successfully', 'category' => $category], 201);
        }
        return response()->json(['success' => 0, 'message' => 'Does not Created successfully'], 500);
    }

    public function show($id)
    {
        $category = Category::with('parent')->findOrFail($id);

        //        return (new SingleCategory($category))->response()->setStatusCode(Response::HTTP_OK);
        //        echo "<pre>";print_r($category->toArray());exit;
        return response()->json(['category' => $category], 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::with('parent')->findOrFail($id);

        $validator = Validator::make($request->only('title'), [
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Please fix these errors', 'errors' => $validator->errors()], 500);
        }

        $category->title = $request->title;
        $category->parent_id = $request->parent_id != '' ? $request->parent_id : null;
        if ($category->save()) {
            return response()->json(['success' => 1, 'message' => 'Updated successfully', 'category' => $category], Response::HTTP_OK);
        }

        return response()->json(['success' => 1, 'message' => 'Does not Updated successfully'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->delete()) {
            return response()->json(['success' => 1, 'message' => 'Deleted successfully'], Response::HTTP_OK);
        }
        return response()->json(['success' => 0, 'message' => 'Deleted done not successfull'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }



    public function getCategoryHtmlTree(Request $request, $parent_id = null)
    {
        // dd('sdfsdfs');
        $categories = Category::where('parent_id', $parent_id);

        if ($request->except_id) {
            $categories->where('id', '!=', $request->except_id)->get();
        }

        $categories = $categories->get();

        foreach ($categories as $category) {
            echo '<option value="' . $category->id . '">' . str_repeat('-', Category::getCategoryLevel($category->id)) . ' ' . $category->title . '</option>';

            if ($category->children->count() > 0) {
                $this->getCategoryHtmlTree($request, $category->id);
            }
        }
    }

    protected function filterAndResponse(Request $request, \Illuminate\Database\Eloquent\Builder $query)
    {
        if ($request->filter_by_id) {
            $query->where('id', $request->filter_by_id);
        }

        if ($request->filter_by_title) {
            $query->where('title', 'like', "%" . $request->filter_by_title . "%");
        }

        if ($request->filter_by_parent_id) {
            $query->where('parent_id', $request->filter_by_parent_id);
        }

        $categories = $query->paginate(5);
        return $categories;
    }
}
