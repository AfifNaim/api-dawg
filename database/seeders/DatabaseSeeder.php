<?php

namespace Database\Seeders;

use App\Models\Api;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::create([
            'name' => 'Contoh Shop API',
            'base_url' => 'https://api.example-shop.com',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.example-token',
            'description' => 'Contoh dokumentasi API untuk toko online. Gunakan sebagai template.',
        ]);

        $auth = Group::create(['project_id' => $project->id, 'name' => 'Autentikasi', 'sort_order' => 1]);
        $users = Group::create(['project_id' => $project->id, 'name' => 'Pengguna', 'sort_order' => 2]);

        Api::create([
            'project_id' => $project->id,
            'group_id' => $auth->id,
            'name' => 'Login',
            'description' => 'Login pengguna dan dapatkan token akses.',
            'method' => 'POST',
            'endpoint' => '/api/auth/login',
            'request_json' => '{"email": "user@example.com", "password": "rahasia123"}',
            'example_request' => "curl -X POST https://api.example-shop.com/api/auth/login \\\n  -H \"Content-Type: application/json\" \\\n  -d '{\"email\":\"user@example.com\",\"password\":\"rahasia123\"}'",
            'success_response' => '{"success": true, "data": {"token": "eyJhbGciOiJIUzI1NiIs...", "user": {"id": 1, "name": "Budi", "email": "user@example.com"}}}',
            'error_response' => '{"success": false, "message": "Email atau password salah.", "errors": null}',
            'sort_order' => 1,
        ]);

        Api::create([
            'project_id' => $project->id,
            'group_id' => $users->id,
            'name' => 'Daftar Pengguna',
            'description' => 'Mengambil daftar semua pengguna.',
            'method' => 'GET',
            'endpoint' => '/api/users',
            'headers' => '{"Authorization": "Bearer <token>"}',
            'request_json' => null,
            'example_request' => 'curl https://api.example-shop.com/api/users \\\n  -H "Authorization: Bearer <token>"',
            'success_response' => '{"success": true, "data": [{"id": 1, "name": "Budi", "email": "user@example.com"}]}',
            'error_response' => '{"success": false, "message": "Unauthenticated.", "errors": null}',
            'sort_order' => 1,
        ]);

        Api::create([
            'project_id' => $project->id,
            'group_id' => $users->id,
            'name' => 'Detail Pengguna',
            'description' => 'Mengambil detail satu pengguna berdasarkan ID.',
            'method' => 'GET',
            'endpoint' => '/api/users/{id}',
            'headers' => '{"Authorization": "Bearer <token>"}',
            'request_json' => null,
            'example_request' => 'curl https://api.example-shop.com/api/users/1 \\\n  -H "Authorization: Bearer <token>"',
            'success_response' => '{"success": true, "data": {"id": 1, "name": "Budi", "email": "user@example.com"}}',
            'error_response' => '{"success": false, "message": "Pengguna tidak ditemukan.", "errors": null}',
            'sort_order' => 2,
        ]);
    }
}
