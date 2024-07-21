<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ImageTrait,ResponseTrait;
    protected CategoryService $categoryService;

    // Injecting the CategoryService
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:admin,access']);
        $this->categoryService = new CategoryService();
    }
    public function create(Request $request){
        // Validate the incoming request data
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'min:3', 'max:20','unique:categories,name'],
            'image' => ['image','mimes:jpeg,jpg,png,svg','max:1000'],
            'description' => ['string', 'max:255']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());
        $this->categoryService->createCategory($request->all());
        return $this->successResponse();
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:categories,id,deleted_at,NULL'],
            'name'=>['required','string', 'unique:categories,name,' . $request->id],
            'image' => ['image','mimes:jpeg,jpg,png,svg','max:1000'],
            'description' => ['required','string', 'max:255']
        ]) ;
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());
        $this->categoryService->updateCategory($request->all());
        return $this->successResponse();
    }

    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:categories,id,deleted_at,NULL']
        ]) ;
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());
        try {
            $this->categoryService->deleteCategory($request->id);
            return $this->successResponse();
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
    }

    public function index(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                'name' => ['nullable','string'],
            ]
        );
        if ($validate->fails())
            return $this->failedResponse($validate->errors()->first());

        $categories = $this->categoryService->getCategories($request->only('name'));
        return $this->successResponse($categories);
    }

    public function changeActive(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:categories,id,deleted_at,NULL']
        ]) ;
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());
        $this->categoryService->changeActive($request->only('id'));
        return $this->successResponse();
    }
}
