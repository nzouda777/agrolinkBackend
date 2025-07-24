<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category', 'variants', 'images']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('featured')) {
            $query->where('featured', $request->featured);
        }
        // return product with its category
        $query->with('category');
        return $query->paginate(20);
    }

    public function show($id)
    {
        return Product::with(['seller', 'category', 'variants', 'images', 'reviews'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,pending,rejected',
            'featured' => 'boolean',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Créer le produit
        $product = Product::create([
            'seller_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'old_price' => $request->old_price,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'featured' => 1,
        ]);

        // Gérer les images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => $path,
                    'sort_order' => $index + 1,
                    'is_main' => $index === 0 // La première image est l'image principale
                ]);
            }
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('images')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update this product'
            ], 403);
        }

        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,pending,rejected',
            'featured' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        $product->update([
            'seller_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'old_price' => $request->old_price,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'featured' => true,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this product'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    public function uploadImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to upload image'
            ], 403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        $image = ProductImage::create([
            'product_id' => $id,
            'image_url' => $imagePath,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image
        ]);
    }

    public function deleteImage($productId, $imageId)
    {
        $product = Product::findOrFail($productId);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete image'
            ], 403);
        }

        $image = ProductImage::findOrFail($imageId);
        
        // Delete the file from storage
        \Storage::delete($image->image_url);
        
        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }

    public function createVariant(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to create variant'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
        ]);

        return response()->json([
            'message' => 'Variant created successfully',
            'variant' => $variant
        ]);
    }

    public function updateVariant(Request $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update variant'
            ], 403);
        }

        $variant = ProductVariant::findOrFail($variantId);

        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
        ]);

        $variant->update($request->all());

        return response()->json([
            'message' => 'Variant updated successfully',
            'variant' => $variant
        ]);
    }

    public function deleteVariant($productId, $variantId)
    {
        $product = Product::findOrFail($productId);

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete variant'
            ], 403);
        }

        $variant = ProductVariant::findOrFail($variantId);
        $variant->delete();

        return response()->json([
            'message' => 'Variant deleted successfully'
        ]);
    }

    public function getReviews($id)
    {
        $product = Product::findOrFail($id);
        return $product->reviews()->with(['user'])->get();
    }

    public function indexProductBySeller($id)
    {
        $query = Product::with(['seller', 'category', 'variants', 'images']);
        $products = $query->where('seller_id', $id)->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products
        ]);
    }
    
    // delete product matching seller
    public function deleteProductBySeller($sellerId, $productId)
    {
        $product = Product::findOrFail($productId);
        // set incomming seller id as integer
        $seller_id = (int) $sellerId;

        if ($product->seller_id !== $seller_id) {
            return response()->json([
                'message' => 'Unauthorized to delete this product',
                'incomingSellerId' => $sellerId,
                'productSellerId' => $product->seller_id
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
