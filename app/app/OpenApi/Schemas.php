<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA; // <-- imprescindible

/**
 * @OA\Schema(
 *   schema="ProductImage",
 *   type="object",
 *   required={"id","src"},
 *   @OA\Property(property="id",  type="integer", example=11),
 *   @OA\Property(property="src", type="string", format="uri", example="http://localhost/storage/products/rm1.jpg"),
 *   @OA\Property(property="alt", type="string", nullable=true, example="Frente")
 * )
 *
 * @OA\Schema(
 *   schema="SizeOption",
 *   type="object",
 *   required={"id","type","label","stock"},
 *   @OA\Property(property="id",    type="integer", example=3),
 *   @OA\Property(property="type",  type="string",  example="adult"),
 *   @OA\Property(property="label", type="string",  example="M"),
 *   @OA\Property(property="stock", type="integer", example=7)
 * )
 *
 * @OA\Schema(
 *   schema="Product",
 *   type="object",
 *   required={"id","name","price","images"},
 *   @OA\Property(property="id",          type="integer", example=1),
 *   @OA\Property(property="name",        type="string",  example="Camiseta Local Real Madrid 24/25"),
 *   @OA\Property(property="price",       type="number",  format="float", example=89.99),
 *   @OA\Property(property="team",        type="string",  nullable=true, example="Real Madrid"),
 *   @OA\Property(property="category",    type="string",  nullable=true, example="Actual"),
 *   @OA\Property(property="description", type="string",  nullable=true, example="Camiseta oficial..."),
 *   @OA\Property(
 *     property="images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/ProductImage")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="ProductWithSizes",
 *   allOf={
 *     @OA\Schema(ref="#/components/schemas/Product"),
 *     @OA\Schema(
 *       @OA\Property(
 *         property="sizes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SizeOption")
 *       )
 *     )
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="Banner",
 *   type="object",
 *   required={"id","title","image_url","order"},
 *   @OA\Property(property="id",         type="integer", example=1),
 *   @OA\Property(property="title",      type="string",  example="Sorpresa de verano"),
 *   @OA\Property(property="subtitle",   type="string",  nullable=true, example="Cashback 20%"),
 *   @OA\Property(property="image_url",  type="string",  format="uri", example="https://cdn.example.com/banners/campnou.jpg"),
 *   @OA\Property(property="order",      type="integer", example=1, description="Orden de aparición; 1 se muestra primero")
 * )
 */
class Schemas {}
