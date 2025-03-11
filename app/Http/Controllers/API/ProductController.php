<?php

namespace App\Http\Controllers\API;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Obtener todos los productos.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $products = $this->productService->getAllProducts($perPage);

        return response()->json($products);
    }

    /**
     * Obtener un producto por su ID.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($product);
    }

    /**
     * Crear un nuevo producto.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'active' => 'nullable|boolean',
            ]);

            $productDTO = ProductDTO::fromArray($validated);
            $product = $this->productService->createProduct($productDTO);

            return response()->json($product, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el producto: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar un producto existente.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'active' => 'nullable|boolean',
            ]);

            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Combinar los datos existentes con los nuevos
            $data = array_merge([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'active' => $product->active,
            ], $validated);

            $productDTO = ProductDTO::fromArray($data);
            $updatedProduct = $this->productService->updateProduct($id, $productDTO);

            return response()->json($updatedProduct);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el producto: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
            }

            $deleted = $this->productService->deleteProduct($id);

            if ($deleted) {
                return response()->json(null, Response::HTTP_NO_CONTENT);
            }

            return response()->json(['message' => 'No se pudo eliminar el producto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el producto: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
