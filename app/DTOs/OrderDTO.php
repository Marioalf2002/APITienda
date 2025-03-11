<?php

namespace App\DTOs;

class OrderDTO
{
    /**
     * @param int $userId
     * @param OrderItemDTO[] $items
     * @param int|null $paymentId
     * @param int|null $orderStateId
     * @param float|null $total
     * @param int|null $totalItems
     */
    public function __construct(
        public readonly int $userId,
        public readonly array $items,
        public readonly ?int $paymentId = null,
        public readonly ?int $orderStateId = null,
        public readonly ?float $total = null,
        public readonly ?int $totalItems = null,
    ) {}

    // Métodos de conversión de datos
    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data['products'] as $product) {
            $items[] = OrderItemDTO::fromArray($product);
        }

        return new self(
            userId: $data['user_id'],
            items: $items,
            paymentId: $data['payment'] ?? null,
            orderStateId: $data['order_state_id'] ?? null,
            total: $data['total'] ?? null,
            totalItems: $data['total_items'] ?? null,
        );
    }

    // Convertir el DTO a un array
    public function toArray(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        return [
            'user_id' => $this->userId,
            'products' => $items,
            'payment_id' => $this->paymentId,
            'order_state_id' => $this->orderStateId,
            'total' => $this->total,
            'total_items' => $this->totalItems,
        ];
    }
}