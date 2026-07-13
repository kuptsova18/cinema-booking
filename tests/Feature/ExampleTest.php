<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_page_opens(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}