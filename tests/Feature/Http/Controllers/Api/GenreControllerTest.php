<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create(['name' => 'test']);
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(200)->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];

        $response = $this->assertStore($data, $data + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);

        $data = [
            'name' => 'test',
            'is_active' => false
        ];

        $this->assertStore($data, $data + ['is_active' => 0, 'deleted_at' => null]);
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create([
            'name' => 'test',
            'is_active' => false,
        ]);

        // Atualização genérica
        $data = [
            'name' => 'test changed',
            'is_active' => true,
        ];
        $response = $this->assertUpdate($data, $data);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testDestroy()
    {
        $response = $this->delete(route('genres.destroy', ['genre' => $this->genre->id]), []);

        $response
            ->assertStatus(204);

        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
