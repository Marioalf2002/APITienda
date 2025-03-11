<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Models\Order;
use App\Models\OrderState;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    // Inyectar el repositorio de órdenes y productos
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository
    ) {}

    // Obtener todas las órdenes
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getAll($perPage);
    }

    // Obtener una orden por su ID
    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->getById($id);
    }

    // Crear una orden
    public function createOrder(OrderDTO $orderDTO): ?Order
    {
        try {
            DB::beginTransaction();

            // Obtener los productos por sus IDs
            $productIds = array_map(fn($item) => $item->productId, $orderDTO->items);
            $products = $this->productRepository->getByIds($productIds);

            // Calcular el total y preparar los items con la información completa
            $total = 0;
            $totalItems = 0;
            $items = [];

            // Iterar sobre los items de la orden
            foreach ($orderDTO->items as $itemDTO) {
                $product = $products->firstWhere('id', $itemDTO->productId);

                // Verificar si el producto existe
                if (!$product) {
                    DB::rollBack();
                    throw new \Exception("Producto con ID {$itemDTO->productId} no encontrado");
                }

                // Verificar stock
                if ($product->stock < $itemDTO->amount) {
                    DB::rollBack();
                    throw new \Exception("Stock insuficiente para el producto {$product->name}");
                }

                // Calcular el total del item
                $itemTotal = $product->price * $itemDTO->amount;
                $total += $itemTotal;
                $totalItems += $itemDTO->amount;

                // Actualizar el stock del producto
                $product->stock -= $itemDTO->amount;
                $product->save();

                // Crear el DTO del item con la información completa
                $items[] = new OrderItemDTO(
                    productId: $product->id,
                    amount: $itemDTO->amount,
                    productName: $product->name,
                    price: $product->price,
                    total: $itemTotal
                );
            }

            // Obtener el ID del estado "Processing"
            $processingStateId = OrderState::where('name', 'Processing')->first()->id;

            // Crear el DTO de la orden con la información calculada
            $completeOrderDTO = new OrderDTO(
                userId: $orderDTO->userId,
                items: $items,
                paymentId: $orderDTO->paymentId,
                orderStateId: $processingStateId,
                total: $total,
                totalItems: $totalItems
            );

            // Crear la orden
            $order = $this->orderRepository->create($completeOrderDTO);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Actualizar el estado de una orden
    public function updateOrderState(int $id, int $stateId): ?Order
    {
        $orderDTO = new OrderDTO(
            userId: 0,
            items: [],
            orderStateId: $stateId
        );

        return $this->orderRepository->update($id, $orderDTO);
    }

    // Actualizar el método de pago de una orden
    public function updateOrderPayment(int $id, int $paymentId): ?Order
    {
        $orderDTO = new OrderDTO(
            userId: 0,
            items: [],
            paymentId: $paymentId
        );

        return $this->orderRepository->update($id, $orderDTO);
    }

    // Eliminar una orden
    public function deleteOrder(int $id): bool
    {
        return $this->orderRepository->delete($id);
    }

    // Obtener las órdenes de un usuario
    public function getOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getByUserId($userId, $perPage);
    }
}