<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Laravel Todo API",
 *   version="1.0.0",
 *   description="Simple Todo REST API with pagination, sort, search, filter."
 * )
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Local server (set L5_SWAGGER_CONST_HOST in .env)"
 * )
 * @OA\Tag(
 *   name="Todos",
 *   description="Operations about todos"
 * )
 */
class OpenApi {}
