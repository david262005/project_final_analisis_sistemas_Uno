<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportTest extends RefreshDatabase
{
    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('Recepcionista');
    }

    public function test_report_index_returns_users(): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('X-Tenant-ID', $this->tenant->id)
            ->withHeader('Authorization', "Bearer {$token}")
            ->get('/api/v1/reports');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'email', 'roles', 'tenant', 'created_at']]])
            ->assertJsonCount(1, 'data');
    }

    public function test_report_index_filters_by_role(): void
    {
        $user2 = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user2->assignRole('Doctor');

        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('X-Tenant-ID', $this->tenant->id)
            ->withHeader('Authorization', "Bearer {$token}")
            ->get('/api/v1/reports?role=Doctor');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', $user2->name);
    }

    public function test_report_export_csv(): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('X-Tenant-ID', $this->tenant->id)
            ->withHeader('Authorization', "Bearer {$token}")
            ->get('/api/v1/reports?export=csv');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv')
            ->assertHeader('Content-Disposition');
    }

    public function test_report_requires_authentication(): void
    {
        $response = $this->withHeader('X-Tenant-ID', $this->tenant->id)
            ->get('/api/v1/reports');

        $response->assertStatus(401);
    }
}
