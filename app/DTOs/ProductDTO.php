<?php

namespace App\DTOs;

class ProductDTO
{
    // Atributos de la clase
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly float $price,
        public readonly int $stock = 0,
        public readonly bool $active = true,
    ) {}

    // Métodos de conversión de datos
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            stock: (int) ($data['stock'] ?? 0),
            active: (bool) ($data['active'] ?? true),
        );
    }

    // Convertir el DTO a un array
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'active' => $this->active,
        ];
    }
}