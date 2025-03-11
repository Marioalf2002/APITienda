<?php

namespace App\Repositories;

use App\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function __construct(
        private Product $model
    ) {}

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function getById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function create(ProductDTO $productDTO): Product
    {
        return $this->model->create($productDTO->toArray());
    }

    public function update(int $id, ProductDTO $productDTO): ?Product
    {
        $product = $this->getById($id);

        if (!$product) {
            return null;
        }

        $product->update($productDTO->toArray());

        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        $product = $this->getById($id);

        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function getByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}