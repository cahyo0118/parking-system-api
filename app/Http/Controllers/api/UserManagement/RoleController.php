<?php

namespace App\Http\Controllers\api\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Utils\QueryHelpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function getAll(Request $request)
    {
        $rawData = QueryHelpers::getData($request, new Role());

        return response()->json([
            'success' => true,
            'body' => $rawData['data'],
            'message' => 'Successfully get data',
            /*Pagination*/
            'from' => ($request->offset + 1),
            'to' => ($request->offset + count($rawData['data'])),
            'total' => $rawData['count'],
        ]);
    }

    public function getOne(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Role());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Successfully get data',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'body' => $data,
            'message' => 'Successfully get data',
        ]);
    }

    public function store(Request $request)
    {

        $validators = Validator::make($request->all(), [
            "name" => "required",
            "display_name" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to store data',
            ], 400);
        }

        $data = new Role();
        $data->name = $request->name;
        $data->display_name = $request->display_name;
        $data->description = $request->description;
        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => 'Successfully update data',
        ]);
    }

    public function update(Request $request, $id)
    {

        $validators = Validator::make($request->all(), [
            "name" => "required",
            "display_name" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to update data',
            ], 400);
        }

        $data = QueryHelpers::getOneById($id, $request, new Role());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed to update data',
            ], 400);
        }

        $data->name = $request->name;
        $data->display_name = $request->display_name;
        $data->description = $request->description;

        $data->save();

        return response()->json([
            'success' => true,
            'body' => $data,
            'message' => 'Successfully update data',
        ]);
    }

    public function updatePermissions(Request $request, $id)
    {

        $validators = Validator::make($request->all(), [
            "is_permit_all" => "required",
            // "permissions" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to update data',
            ], 400);
        }

        DB::beginTransaction();

        try {

            $data = QueryHelpers::getOneById($id, $request, new Role());

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'body' => null,
                    'message' => 'Failed to update data, data not found',
                ], 400);
            }

            $data->permissions()->detach();

            if (empty($request->is_permit_all)) {
                $role_permissions = [];

                foreach ($request->permissions as $permission) {
                    $role_permissions[$permission['id']] = ['module_id' => $permission['module_id']];
                }

                $data->permissions()->attach($role_permissions);
            } else {
                // $role_permissions = [];

                $permissions = Permission::all();

                foreach ($permissions as $permission) {
                    $role_permissions[$permission['id']] = ['module_id' => $permission['module_id']];
                }

                // error_log(json_encode($role_permissions));

                $data->permissions()->attach($role_permissions);

                $data->is_permit_all = true;
                $data->save();
                
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'body' => $data,
                'message' => 'Successfully update data',
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to update data',
            ], 400);
        }
    }

    public function delete(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Role());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed to delete data',
            ], 400);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'body' => $data,
            'message' => __('messages.success.delete'),
        ]);
    }
}
