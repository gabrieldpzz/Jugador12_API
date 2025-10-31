<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA; // <-- imprescindible

/**
 * @OA\Info(
 *   title="Jugador12 API",
 *   version="1.0.0",
 *   description="Documentación de la API de Jugador12"
 * )
 * @OA\Server(
 *   url="/",
 *   description="Servidor base (Nginx en Docker)"
 * )
 *
 * @OA\Tag(
 *   name="Productos",
 *   description="Catálogo: listado, detalle, tallas e imágenes"
 * )
 *
 * @OA\Tag(
 *   name="Banners",
 *   description="Carrusel de la portada"
 * )
 */
class OpenApi {}
