<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    // Inyectar el repositorio de productos
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    // Obtener todos los productos
    public function getAllProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getAll($perPage);
    }

    // Obtener un producto por su ID
    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->getById($id);
    }

    // Crear un producto
    public function createProduct(ProductDTO $productDTO): Product
    {
        return $this->productRepository->create($productDTO);
    }

    // Actualizar un producto
    public function updateProduct(int $id, ProductDTO $productDTO): ?Product
    {
        return $this->productRepository->update($id, $productDTO);
    }

    // Eliminar un producto
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}