<?php
  
namespace App\Http\Controllers;
  
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products =Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ->select('products.*','categories.name as category_name','categories.description as category_description')->paginate(10);
        
        return view('products.index',compact('products'))
                    ->with('i', (request()->input('page', 1) - 1) * 5);
    }
  
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('products.create',compact('categories'));
    }
  
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'category_id' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect("products/create")->withErrors($validator);
            }
            Product::create($request->all());
            
            return redirect()->route('products.index')
                            ->with('success','Product created successfully.');
        }catch (Exception $ex) {
            return redirect("products/create")->withErrors($ex->getMessage());
        }
    }
  
    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('products.show',compact('product'));
    }
  
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('products.edit',compact('product','categories'));
    }
  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'category_id' => 'required',
            ]);
            if ($validator->fails()) {
                    return redirect("products/".$product->id."/edit")->withErrors($validator);
            }
            $product->update($request->all());
            return redirect()->route('products.index')
                        ->with('success','Product updated successfully');
        }catch (Exception $ex) {
            return redirect("products/".$product->id."/edit")->withErrors($ex->getMessage());
        }
    }
  
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
         
        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }
}