<?php

namespace App\Http\Controllers\api\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Utils\QueryHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function getAll(Request $request)
    {
        $rawData = QueryHelpers::getData($request, new Permission());

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
        $data = QueryHelpers::getOneById($id, $request, new Permission());

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
            "module_id" => "required",
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

        $data = new Permission();
        $data->module_id = $request->module_id;
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
            "module_id" => "required",
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

        $data = QueryHelpers::getOneById($id, $request, new Permission());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed to update data',
            ], 400);
        }

        $data->module_id = $request->module_id;
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

    public function delete(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Permission());

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
