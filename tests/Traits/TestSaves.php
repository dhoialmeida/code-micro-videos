<?php

namespace Tests\Traits;

trait TestSaves {
    protected function assertStore($sendData, $testData) {
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Status must be 201, given {$response->status()}: {$response->content()}.");
        } 
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testData + ['id' => $response->json("id")]);
    }
}