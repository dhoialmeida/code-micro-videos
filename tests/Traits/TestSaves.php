<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertStore($sendData, $testDatabase, $testJsonData = []): TestResponse
    {
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Status must be 201, given {$response->status()}: {$response->content()}.");
        }

        $this->assertInDatabase($response, $testDatabase);
        $this->arrestJsonResponseContent($response, $testDatabase, $testJsonData);
        return $response;
    }

    protected function assertUpdate($sendData, $testDatabase, $testJsonData = []): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Status must be 200, given {$response->status()}: {$response->content()}.");
        }

        $this->assertInDatabase($response, $testDatabase);
        $this->arrestJsonResponseContent($response, $testDatabase, $testJsonData);
        return $response;
    }

    private function assertInDatabase($response, $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json("id")]);
    }

    private function arrestJsonResponseContent($response, $testDatabase, $testJsonData)
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testJsonData + ['id' => $response->json('id')]);
        return $response;
    }
}
