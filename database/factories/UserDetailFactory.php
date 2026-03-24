<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [];
    }

    public function ketuaRW(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_roles' => 1,
            'status' => 'Disetujui',
            'status_transaction' => 'Belum Disetujui',
        ]);
    }

    public function bankSampah(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_roles' => 2,
            'id_gender' => 3,
            'status' => 'Pengajuan Verifikasi',
            'status_transaction' => 'Belum Disetujui',
        ]);
    }

    public function warga(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_roles' => 3,
            'status' => 'Pengajuan Verifikasi',
            'status_transaction' => 'Belum Disetujui',
        ]);
    }
}
