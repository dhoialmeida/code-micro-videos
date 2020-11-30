<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves {
    protected function assertStore($sendData, $testDatabase, $testJsonData = []): TestResponse {
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Status must be 201, given {$response->status()}: {$response->content()}.");
        } 
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json("id")]);

        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testJsonData + ['id' => $response->json('id')]);
        return $response;
    }
}