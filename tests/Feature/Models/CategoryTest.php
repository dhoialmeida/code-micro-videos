<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'description', 'created_at', 'updated_at', 'deleted_at', 'is_active'],
            $categoryKeys
        );
    }

    public function testCreate()
    {
        $category = Category::create(['name' => 'test1']);
        $category->refresh();

        $this->assertEquals($category->name, 'test1');
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        // Valid UUID (V4)
        $uuid_v4_regex = "#^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$#i";
        $result = (bool) preg_match($uuid_v4_regex, $category->id);
        $this->assertTrue($result);

        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);
        $this->assertEquals($category->description, 'test_description');

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class, 1)->create([
            'description' => 'test_description',
            'is_active' => false,
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' =>true,
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class, 1)->create()->first();
        $this->assertNotNull(Category::find($category)->first());
        $category->delete();
        $this->assertNull(Category::find($category)->first());
    }
}
