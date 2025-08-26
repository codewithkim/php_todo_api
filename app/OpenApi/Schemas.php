<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

final class Schemas {}

/**
 * @OA\Schema(
 *   schema="Todo",
 *   type="object",
 *   required={"id","title","is_completed","created_at","updated_at"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="Buy milk"),
 *   @OA\Property(property="description", type="string", nullable=true, example="2% milk"),
 *   @OA\Property(property="is_completed", type="boolean", example=false),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T21:43:12Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T21:43:12Z")
 * )
 */
