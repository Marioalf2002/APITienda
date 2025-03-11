<?php

namespace App\Repositories;

use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    // Inyectar los modelos de orden y item de orden
    public function __construct(
        private Order $model,
        private OrderItem $orderItemModel
    ) {}

    // Obtener todas las órdenes
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'state', 'payment', 'items.product'])->paginate($perPage);
    }

    // Obtener una orden por su ID
    public function getById(int $id): ?Order
    {
        return $this->model->with(['user', 'state', 'payment', 'items.product'])->find($id);
    }

    // Crear una orden con sus items
    public function create(OrderDTO $orderDTO): ?Order
    {
        try {
            DB::beginTransaction();

            // Crear la orden
            $order = $this->model->create([
                'user_id' => $orderDTO->userId,
                'order_state_id' => $orderDTO->orderStateId ?? 1,
                'payment_id' => $orderDTO->paymentId,
                'total' => $orderDTO->total,
                'total_items' => $orderDTO->totalItems,
            ]);

            // Crear los items de la orden
            foreach ($orderDTO->items as $itemDTO) {
                $this->orderItemModel->create([
                    'order_id' => $order->id,
                    'product_id' => $itemDTO->productId,
                    'product_name' => $itemDTO->productName,
                    'price' => $itemDTO->price,
                    'amount' => $itemDTO->amount,
                    'total' => $itemDTO->total,
                ]);
            }

            DB::commit();

            return $this->getById($order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Actualizar una orden
    public function update(int $id, OrderDTO $orderDTO): ?Order
    {
        try {
            DB::beginTransaction();

            $order = $this->getById($id);

            if (!$order) {
                DB::rollBack();
                return null;
            }

            // Actualizar la orden
            $order->update([
                'order_state_id' => $orderDTO->orderStateId ?? $order->order_state_id,
                'payment_id' => $orderDTO->paymentId ?? $order->payment_id,
                'total' => $orderDTO->total ?? $order->total,
                'total_items' => $orderDTO->totalItems ?? $order->total_items,
            ]);

            DB::commit();

            return $this->getById($order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Eliminar una orden
    public function delete(int $id): bool
    {
        $order = $this->getById($id);

        if (!$order) {
            return false;
        }

        return $order->delete();
    }

    // Obtener todas las órdenes de un usuario
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['state', 'payment', 'items.product'])
            ->where('user_id', $userId)
            ->paginate($perPage);
    }
}