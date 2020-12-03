<?php

namespace App\Http\Controllers\api\ParkingManagement;

use App\Http\Controllers\Controller;
use App\Models\ParkingFee;
use App\Utils\QueryHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParkingFeeController extends Controller
{
    public function getAll(Request $request)
    {
        $rawData = QueryHelpers::getData($request, new ParkingFee());

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
        $data = QueryHelpers::getOneById($id, $request, new ParkingFee());

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

    public function getCurrentByVehicleType(Request $request, $vehicle_type)
    {
        $data = ParkingFee::where('vehicle_type', $vehicle_type)->latest()->first();

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

    public function updateByVehicleType(Request $request, $vehicle_type)
    {

        $validators = Validator::make($request->all(), [
            "parking_fee" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to update data',
            ], 400);
        }

        $data = new ParkingFee();
        $data->vehicle_type = $vehicle_type;
        $data->parking_fee = $request->parking_fee;
        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => 'Successfully update data',
        ]);
    }

}
