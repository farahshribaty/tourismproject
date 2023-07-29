<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Trips\TripAdminController;
use App\Models\TripCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripController extends TripAdminController
{
    // This controller contains all operations the main admin can do on the Trip section.


    /**
     * Show All Companies
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTripCompanies(): JsonResponse
    {
        $companies = TripCompany::paginate(10);
        return $this->success($companies, 'Retrieved successfully');
    }

    /**
     * Show Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function getTripCompanyDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trip_companies',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->tripCompanyDetails($request->id);
    }

    /**
     * Edit Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editCompanyDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trip_companies',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->editCompany($request, $request->id);
    }

    /**
     * Delete Some Company
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTheCompany(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trip_companies',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->deleteCompany($request->id);
    }

}
