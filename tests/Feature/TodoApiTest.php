<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_list_todos(): void
    {
        $resp = $this->postJson('/api/todos', ['title' => 'A']);
        $resp->assertStatus(201)->assertJsonFragment(['title' => 'A']);

        $list = $this->getJson('/api/todos');
        $list->assertStatus(200)->assertJsonStructure(['data','meta']);
    }
}
