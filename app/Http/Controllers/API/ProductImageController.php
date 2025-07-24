<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductImageController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        return $product->images()->get();
    }

    public function store(Request $request, $productId)
    {
        // Définir les types MIME autorisés
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        $image = $request->file('image');
        try {
            // Vérifier l'existence du produit
            $product = Product::findOrFail($productId);

            // Valider les données
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
                'is_main' => 'boolean',
                'sort_order' => 'integer',
            ]);

            // Vérifier le fichier image
            if (!$image) {
                throw new \Exception('No image file provided');
            }

            // Générer un nom unique pour l'image
            $filename = time() . '_' . $image->getClientOriginalName();

            // Vérifier la taille du fichier
            if ($image->getSize() > 5048 * 1024) { // 5Mo en bytes
                throw new \Exception('Image size exceeds maximum allowed size (5MB)');
            }

            // Vérifier le type MIME
            $mimeType = $image->getMimeType();
            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Invalid image type. Only JPEG, PNG, and GIF are allowed');
            }

            // Stocker l'image
            try {
                $publicPath = $image->storeAs('public/products', $filename);
                $path = $image->storeAs('/products', $filename);
            } catch (\Exception $e) {
                throw new \Exception('Failed to store image: ' . $e->getMessage());
            }

            // Créer l'enregistrement de l'image
            $imageRecord = $product->images()->create([
                'image_url' => $path,
                'is_main' => $validated['is_main'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image' => $imageRecord
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'details' => [
                    'file_size' => isset($image) ? $image->getSize() : null,
                    'allowed_size' => 5048 * 1024,
                    'file_type' => isset($image) ? $image->getMimeType() : null,
                    'allowed_types' => $allowedTypes
                ]
            ], 400);
        }
    }

    public function update(Request $request, $productId, $imageId)
    {
        $validated = $request->validate([
            'is_main' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $image = ProductImage::where('product_id', $productId)
            ->findOrFail($imageId);

        $image->update($validated);
        return response()->json([
            'message' => 'Image updated successfully',
            'image' => $image
        ], 200);
    }

    public function destroy($productId, $imageId)
    {
        $image = ProductImage::where('product_id', $productId)
            ->findOrFail($imageId);

        // Supprimer le fichier de l'image
        Storage::delete($image->image_url);
        
        $image->delete();
        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }

    public function setMainImage($productId, $imageId)
    {
        $product = Product::findOrFail($productId);
        
        // Marquer toutes les autres images comme non principales
        $product->images()->update(['is_main' => false]);
        
        // Marquer l'image spécifiée comme principale
        $image = $product->images()->findOrFail($imageId);
        $image->update(['is_main' => true]);

        return response()->json([
            'message' => 'Image set as main successfully',
            'image' => $image
        ]);
    }
}
