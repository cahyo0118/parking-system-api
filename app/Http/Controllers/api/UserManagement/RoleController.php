<?php

namespace App\Http\Controllers\api\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Utils\QueryHelpers;
use Illuminate\Http\Request;
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
                'message' => __('messages.failed.post'),
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
            'message' => __('messages.success.post'),
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
                'message' => __('messages.failed.put'),
            ], 400);
        }

        $data = QueryHelpers::getOneById($id, $request, new Role());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => __('messages.failed.put'),
            ], 400);
        }

        $data->name = $request->name;
        $data->display_name = $request->display_name;
        $data->description = $request->description;

        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => __('messages.success.post'),
        ]);
    }

    public function delete(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Role());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => __('messages.failed.delete'),
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
