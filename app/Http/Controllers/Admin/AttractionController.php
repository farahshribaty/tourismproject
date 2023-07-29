<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Attractions\AttractionAdminController;
use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionReservation;
use App\Models\AttractionUpdating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttractionController extends AttractionAdminController
{
    // This controller contains all operations the main admin can do on the Attraction section.

    /**
     * Adding New Attraction Admin
     * @param Request $request
     * @return JsonResponse
     */
    public function makeNewAdmin(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'user_name' => 'required|unique:attraction_admins',
            'password' => 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        AttractionAdmin::create([
            'user_name'=>$request->user_name,
            'password'=>$request->password,
        ]);

        $attraction = AttractionAdmin::where('user_name','=',$request->user_name)->first();
        $attraction['token'] = $attraction->createToken('MyApp')->accessToken;

        return $this->success($attraction,'Admin added successfully');
    }

    /**
     * Getting Updating List
     * @param Request $request
     * @return JsonResponse
     */
    public function getUpdatingList(Request $request): JsonResponse
    {
        $updates = AttractionUpdating::where('accepted', '=', 0)->get();
        return $this->success($updates, 'Updates retrieved successfully');
    }

    /**
     * Accepting An Update (Adding/Editing Attraction)
     * @param Request $request
     * @return JsonResponse
     */
    public function acceptingAttraction(Request $request): JsonResponse  // both adding or updating an attraction
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attraction_updatings',
            'accepted' => 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if($request->rejected == 1){
            AttractionUpdating::where('id','=',$request->id)->update([
                'rejected'=> 1,
            ]);

            return $this->success(null,'Updates rejected successfully');
        }

        $updates = AttractionUpdating::where('id','=',$request->id)->first()->toArray();

        if(!isset($updates)){
            return $this->error('Updates not found');
        }

        AttractionUpdating::where('id','=',$request->id)->update(['accepted'=>1]);

        if($updates['add_or_update']){       // updating an attraction
            foreach($updates as $key=>$value){
                if($value == null) unset($updates[$key]);
            }
            $user = Attraction::findOrFail($updates['attraction_id']);
            $user->fill($updates);
            $user->save();

            return $this->success(null,'Updates accepted successfully');
        }
        else{       // adding new attraction
            $updates['rate'] = 0;
            $updates['num_of_ratings'] = 0;
            Attraction::create($updates);
            return $this->success(null,'Attraction company accepted successfully');
        }
    }

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

    /**
     * Getting The Latest Reservations
     * @param Request $request
     * @return JsonResponse
     */
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
