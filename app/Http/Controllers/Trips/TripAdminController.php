<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Models\AttractionPhoto;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripAdminController extends Controller
{
    // This controller contains all operations that the attraction admin can do on the attraction he's responsible for.


    public function adminRegister(Request $request)   // ########### Unofficial ################
    {
        TripAdmin::create([
            'trip_company_id'=>$request->trip_company_id,
            'user_name'=>$request->user_name,
            'password' => $request->password,
        ]);

        $admin = TripAdmin::where('user_name', '=', $request->user_name)->first();
        $admin['token'] = $admin->createToken('MyApp')->accessToken;
        return response()->json([
            'date' => $admin,
        ]);
    }

    public function dashboard(Request $request)   // ########### Unofficial ################
    {
        return $request->user();
    }


    /**
     * Show Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function getTripCompanyDetails(Request $request): JsonResponse
    {
        $id = $request->user()->trip_company_id;
        return $this->tripCompanyDetails($id);
    }

    /**
     * Edit Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editCompanyDetails(Request $request): JsonResponse
    {
        $id = $request->user()->trip_company_id;
        return $this->editCompany($request,$id);
    }

    /**
     * Delete The Company
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTheCompany(Request $request): JsonResponse
    {
        $id = $request->user()->trip_company_id;
        return $this->deleteCompany($id);
    }

    public function addNewTrip(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [

        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }
        $company_id = $request->user()->trip_company_id;
    }


    protected function deleteCompany($id)
    {
        TripCompany::where('id','=',$id)->delete();
        return $this->success(null,'Company deleted successfully');
    }
    protected function editCompany($request,$id)
    {
        $user = TripCompany::findOrFail($id);
        $user->fill($request->all());
        $user->save();

        return $this->success(null,'Company edited successfully');
    }
    protected function tripCompanyDetails($id){
        $company = TripCompany::where('id','=',$id)->first();

        if(!isset($company)){
            return $this->error('Company not found');
        }

        return $this->success($company,'Company retrieved successfully');
    }
}
