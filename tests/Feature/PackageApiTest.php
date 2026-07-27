<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PackageApiTest extends TestCase
{
    use DatabaseMigrations;

    private string $jwtToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->jwtToken = $this->loginAndGetToken();
    }

    private function loginAndGetToken(): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@aeroflash.com',
            'password' => 'password',
        ]);

        return $response->json('data.access_token');
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->jwtToken,
            'Accept' => 'application/json',
        ];
    }

    public function test_login_successful(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@aeroflash.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Authentication successful',
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => ['access_token', 'token_type', 'expires_in'],
        ]);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@aeroflash.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_login_validation_error(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Validation error',
        ]);
    }

    public function test_register_package(): void
    {
        $payload = [
            'tracking_number' => 'AF-NEW-001',
            'description' => 'Test package for unit testing',
            'weight' => 3.50,
            'branch_id' => 1,
            'delivery_address' => 'Calle Falsa 123',
            'recipient_name' => 'Test User',
            'recipient_phone' => '55-9999-0000',
        ];

        $response = $this->postJson('/api/v1/packages', $payload, $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Package registered successfully',
        ]);
        $response->assertJsonPath('data.package.tracking_number', 'AF-NEW-001');
        $response->assertJsonPath('data.package.status', 'Registered');
        $this->assertNotEmpty($response->json('data.tracking_history'));
    }

    public function test_register_package_validation_error(): void
    {
        $response = $this->postJson('/api/v1/packages', [], $this->authHeaders());

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Validation error',
        ]);
    }

    public function test_get_package_by_tracking_number(): void
    {
        $response = $this->getJson('/api/v1/packages/AF-TEST-001', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Package found',
        ]);
        $response->assertJsonPath('data.package.tracking_number', 'AF-TEST-001');
        $response->assertJsonPath('data.package.status', 'Registered');
        $this->assertIsArray($response->json('data.tracking_history'));
    }

    public function test_get_package_not_found(): void
    {
        $response = $this->getJson('/api/v1/packages/INVALID-999', $this->authHeaders());

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_update_package_status(): void
    {
        $payload = [
            'new_status' => 'In Transit',
            'comment' => 'Automated test transition',
            'location' => 'CDMX Centro',
            'courier_id' => 1,
            'vehicle_id' => 1,
        ];

        $response = $this->patchJson('/api/v1/packages/AF-TEST-001/status', $payload, $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
        $response->assertJsonPath('data.package.status', 'In Transit');
    }

    public function test_invalid_status_transition(): void
    {
        $response = $this->patchJson('/api/v1/packages/AF-TEST-001/status', [
            'new_status' => 'Delivered',
        ], $this->authHeaders());

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_require_courier_and_vehicle_for_in_transit(): void
    {
        $response = $this->patchJson('/api/v1/packages/AF-TEST-001/status', [
            'new_status' => 'In Transit',
        ], $this->authHeaders());

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_unauthenticated_request(): void
    {
        $response = $this->getJson('/api/v1/packages/AF-TEST-001');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Token not provided',
        ]);
    }

    public function test_invalid_token(): void
    {
        $response = $this->getJson('/api/v1/packages/AF-TEST-001', [
            'Authorization' => 'Bearer invalid-token-here',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid or expired token',
        ]);
    }
}
