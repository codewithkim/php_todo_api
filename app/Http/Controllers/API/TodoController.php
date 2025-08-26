<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Todo;
use OpenApi\Annotations as OA;

class TodoController extends Controller
{
    /**
     * List todos with pagination, sort, search, and boolean filter.
     *
     * @OA\Get(
     *   path="/api/todos",
     *   operationId="listTodos",
     *   tags={"Todos"},
     *   summary="List todos",
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1)),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100)),
     *   @OA\Parameter(name="sortBy", in="query", description="id|title|created_at|updated_at|is_completed", @OA\Schema(type="string")),
     *   @OA\Parameter(name="sortDir", in="query", @OA\Schema(type="string", enum={"asc","desc"})),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="filter[is_completed]", in="query", @OA\Schema(type="integer", enum={0,1})),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array",
     *         @OA\Items(ref="#/components/schemas/Todo")
     *       ),
     *       @OA\Property(property="links", type="object"),
     *       @OA\Property(property="meta", type="object")
     *     )
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $q = Todo::query();

        if ($search = (string) $request->input('search', '')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $isCompleted = $request->input('filter.is_completed', null);
        if ($isCompleted !== null && $isCompleted !== '') {
            $q->where('is_completed', (bool) ((int) $isCompleted));
        }

        $allowedSort = ['id','title','created_at','updated_at','is_completed'];
        $sortBy = $request->input('sortBy', 'created_at');
        if (!in_array($sortBy, $allowedSort, true)) $sortBy = 'created_at';
        $sortDir = strtolower((string) $request->input('sortDir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        return response()->json($q->paginate($perPage));
    }

    /**
     * Create a new todo. 409 on duplicate title.
     *
     * @OA\Post(
     *   path="/api/todos",
     *   operationId="createTodo",
     *   tags={"Todos"},
     *   summary="Create a todo",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"title"},
     *       @OA\Property(property="title", type="string", example="Buy milk"),
     *       @OA\Property(property="description", type="string", example="2% milk from store", nullable=true),
     *       @OA\Property(property="is_completed", type="boolean", example=false)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *   @OA\Response(response=409, description="Title already exists"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string','max:2000'],
            'is_completed' => ['nullable','boolean'],
        ]);

        if (Todo::where('title', $data['title'])->exists()) {
            return response()->json(['message' => 'Title already exists'], 409);
        }

        $todo = Todo::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'is_completed' => (bool) ($data['is_completed'] ?? false),
        ]);

        return response()->json($todo, 201);
    }

    /**
     * Get a single todo by id.
     *
     * @OA\Get(
     *   path="/api/todos/{id}",
     *   operationId="showTodo",
     *   tags={"Todos"},
     *   summary="Get todo",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $todo = Todo::find($id);
        if (!$todo) return response()->json(['message' => 'Not found'], 404);
        return response()->json($todo);
    }

    /**
     * Update a todo. PUT=full, PATCH=partial (no required fields).
     *
     * @OA\Put(
     *   path="/api/todos/{id}",
     *   operationId="replaceTodo",
     *   tags={"Todos"},
     *   summary="Replace a todo (full)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"title"},
     *       @OA\Property(property="title", type="string", example="New title"),
     *       @OA\Property(property="description", type="string", example="Updated description", nullable=true),
     *       @OA\Property(property="is_completed", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *   @OA\Response(response=404, description="Not found"),
     *   @OA\Response(response=409, description="Title already exists"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     * @OA\Patch(
     *   path="/api/todos/{id}",
     *   operationId="patchTodo",
     *   tags={"Todos"},
     *   summary="Partially update a todo",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="title", type="string", example="Renamed"),
     *       @OA\Property(property="description", type="string", example="Only changing this", nullable=true),
     *       @OA\Property(property="is_completed", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *   @OA\Response(response=404, description="Not found"),
     *   @OA\Response(response=409, description="Title already exists"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $todo = Todo::find($id);
        if (!$todo) return response()->json(['message' => 'Not found'], 404);

        if ($request->isMethod('patch')) {
            $rules = [
                'title'        => ['sometimes','string','max:255'],
                'description'  => ['sometimes','nullable','string','max:2000'],
                'is_completed' => ['sometimes','boolean'],
            ];
        } else {
            $rules = [
                'title'        => ['required','string','max:255'],
                'description'  => ['nullable','string','max:2000'],
                'is_completed' => ['nullable','boolean'],
            ];
        }

        $data = $request->validate($rules);

        if (array_key_exists('title', $data)) {
            $exists = Todo::where('title', $data['title'])->where('id', '!=', $todo->id)->exists();
            if ($exists) return response()->json(['message' => 'Title already exists'], 409);
            $todo->title = $data['title'];
        }
        if (array_key_exists('description', $data)) $todo->description = $data['description'];
        if (array_key_exists('is_completed', $data)) $todo->is_completed = (bool) $data['is_completed'];

        $todo->save();
        return response()->json($todo);
    }

    /**
     * Delete a todo.
     *
     * @OA\Delete(
     *   path="/api/todos/{id}",
     *   operationId="deleteTodo",
     *   tags={"Todos"},
     *   summary="Delete todo",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="No content"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $todo = Todo::find($id);
        if (!$todo) return response()->json(['message' => 'Not found'], 404);
        $todo->delete();
        return response()->json(null, 204);
    }
}
