<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Models\AttractionPhoto;
use App\Models\Trip;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use App\Models\TripDay;
use App\Models\TripOffer;
use Carbon\Carbon;
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
            'trip_company_id' => 'required|exists:trip_companies',
            'destination'=>'required',
            'description'=>'required',
            'details'=>'required',
            'days_number'=>'required',
            'max_persons'=>'required',
            'start_age'=>'required',
            'end_age'=>'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $company = TripCompany::where('trip_admin_id','=',$request->user()->id)->first();
        if(!isset($company)){
            return $this->error('Company not found');
        }

        return $this->addTrip($request,$company->id);
    }


    protected function deleteCompany($id)
    {
        TripCompany::where('id','=',$id)->delete();
        return $this->success(null,'Company deleted successfully');
    }
    protected function tripCompanyDetails($id){
        $company = TripCompany::where('id','=',$id)
            ->with('admin')
            ->first();

        if(!isset($company)){
            return $this->error('Company not found');
        }

        return $this->success($company,'Company retrieved successfully');
    }
    protected function tripsForCompany($id){
        $trips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->paginate(6);

        return $this->success($trips,'Trips retrieved successfully');
    }
    protected function tripDetails($id)
    {
        $trip = Trip::where('id',$id)
            ->with(['photos','destination','services','activities','days',
                'dates',
                'offers'
            ])
            ->first();

        return $this->success($trip,'Trip retrieved successfully');
    }
    protected function editCompany($request,$id)
    {
        $trip_company = TripCompany::findOrFail($id);
        $trip_company->fill($request->all());
        $trip_company->save();

        return $this->success(null,'Company edited successfully');
    }
    protected function editTrip($request,$id)
    {
        $trip = Trip::findOrFail($id);
        $trip->fill($request->all());
        $trip->save();

        return $this->success(null,'Trip edited successfully');
    }
    protected function editDay($request,$id)
    {
        $day = TripDay::findOrFail($id);
        $day->fill($request->all());
        $day->save();

        return $this->success(null,'Day edited successfully');
    }
    protected function editOffer($request,$id)
    {
        $offer = TripOffer::findOrFail($id);
        $offer->fill($request->all());
        $offer->save();

        return $this->success(null,'Offer edited successfully');
    }
    protected function addTrip($request,$id)
    {
        $request['rate']=0;
        $request['num_of_ratings']=0;
        Trip::create($request->all());

        return $this->success(null,'Trip created successfully');
    }
}
