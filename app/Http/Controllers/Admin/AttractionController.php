<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Attractions\AttractionAdminController;
use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttractionController extends AttractionAdminController
{
    // This controller contains all operations the main admin can do on the Attraction section.

    /**
     * Shows All Attractions
     * @return JsonResponse
     */
    public function getAllAttractions(): JsonResponse
    {
        $attractions = Attraction::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$attractions,
        ]);
    }



    /**
     * Shows Attraction Details
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttractionDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->attractionDetails($request->id);
    }

    /**
     * Edit Attraction Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editAttractionDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->editDetails($request,$request->id);
    }

    /**
     * Delete An Attraction
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAttraction(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        Attraction::where('id','=',$request->id)->delete();

        return $this->success(null,'Attraction deleted successfully');
    }

    /**
     * Uploading Multiple Photos
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiplePhotos(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->addMultiplePhotos($request,$request->id);
    }

    /**
     * Uploading One Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadOnePhoto(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->addPhoto($request,$request->id);
    }

    /**
     * Deleting Some Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteOnePhoto(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attraction_photos',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->deletePhoto($request->id);
    }

    public function getLatestReservations(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attractions',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $reservations = AttractionReservation::where('attraction_id','=',$request->id)
            ->with([
                'user'=>function($q){
                    $q->select('id','first_name','last_name','email','phone_number');
                }
            ])
            ->orderBy('id','desc')
            ->paginate(10);
        return $this->success($reservations,'Reservations returned successfully');
    }

}
