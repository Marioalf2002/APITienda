# API de Tienda Online con Laravel 12

Este proyecto implementa una API RESTful para una tienda online utilizando Laravel 12 y PostgreSQL. La arquitectura sigue un enfoque en capas con DTOs, Servicios, Repositorios y Controladores.

## Requisitos

-   PHP 8.2 o superior
-   Composer
-   PostgreSQL
-   Laravel 12

## Instalación

1. Clonar el repositorio
2. Instalar dependencias:
    ```bash
    composer install
    ```
3. Configurar el archivo `.env` con las credenciales de la base de datos PostgreSQL
4. Ejecutar migraciones y seeders:
    ```bash
    php artisan migrate:fresh --seed
    ```
5. Iniciar el servidor:
    ```bash
    php artisan serve
    ```

## Estructura del Proyecto

El proyecto sigue una arquitectura en capas:

-   **DTOs (Data Transfer Objects)**: Objetos para transferir datos entre capas
-   **Servicios**: Contienen la lógica de negocio
-   **Repositorios**: Manejan el acceso a datos
-   **Controladores**: Gestionan las peticiones HTTP

### Modelos

-   `User`: Usuarios del sistema
-   `Product`: Productos disponibles
-   `Payment`: Métodos de pago
-   `OrderState`: Estados posibles de una orden
-   `Order`: Órdenes de compra
-   `OrderItem`: Items de cada orden

### DTOs

-   `ProductDTO`: Para transferir datos de productos
-   `OrderDTO`: Para transferir datos de órdenes
-   `OrderItemDTO`: Para transferir datos de items de órdenes

### Servicios

-   `ProductService`: Lógica de negocio para productos
-   `OrderService`: Lógica de negocio para órdenes

### Repositorios

-   `ProductRepository`: Acceso a datos de productos
-   `OrderRepository`: Acceso a datos de órdenes

### Controladores

-   `ProductController`: Gestiona las peticiones relacionadas con productos
-   `OrderController`: Gestiona las peticiones relacionadas con órdenes

## Endpoints API

### Productos

-   `GET /api/products`: Obtener todos los productos
-   `GET /api/products/{id}`: Obtener un producto por su ID
-   `POST /api/products`: Crear un nuevo producto
-   `PUT /api/products/{id}`: Actualizar un producto existente
-   `DELETE /api/products/{id}`: Eliminar un producto

#### Ejemplo de creación de producto:

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Producto de Prueba",
    "description": "Descripción del nuevo producto",
    "price": 1500,
    "stock": 100,
    "active": true
  }'
```

### Órdenes

-   `POST /api/buy`: Crear una nueva orden
-   `GET /api/buy/{id}`: Obtener el detalle de una orden
-   `GET /api/buy`: Obtener todas las órdenes
-   `PUT /api/buy/{id}/state`: Actualizar el estado de una orden
-   `PUT /api/buy/{id}/payment`: Actualizar el método de pago de una orden
-   `GET /api/user/{userId}/orders`: Obtener las órdenes de un usuario

#### Ejemplo de creación de orden:

```bash
curl -X POST http://localhost:8000/api/buy \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "products": [
      {
        "id": 1,
        "amount": 3
      },
      {
        "id": 2,
        "amount": 1
      }
    ],
    "payment": 1
  }'
```

#### Ejemplo de respuesta al obtener una orden:

```json
{
    "user": {
        "id": 1,
        "user": "Test User"
    },
    "products": [
        {
            "id": 1,
            "name": "Producto 1",
            "value": 1200,
            "amount": 3,
            "total": 3600
        },
        {
            "id": 2,
            "name": "Producto 2",
            "value": 800,
            "amount": 1,
            "total": 800
        }
    ],
    "state": {
        "id": 1,
        "name": "Processing"
    },
    "total": 4400,
    "amount": 4,
    "created_at": "2025-03-11 22:06:31",
    "updated_at": "2025-03-11 22:06:31",
    "payment": {
        "id": 1,
        "name": "Tarjeta de crédito"
    }
}
```

## Pruebas

El proyecto incluye pruebas funcionales para validar el funcionamiento de las APIs:

-   `ProductControllerTest`: Pruebas para el controlador de productos
-   `OrderControllerTest`: Pruebas para el controlador de órdenes

Para ejecutar las pruebas:

```bash
php artisan test
```

O para ejecutar pruebas específicas:

```bash
php artisan test --filter=ProductControllerTest
php artisan test --filter=OrderControllerTest
```

## Características Implementadas

1. **Modelo de datos en PostgreSQL**:

    - Tablas con relaciones
    - Foreign keys
    - Índices
    - Restricciones únicas

2. **CRUD para productos**:

    - Crear, leer, actualizar y eliminar productos

3. **API REST para órdenes**:

    - Crear órdenes
    - Obtener detalles de órdenes
    - Gestionar estados y métodos de pago

4. **Arquitectura en capas**:

    - DTOs para transferencia de datos
    - Servicios para lógica de negocio
    - Repositorios para acceso a datos
    - Controladores para gestión de peticiones

5. **Validación de datos**:

    - Validación de campos requeridos
    - Validación de existencia de entidades relacionadas
    - Validación de stock disponible

6. **Pruebas funcionales**:
    - Pruebas para validar el funcionamiento de las APIs
    - Cobertura de casos de éxito y error
