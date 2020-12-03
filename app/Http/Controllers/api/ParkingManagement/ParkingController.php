<?php

namespace App\Http\Controllers\api\ParkingManagement;

use App\Exports\ParkingExport;
use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\ParkingFee;
use App\Utils\QueryHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ParkingController extends Controller
{
    public function getAll(Request $request)
    {

        $data = new Parking();
        $dataCount = new Parking();

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        if (empty(Parking::$searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {
                    if ($filter != null && $filter != "") {

                        if ($filterIndex == 'start_date') {
                            $data = $data->where('created_at', ">=", $filter);
                            $dataCount = $dataCount->where('created_at', ">=", $filter);
                        } else if ($filterIndex == 'finish_date') {
                            $data = $data->where('created_at', "<=", $filter);
                            $dataCount = $dataCount->where('created_at', "<=", $filter);
                        } else {
                            $data = $data->where($filterIndex, "=", $filter);
                            $dataCount = $dataCount->where($filterIndex, "=", $filter);
                        }
                    }
                }
            }

            if (!empty($request->orderBy)) {
                $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
            }

            if ($request->limit != null && $request->offset != null) {
                $data = $data->limit($request->limit);
                $data = $data->offset($request->offset);
            }
        } else {


            foreach ((array)Parking::$searchable as $searchableFieldName) {

                /*Initialize query conditional*/
                $data = $data->orWhereRaw("1 = 1");
                $data = $data->where($searchableFieldName, "LIKE", "%{$request->keyword}%");

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            if ($filterIndex == 'start_date') {
                                $data = $data->where('created_at', ">=", $filter);
                                $dataCount = $dataCount->where('created_at', ">=", $filter);
                            } else if ($filterIndex == 'finish_date') {
                                $data = $data->where('created_at', "<=", $filter);
                                $dataCount = $dataCount->where('created_at', "<=", $filter);
                            } else {
                                $data = $data->where($filterIndex, "=", $filter);
                                $dataCount = $dataCount->where($filterIndex, "=", $filter);
                            }
                        }
                    }
                }

                if (!empty($request->orderBy)) {
                    $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
                }

                if ($request->limit != null && $request->offset != null) {
                    $data = $data->limit($request->limit);
                    $data = $data->offset($request->offset);
                }
            }
        }

        return response()->json([
            'success' => true,
            'body' => $data->get(),
            'message' => 'Successfully get data',
            /*Pagination*/
            'from' => ($request->offset + 1),
            'to' => ($request->offset + count($data->get())),
            'total' => $dataCount->count(),
        ]);
    }

    public function getOne(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Parking());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed get data',
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
            "vehicle_type" => "required",
            "vehicle_police_number" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to store data',
            ], 400);
        }

        $parking_fee = ParkingFee::where('vehicle_type', $request->vehicle_type)->latest()->first();

        if (empty($parking_fee)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed save data, parking fee not found',
            ], 400);
        }

        $parking = Parking::where('vehicle_police_number', $request->vehicle_police_number)
            ->where('status', 'in')
            ->first();

        if (!empty($parking)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed save data, vehicle is already registered',
            ], 400);
        }

        $data = new Parking();
        $data->code = Str::upper(Str::random(10));
        $data->vehicle_type = $request->vehicle_type;
        $data->vehicle_police_number = $request->vehicle_police_number;
        $data->parking_fee = $parking_fee->parking_fee;
        $data->status = 'in';
        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => 'Successfully save data',
        ]);
    }

    public function updateStatus(Request $request, $id, $status)
    {

        $data = QueryHelpers::getOneById($id, $request, new Parking());

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'body' => null,
                'message' => 'Failed to update data',
            ], 400);
        }

        $data->status = $status;
        $data->out_at = Carbon::now();

        $data->save();

        return response()->json([
            'success' => true,
            'body' => null,
            'message' => 'Successfully update data',
        ]);
    }

    public function delete(Request $request, $id)
    {
        $data = QueryHelpers::getOneById($id, $request, new Parking());

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

    public function export(Request $request)
    {
        $data = new Parking();
        $dataCount = new Parking();

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        if (empty(Parking::$searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {
                    if ($filter != null && $filter != "") {

                        if ($filterIndex == 'start_date') {
                            $data = $data->where('created_at', ">=", $filter);
                            $dataCount = $dataCount->where('created_at', ">=", $filter);
                        } else if ($filterIndex == 'finish_date') {
                            $data = $data->where('created_at', "<=", $filter);
                            $dataCount = $dataCount->where('created_at', "<=", $filter);
                        } else {
                            $data = $data->where($filterIndex, "=", $filter);
                            $dataCount = $dataCount->where($filterIndex, "=", $filter);
                        }
                    }
                }
            }

            if (!empty($request->orderBy)) {
                $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
            }

            if ($request->limit != null && $request->offset != null) {
                $data = $data->limit($request->limit);
                $data = $data->offset($request->offset);
            }
        } else {


            foreach ((array)Parking::$searchable as $searchableFieldName) {

                /*Initialize query conditional*/
                $data = $data->orWhereRaw("1 = 1");
                $data = $data->where($searchableFieldName, "LIKE", "%{$request->keyword}%");

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            if ($filterIndex == 'start_date') {
                                $data = $data->where('created_at', ">=", $filter);
                                $dataCount = $dataCount->where('created_at', ">=", $filter);
                            } else if ($filterIndex == 'finish_date') {
                                $data = $data->where('created_at', "<=", $filter);
                                $dataCount = $dataCount->where('created_at', "<=", $filter);
                            } else {
                                $data = $data->where($filterIndex, "=", $filter);
                                $dataCount = $dataCount->where($filterIndex, "=", $filter);
                            }
                        }
                    }
                }

                if (!empty($request->orderBy)) {
                    $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
                }

                if ($request->limit != null && $request->offset != null) {
                    $data = $data->limit($request->limit);
                    $data = $data->offset($request->offset);
                }
            }
        }

        $parking_export = new ParkingExport();
        $parking_export->setData($data->get());

        return Excel::download($parking_export, 'parking.xlsx');
    }
}
