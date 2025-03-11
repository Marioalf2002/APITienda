<?php

namespace App\DTOs;

class OrderItemDTO
{
    // Atributos de la clase
    public function __construct(
        public readonly int $productId,
        public readonly int $amount,
        public readonly ?string $productName = null,
        public readonly ?float $price = null,
        public readonly ?float $total = null,
    ) {}

    // Métodos de conversión de datos
    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['id'],
            amount: $data['amount'],
            productName: $data['name'] ?? null,
            price: isset($data['value']) ? (float) $data['value'] : null,
            total: isset($data['total']) ? (float) $data['total'] : null,
        );
    }

    // Convertir el DTO a un array
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'amount' => $this->amount,
            'product_name' => $this->productName,
            'price' => $this->price,
            'total' => $this->total,
        ];
    }
}