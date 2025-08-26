<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Todo",
 *   required={"title"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="Buy milk"),
 *   @OA\Property(property="description", type="string", example="From the store", nullable=true),
 *   @OA\Property(property="is_completed", type="boolean", example=false),
 *   @OA\Property(property="target_end_date", type="string", format="date", example="2025-09-01", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Todo extends Model
{
    protected $fillable = ['title','description','is_completed','target_end_date'];
    protected $casts = ['is_completed'=>'boolean','target_end_date'=>'date'];
}
