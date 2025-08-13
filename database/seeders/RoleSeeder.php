<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use BezhanSalleh\FilamentShield\Support\Utils;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Super Admin Role - mendapatkan semua permission (provinsi)
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'description' => 'Role yang memiliki seluruh hak akses pada sistem'
        ]);

        $kabkotRole = Role::create([
            'name' => 'kabkot',
            'guard_name' => 'web',
            'description' => 'Role yang memiliki hak akses pada tingkat kabupaten/kota'
        ]);

        // Setelah Shield menghasilkan semua permission, kita akan menambahkan permission ke role
        // Shield permission akan dibuat dengan pola: view_[resource], create_[resource], dll

        // SuperAdmin mendapatkan semua permission
        $superAdminRole->givePermissionTo(Permission::all());

        // Kabkot mendapatkan permission untuk mengelola publikasi
        $this->assignNaskahPermissions($kabkotRole);
    }

    private function assignNaskahPermissions($role)
    {
        $entities = ['naskah'];

        $resourcePermissions = collect($entities)
            ->flatMap(fn($entity) => [
                "view_$entity",
                "create_$entity",
                "update_$entity",
                "restore_$entity",
                "delete_$entity",
                "view_any_$entity",
                "restore_any_$entity",
                "delete_any_$entity",
            ])
            ->toArray();

        // Berikan permission ke role
        foreach ($resourcePermissions as $permissionName) {
            // Pastikan permission ada sebelum memberikannya
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => config('filament-shield.guard', 'web'),
            ]);

            $role->givePermissionTo($permission);
        }
    }
}
