# API: Banners

## Endpoint

GET /api/banners

## Descripción

Devuelve los banners activos ordenados por `order` ascendente. Úsalo para el carrusel de la Home.

## Query params

- `limit` (opcional, int, default: 2): cantidad máxima de banners a retornar.

## Respuesta 200 (ejemplo)

```json
[
  {
    "id": 1,
    "title": "Cashback 20%",
    "subtitle": "A Summer Surprise",
    "image_url": "https://.../banner1.jpg",
    "order": 1
  },
  {
    "id": 2,
    "title": "Nueva colección",
    "subtitle": "Temporada 24/25",
    "image_url": "https://.../banner2.jpg",
    "order": 2
  }
]
```

## Códigos de respuesta

- `200 OK` — Petición correcta.
- `400 Parámetros inválidos` — P. ej., `limit` no es un entero válido.
- `500 Error interno` — Error en el servidor.

## Ejemplos (PowerShell / curl)

```powershell
curl http://localhost/api/banners
curl http://localhost/api/banners?limit=2
```

---

# API: Productos

## 1. Listar productos

GET /api/products

Descripción:
Devuelve una lista de productos paginada/limitada para mostrar en listados.

Query params:

- `limit` (opcional, int; default: 6): cantidad máxima de productos a retornar.

Respuesta 200 (ejemplo):

```json
[
  {
    "id": 1,
    "name": "Camiseta Local Real Madrid 24/25",
    "price": 89.99,
    "image_url": "https://...",
    "team": "Real Madrid",
    "category": "Actual",
    "description": "Camiseta oficial..."
  }
  // ...hasta "limit"
]
```

Códigos de respuesta:

- `200 OK` — Petición correcta.
- `500 Internal Server Error` — Error en el servidor (ver logs en app/laravel.log).

Ejemplo (CMD/PowerShell):

```powershell
curl.exe -i "http://localhost/api/products?limit=6"
```

## 2. Producto + tallas (detalle en 1 request)

GET /api/products/{id}

Descripción:
Devuelve detalle del producto especificado junto con sus tallas y stock.

Respuesta 200 (ejemplo):

```json
{
  "id": 1,
  "name": "Camiseta Local Real Madrid 24/25",
  "image_url": "https://...",
  "price": 89.99,
  "team": "Real Madrid",
  "category": "Actual",
  "description": "Camiseta oficial...",
  "sizes": [
    { "id": 3, "type": "adult", "label": "S", "stock": 10 },
    { "id": 4, "type": "adult", "label": "M", "stock": 0 },
    { "id": 5, "type": "adult", "label": "L", "stock": 7 }
  ]
}
```

Códigos de respuesta:

- `200 OK` — Petición correcta.
- `404 Not Found` — Producto no existe.
- `500 Internal Server Error` — Error en el servidor.

Ejemplo:

```powershell
curl.exe -i "http://localhost/api/products/1"
```

## 3. Solo tallas por producto (si prefieres 2 requests)

GET /api/products/{id}/sizes

Descripción:
Devuelve únicamente las tallas y stock del producto.

Respuesta 200 (ejemplo):

```json
{
  "product_id": 1,
  "sizes": [
    { "id": 3, "type": "adult", "label": "S", "stock": 10 },
    { "id": 4, "type": "adult", "label": "M", "stock": 0 }
  ]
}
```

Códigos de respuesta:

- `200 OK` — Petición correcta.
- `404 Not Found` — Producto no existe.
- `500 Internal Server Error` — Error en el servidor.

Ejemplo:

```powershell
curl.exe -i "http://localhost/api/products/1/sizes"
```
