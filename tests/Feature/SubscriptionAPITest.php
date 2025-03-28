<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class SubscriptionAPITest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_returns_a_successful_response(): void
    {
        $response = $this->post('/api/subscribers', [
            'email' => 'test@test.com',
            'percent' => '3',
            'period' => '1'
        ]);
        $response->assertStatus(201);

        $response = $this->post('/api/subscribers', [
            'email' => 'test2@test.com',
            'percent' => '3',
            'period' => '6'
        ]);
        $response->assertStatus(201);

        $response = $this->post('/api/subscribers', [
            'email' => 'test3@test.com',
            'percent' => 3,
            'period' => '24'
        ]);
        $response->assertStatus(201);
    }

    public function test_the_app_returns_bad_request_when_no_parameters_are_sent(): void
    {
        $response = $this->get('/api/subscribers');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_percent_is_not_sent(): void
    {
        $response = $this->get('/api/subscribers?email=test@test.com&period=24');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_period_is_not_sent(): void
    {
        $response = $this->get('/api/subscribers?email=test@test.com&percent=3&period=');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_period_is_not_number(): void
    {
        $response = $this->get('/api/subscribers?email=test@test.com&percent=3&period=abc');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_period_is_not_1_6_or_24(): void
    {
        $response = $this->get('/api/subscribers?email=test@test.com&percent=3&period=2');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_email_is_not_sent(): void
    {
        $response = $this->get('/api/subscribers?percent=3&period=24');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_email_is_not_in_correct_format(): void
    {
        $response = $this->get('/api/subscribers?email=test@test&percent=3&period=24');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_email_is_not_in_correct_format_2(): void
    {
        $response = $this->get('/api/subscribers?email=test.com&percent=3&period=24');
        $response->assertStatus(405);
    }

    public function test_the_app_returns_bad_request_when_email_is_already_registered(): void
    {
        $response = $this->post('/api/subscribers', [
            'email' => 'test@test.com',
            'percent' => 3,
            'period' => 24
        ]);
        $response->assertStatus(201);

        $response = $this->post('/api/subscribers', [
            'email' => 'test@test.com',
            'percent' => 5,
            'period' => 24
        ]);
        $response->assertStatus(400);
    }

    public function test_unsubscribe_works_as_expected(): void
    {
        $response = $this->post('/api/subscribers', [
            'email' => 'test@test.com',
            'percent' => 3,
            'period' => 24
        ]);
        $response->assertStatus(201);

        $response = $this->get('/api/subscribers/1/unsubscribe?email=test@test.com');
        $response->assertStatus(200);
    }

    public function test_unsubscribe_fails_when_email_not_found_in_DB(): void
    {
        $response = $this->get('/api/subscribers/1/unsubscribe?email=test@test.com');
        $response->assertStatus(400);
    }
}
