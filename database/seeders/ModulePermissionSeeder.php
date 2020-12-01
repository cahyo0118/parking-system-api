<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::beginTransaction();

        try {

            $modules = [
                [
                    'name' => 'users',
                    'display_name' => 'Users',
                    'permissions' => [
                        'read',
                        'create',
                        'update',
                        'delete',
                    ]
                ],

                [
                    'name' => 'roles',
                    'display_name' => 'Roles',
                    'permissions' => [
                        'read',
                        'create',
                        'update',
                        'delete',
                    ]
                ],

                [
                    'name' => 'modules',
                    'display_name' => 'Modules',
                    'permissions' => [
                        'read',
                        'create',
                        'update',
                        'delete',
                    ]
                ],

                [
                    'name' => 'permissions',
                    'display_name' => 'Permissions',
                    'permissions' => [
                        'read',
                        'create',
                        'update',
                        'delete',
                    ]
                ],
            ];

            foreach ($modules as $module) {

                // Create module
                $moduleData = Module::where('name', $module['name'])->first();

                if (empty($moduleData)) {

                    $moduleData = new Module();
                    $moduleData->name = $module['name'];
                    $moduleData->display_name = $module['display_name'];
                    $moduleData->save();
                }

                // Create module permissions
                foreach ($module['permissions'] as $permission) {
                    $permissionData = $moduleData->permissions()->where('name', $permission)->first();

                    if (empty($permissionData)) {
                        $permissionData = new Permission();
                        $permissionData->module_id = $moduleData->id;
                        $permissionData->name = $permission;
                        $permissionData->display_name = $permission;
                        $permissionData->save();
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
