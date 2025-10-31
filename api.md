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
