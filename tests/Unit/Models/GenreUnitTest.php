<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenreUnitTest extends TestCase
{
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function testTraits()
    {
        $expectedTraits = [SoftDeletes::class, \App\Models\Traits\Uuid::class];
        $usedTraites = array_keys(class_uses(Genre::class));
        $this->assertEqualsCanonicalizing($expectedTraits, $usedTraites);
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'is_active' => 'bool'];
        $this->assertEqualsCanonicalizing($casts, $this->genre->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->genre->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }
        $this->assertCount(count($dates), $this->genre->getDates());
    }
}
