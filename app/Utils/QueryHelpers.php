<?php

namespace App\Utils;

use Illuminate\Support\Str;

class QueryHelpers
{
    public static function getData($request, $model)
    {
        $data = $model;
        $dataCount = $model;

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        if (empty($model::$searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {
                    if ($filter != null && $filter != "") {
                        $data = $data->where($filterIndex, "=", $filter);
                        $dataCount = $dataCount->where($filterIndex, "=", $filter);
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

            $data = $model;
            $dataCount = $model;

            foreach ((array)$model::$searchable as $searchableFieldName) {

                /*Initialize query conditional*/
                $data = $data->orWhereRaw("1 = 1");
                $data = $data->where($searchableFieldName, "LIKE", "%{$request->keyword}%");

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            $data = $data->where($filterIndex, "=", $filter);
                        }
                    }
                }

                
                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            $dataCount = $dataCount->where($filterIndex, "=", $filter);
                        }
                    }
                
                }
                $dataCount = $dataCount->orWhereRaw("1 = 1");
                $dataCount = $dataCount->where($searchableFieldName, "LIKE", "%{$request->keyword}%");

                if (!empty($request->orderBy)) {
                    $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
                }

                if ($request->limit != null && $request->offset != null) {
                    $data = $data->limit($request->limit);
                    $data = $data->offset($request->offset);
                }
            }
        }

        return [
            'count' => $dataCount->count(),
            'data' => $data->get()
        ];

    }

    public static function getDataQueryBuilder($request, $model, $data)
    {

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        if (empty($model::$searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {


                    if ($filter != null && $filter != "") {

                        $data = $data->where($filterIndex, "=", $filter);

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

            foreach ((array)$model::$searchable as $searchableFieldIndex => $searchableFieldName) {

                /*Initialize query conditional*/
                if ($searchableFieldIndex > 0) {
                    $data = $data->orWhere(Str::lower($searchableFieldName), "LIKE", Str::lower("%{$request->keyword}%"));
                } else {
                    $data = $data->where(Str::lower($searchableFieldName), "LIKE", Str::lower("%{$request->keyword}%"));
                }

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            $data = $data->where($filterIndex, "=", $filter);
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

        return [
            'count' => $data->count(),
            'data' => $data->get()
        ];

    }


    public static function getNoRelationDataQueryBuilder($request, $model, $data)
    {

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

//        if (!empty($request->with) && count($request->with) > 0) {
//            $data = $data->with($request->with);
//        }

        if (empty($model::$searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {
                    if ($filter != null && $filter != "") {
                        $data = $data->where($filterIndex, "=", $filter);
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

            foreach ((array)$model::$searchable as $searchableFieldIndex => $searchableFieldName) {

                /*Initialize query conditional*/
                if ($searchableFieldIndex > 0) {
                    $data = $data->orWhere(Str::lower($searchableFieldName), "LIKE", Str::lower("%{$request->keyword}%"));
                } else {
                    $data = $data->where(Str::lower($searchableFieldName), "LIKE", Str::lower("%{$request->keyword}%"));
                }

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            $data = $data->where($filterIndex, "=", $filter);
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

        return [
            'count' => $data->count(),
            'data' => $data->get()
        ];

    }

    public static function getRelationQueryBuilder($request, $data, $searchable = [])
    {

        if (!empty($request->filters)) {
            $filters = json_decode($request->filters, true);
        }

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (empty($searchable) || empty($request->keyword)) {

            if (!empty($filters)) {
                foreach ($filters as $filterIndex => $filter) {
                    if ($filter != null && $filter != "") {
                        $data = $data->where($filterIndex, "=", $filter);
                    }
                }
            }

            if (!empty($request->orderBy)) {
                $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
            }

            if (!empty($request->limit) && !empty($request->offset)) {
                $data = $data->limit($request->limit);
                $data = $data->offset($request->offset);
            }
        } else {

            foreach ((array)$searchable as $searchableFieldIndex => $searchableFieldName) {
                error_log("searchable -> {$searchableFieldName} -> {$request->keyword} -> {$searchableFieldIndex}");

                /*Initialize query conditional*/
                if ($searchableFieldIndex > 0) {
                    $data = $data->orWhereRaw("1 = 1");
                }

                $data = $data->where($searchableFieldName, "LIKE", "%{$request->keyword}%");

                if (!empty($filters)) {
                    foreach ($filters as $filterIndex => $filter) {
                        if ($filter != null && $filter != "") {
                            $data = $data->where($filterIndex, "=", $filter);
                        }
                    }
                }

                if (!empty($request->orderBy)) {
                    $data = $data->orderBy($request->orderBy, empty($request->orderType) ? 'desc' : $request->orderType);
                }


                if (!empty($request->limit) && !empty($request->offset)) {
                    $data = $data->limit($request->limit);
                    $data = $data->offset($request->offset);
                }
            }
        }

        return [
            'count' => $data->count(),
            'data' => $data->get()
        ];

    }

    public static function getOneById($id, $request, $model)
    {
        $data = $model;

        $data = $data->where("id", $id);

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        return $data->first();

    }

    public static function getOneByQueryBuilder($data, $request)
    {

        if (!empty($request->with) && count($request->with) > 0) {
            $data = $data->with($request->with);
        }

        if (!empty($request->withCount) && count($request->withCount) > 0) {
            $data = $data->withCount($request->withCount);
        }

        return $data->first();

    }

}
