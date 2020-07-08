<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GenreTest extends TestCase
{
    use DatabaseMigrations;
    public function testList()
    {
        factory(Genre::class, 1)->create();
        $categories = Genre::all();
        $this->assertCount(1, $categories);
        $genreKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'created_at', 'updated_at', 'deleted_at', 'is_active'],
            $genreKeys
        );
    }

    public function testCreate()
    {
        $genre = Genre::create(['name' => 'test1']);
        $genre->refresh();

        $this->assertEquals($genre->name, 'test1');
        $this->assertTrue($genre->is_active);

        // Valid UUID (V4)
        $uuid_v4_regex = "#^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$#i";
        $result = (bool) preg_match($uuid_v4_regex, $genre->id);
        $this->assertTrue($result);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class, 1)->create([
            'is_active' => false,
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'is_active' =>true,
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        $genre = factory(Genre::class, 1)->create()->first();
        $this->assertNotNull(Genre::find($genre)->first());
        $genre->delete();
        $this->assertNull(Genre::find($genre)->first());
    }
}
