<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $admin = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $subdirector = Role::updateOrCreate(['slug' => 'subdirector'], ['name' => 'Subdirector']);
        $operador = Role::updateOrCreate(['slug' => 'operador'], ['name' => 'Operador']);

        // 2. Permissions
        $manageUsers = Permission::updateOrCreate(['slug' => 'manage-users'], ['name' => 'Gestionar Usuarios']);
        $managePlanning = Permission::updateOrCreate(['slug' => 'manage-planning'], ['name' => 'Gestionar Planificación']);
        $viewReports = Permission::updateOrCreate(['slug' => 'view-reports'], ['name' => 'Ver Reportes']);

        // 3. Link Roles and Permissions
        $admin->permissions()->sync([$manageUsers->id, $managePlanning->id, $viewReports->id]);
        $subdirector->permissions()->sync([$managePlanning->id, $viewReports->id]);
        $operador->permissions()->sync([$viewReports->id]);

        // 4. Assign Admin Role to existing user (assuming 'admin' username exists)
        $user = User::where('username', 'admin')->first();
        if ($user) {
            $user->roles()->sync([$admin->id]);
        }
    }
}
