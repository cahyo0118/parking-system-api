<?php

namespace App\Http\Controllers\api\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Utils\QueryHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        $rawData = QueryHelpers::getData($request, new User());

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
        $data = QueryHelpers::getOneById($id, $request, new User());

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
            "email" => "required",
            "password" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to store data',
            ], 400);
        }

        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
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
            "email" => "required",
            "password" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to update data',
            ], 400);
        }

        $data = QueryHelpers::getOneById($id, $request, new User());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed to update data',
            ], 400);
        }

        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);

        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => 'Successfully update data',
        ]);
    }

    public function delete(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new User());

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

    public function addRole(Request $request, $id, $role_id)
    {
        $data = QueryHelpers::getOneById($id, $request, new User());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed update data',
            ], 400);
        }

        $role = QueryHelpers::getOneById($role_id, $request, new Role());

        if (empty($role)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed update data, role not found',
            ], 400);
        }

        if (empty($data->roles()->where('role_id', $role_id)->first())) {
            $data->roles()->attach($role_id);
        }

        return response()->json([
            'success' => true,
            'body' => $data,
            'message' => 'Successfully update data',
        ]);
    }

    public function removeRole(Request $request, $id, $role_id)
    {
        $data = QueryHelpers::getOneById($id, $request, new User());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed update data',
            ], 400);
        }

        $role = QueryHelpers::getOneById($role_id, $request, new Role());

        if (empty($role)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed update data, role not found',
            ], 400);
        }

        $data->roles()->detach($role_id);

        return response()->json([
            'success' => true,
            'body' => $data,
            'message' => 'Successfully update data',
        ]);
    }
}
