<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    protected $product;
    public function __construct(){
        $this->product = new product();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
      ]);


        $product = Product::create($validatedData);


        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);


        // Check if the product was found
       if ($product) {
      // Return the product as a JSON response with a 200 HTTP status code
           return response()->json($product, 200);
       } else {
   // Return a 404 Not Found HTTP status code if the product was not found
      return response()->json(['message' => 'Product not found'], 404);
   }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);



      // Check if the product was found
      if ($product) {

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
      ]);

      // Update the product with the validated data
      $product->update($validatedData);



        // Return the updated product as a JSON response with a 200 HTTP status code
         return response()->json($product, 200);
      } else {
        // Return a 404 Not Found HTTP status code if the product was not found
        return response()->json(['message' => 'Product not found'], 404);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $product = Product::find($id);

        if ($product) {
            // Delete the product
            $product->delete();


            // Return a 204 No Content HTTP status code
            return response()->json(null, 204);
          } else {
            // Return a 404 Not Found HTTP status code if the product was not found
            return response()->json(['message' => 'Product not found'], 404);
          }
    }
}