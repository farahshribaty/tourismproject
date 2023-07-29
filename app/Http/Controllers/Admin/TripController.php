<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Trips\TripAdminController;
use App\Models\Trip;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use App\Models\TripUpdating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripController extends TripAdminController
{
    // This controller contains all operations the main admin can do on the Trip section.


    /**
     * Make New Admin For Trip Company
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

        TripAdmin::create([
            'user_name'=>$request->user_name,
            'password'=>$request->password,
        ]);

        $attraction = TripAdmin::where('user_name','=',$request->user_name)->first();
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
        $updates = TripUpdating::when($request->accepted == 1, function($q){
            $q->where('accepted','=',1);
            })
            ->when($request->rejected == 1,function($q){
                $q->where('rejected','=',1);
            })
            ->when($request->unseen_only == 1,function($q){
                $q->where('seen','=',0);
            })
            ->get();
        return $this->success($updates, 'Updates retrieved successfully');
    }

    /**
     * Accepting An Update (Adding/Editing Trip)
     * @param Request $request
     * @return JsonResponse
     */
    public function acceptTripCompanyUpdate(Request $request): JsonResponse  // both adding or updating an attraction
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trip_updatings',
            'accepted' => 'required',
            'rejected' => 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $updates = TripUpdating::where('id','=',$request->id)->first()->toArray();

        if($updates['accepted']==1 || $updates['rejected']==1){
            return $this->error('You can not accept/reject this update twice');
        }

        if($request->accepted == $request->rejected){
            return $this->error('Select one: accept or reject');
        }


        if($request->rejected == 1){            // if the updates/adds rejected
            TripUpdating::where('id','=',$request->id)->update([
                'rejected'=> 1,
            ]);

            return $this->success(null,'Updates rejected successfully');
        }


        if(!isset($updates)){
            return $this->error('Updates not found');
        }

        TripUpdating::where('id','=',$request->id)->update(['accepted'=>1]);

        if($updates['add_or_update']){       // updating a trip company
            foreach($updates as $key=>$value){
                if($value == null) unset($updates[$key]);
            }
            $trip_company = TripCompany::findOrFail($updates['trip_company_id']);
            $trip_company->fill($updates);
            $trip_company->save();

            return $this->success(null,'Updates accepted successfully');
        }
        else{       // adding new attraction
            Tripcompany::create($updates);
            return $this->success(null,'Tripp company accepted successfully');
        }
    }

    /**
     * Show All Companies
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTripCompanies(): JsonResponse
    {
        $companies = TripCompany::with('admin')->paginate(10);
        return $this->success($companies, 'Retrieved successfully');
    }

    /**
     * Show All Admins With Their Attraction Company
     * @return JsonResponse
     */
    public function getAllTripAdmins(): JsonResponse
    {
        $admins = TripAdmin::with([
            'tripCompany'=>function($q){
                $q->select('id','name','trip_admin_id');
            }
        ])->paginate(10);
        return $this->success($admins,'Admins retrieved successfully');
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
