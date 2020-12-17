<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Video::class, 1)->create();
        $categories = Video::all();
        $this->assertCount(1, $categories);
        $videoKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $videoKeys
        );
    }

    public function testCreate()
    {
        $video = Video::create([
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ]);
        $video->refresh();

        $this->assertEquals($video->title, 'title');
        $this->assertNotNull($video->description);

        // Valid UUID (V4)
        $uuid_v4_regex = "#^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$#i";
        $result = (bool) preg_match($uuid_v4_regex, $video->id);
        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $video = factory(Video::class, 1)->create([
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ])->first();

        $data = [
            'title' => 'title edited',
            'description' => 'description edited',
            'year_launched' => 2011,
            'rating' => Video::RATING_LIST[1],
            'duration' => 91
        ];

        $video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete()
    {
        $video = factory(Video::class, 1)->create()->first();
        $this->assertNotNull(Video::find($video)->first());
        $video->delete();
        $this->assertNull(Video::find($video)->first());
    }
}
