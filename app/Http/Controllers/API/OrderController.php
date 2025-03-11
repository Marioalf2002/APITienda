<?php

namespace App\Http\Controllers\API;

use App\DTOs\OrderDTO;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Crear una nueva orden de compra.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|integer|exists:products,id',
                'products.*.amount' => 'required|integer|min:1',
                'payment' => 'nullable|integer|exists:payments,id',
            ]);

            $orderDTO = OrderDTO::fromArray($validated);
            $order = $this->orderService->createOrder($orderDTO);

            return response()->json($order, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener el detalle de una orden por su ID.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Formatear la respuesta según el ejemplo
        $formattedOrder = [
            'user' => [
                'id' => $order->user->id,
                'user' => $order->user->name,
            ],
            'products' => $order->items->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product_name,
                    'value' => (float) $item->price,
                    'amount' => $item->amount,
                    'total' => (float) $item->total,
                ];
            }),
            'state' => [
                'id' => $order->state->id,
                'name' => $order->state->name,
            ],
            'total' => (float) $order->total,
            'amount' => $order->total_items,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : null,
        ];

        // Añadir información de pago si existe
        if ($order->payment) {
            $formattedOrder['payment'] = [
                'id' => $order->payment->id,
                'name' => $order->payment->name,
            ];
        } else {
            $formattedOrder['payment'] = null;
        }

        return response()->json($formattedOrder);
    }

    /**
     * Obtener todas las órdenes.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $orders = $this->orderService->getAllOrders($perPage);

        return response()->json($orders);
    }

    /**
     * Actualizar el estado de una orden.
     */
    public function updateState(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'state_id' => 'required|integer|exists:order_states,id',
            ]);

            $order = $this->orderService->getOrderById($id);

            if (!$order) {
                return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
            }

            $updatedOrder = $this->orderService->updateOrderState($id, $validated['state_id']);

            return response()->json($updatedOrder);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el estado de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar el método de pago de una orden.
     */
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_id' => 'required|integer|exists:payments,id',
            ]);

            $order = $this->orderService->getOrderById($id);

            if (!$order) {
                return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
            }

            $updatedOrder = $this->orderService->updateOrderPayment($id, $validated['payment_id']);

            return response()->json($updatedOrder);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el método de pago de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener las órdenes de un usuario.
     */
    public function getByUser(int $userId): JsonResponse
    {
        $orders = $this->orderService->getOrdersByUserId($userId);

        return response()->json($orders);
    }
}
