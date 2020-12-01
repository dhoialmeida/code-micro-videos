<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $cast_member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast_member = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response->assertStatus(200)->assertJson([$this->cast_member->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->cast_member->id]));
        $response->assertStatus(200)->assertJson($this->cast_member->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'type' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR,
        ];

        $response = $this->assertStore($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);

        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR,
        ];

        $this->assertStore($data, $data + ['deleted_at' => null]);
    }

    public function testUpdate()
    {
        $this->cast_member = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR,
        ]);

        // Atualização genérica
        $data = [
            'name' => 'test changed',
            'type' => CastMember::TYPE_ACTOR,
        ];
        $response = $this->assertUpdate($data, $data);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testDestroy()
    {
        $response = $this->delete(route('cast_members.destroy', ['cast_member' => $this->cast_member->id]), []);

        $response
            ->assertStatus(204);

        $this->assertNull(CastMember::find($this->cast_member->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->cast_member->id));
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->cast_member]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
