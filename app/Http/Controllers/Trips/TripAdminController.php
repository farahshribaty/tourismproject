<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Models\AttractionPhoto;
use App\Models\Trip;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use App\Models\TripDate;
use App\Models\TripDay;
use App\Models\TripOffer;
use App\Models\TripPhoto;
use App\Models\TripsReservation;
use App\Models\TripUpdating;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripAdminController extends Controller
{
    // This controller contains all operations that the attraction admin can do on the attraction he's responsible for.

    public function getUpdatingList(Request $request): JsonResponse
    {
        $updates = TripUpdating::where('trip_admin_id', $request->user()->id)
            ->when($request->accepted == 1, function ($q) {
                $q->where('accepted', '=', 1);
            })
            ->when($request->rejected == 1, function ($q) {
                $q->where('rejected', '=', 1);
            })
            ->when($request->unseen_only == 1, function ($q) {
                $q->where('seen', '=', 0);
            })
            ->get();
        return $this->success($updates, 'Updates retrieved successfully');
    }

    /**
     * Show Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function getTripCompanyDetails(Request $request): JsonResponse
    {
        $company = TripCompany::where('trip_admin_id', $request->user()->id)->first();
        return $this->tripCompanyDetails($company['id']);
    }

    public function getAllTrips(Request $request): JsonResponse
    {
        $company = TripCompany::where('trip_admin_id', $request->user()->id)->first();
        return $this->tripsForCompany($company['id']);
    }

    public function getTripDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if (!$this->hasTrip($request->trip_id, $request->user()->id)) {
            return $this->error('Unauthorized to view this trip!', 403);
        }

        return $this->tripDetails($request->trip_id);
    }

    public function getTripDates(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if (!$this->hasTrip($request->trip_id, $request->user()->id)) {
            return $this->error('Unauthorized to view this trip!', 403);
        }

        return $this->tripDates($request->trip_id);
    }

    public function getLatestReservations(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if(!$this->hasTrip($request->trip_id,$request->user()->id)){
            return $this->error('Unauthorized to reach this trip.',403);
        }

        return $this->latestReservations($request->trip_id);

    }

    /**
     * Edit Company Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editCompanyDetails(Request $request): JsonResponse
    {
        $company = TripCompany::where('trip_admin_id', '=', $request->user()->id)->first();

        $data = $request;
        $data['trip_admin_id'] = $request->user()->id;
        $data['trip_company_id'] = $company['id'];
        $data['add_or_update'] = 1;
        $data['accepted'] = 0;
        $data['rejected'] = 0;
        $data['seen'] = 0;

        TripUpdating::create($data->all());

        return $this->success(null, 'Updates sent successfully, pending approval.');
    }

    /**
     * Edit Trip Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editTripDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trips',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if(!$this->hasTrip($request->id,$request->user()->id)){
            return $this->error('Unauthorized to reach this trip!',403);
        }

        return $this->editTrip($request, $request->id);
    }


    /**
     * Edit Offer Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editOfferDetails(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:trip_offers',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        return $this->editOffer($request, $request->id);

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


    /**
     * Adding One Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadOnePhoto(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'trip_id'=>'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if(!$this->hasTrip($request->trip_id,$request->user()->id)){
            return $this->error('Unauthorized to add to this trip!',403);
        }

        return $this->addOnePhoto($request,$request->trip_id);
    }

    /**
     * Uploading Multiple Photos
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiplePhotos(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'trip_id'=>'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if(!$this->hasTrip($request->trip_id,$request->user()->id)){
            return $this->error('Unauthorized to add to this trip!',403);
        }

        $names=array();

        return $this->addMultiplePhotos($request,$request->trip_id);
    }

    /**
     * Deleting One Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteOnePhoto(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'photo_id'=>'required',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if(!$this->hasPhoto($request->photo_id,$request->user()->id)){
            return $this->error('Unauthorized to delete this photo!',403);
        }

        return $this->deletePhoto($request->photo_id);
    }


    // Helpful functions

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
            ->where('trip_company_id',$id)
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
    protected function tripDates($id)
    {
        $dates = TripDate::where('trip_id','=',$id)->get();
        return $this->success($dates,'Dates retrieved successfully');
    }
    protected function latestReservations($id)
    {
        $reservations = TripsReservation::select(['date_id','user_id','child','adult','points_added','money_spent','active','departure_date'])
            ->join('trip_dates','trips_reservations.date_id','=','trip_dates.id')
            ->where('trip_dates.trip_id',$id)
            ->orderBy('trips_reservations.id','desc')
            ->get();

        return $this->success($reservations,'Reservations retrieved successfully');
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
        $data = $request;
        if(isset($data['rate'])){
            unset($data['rate']);
        }
        if(isset($data['num_of_ratings'])){
            unset($data['num_of_ratings']);
        }
        $trip = Trip::findOrFail($id);
        $trip->fill($data->all());
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
        $trip = Trip::create($request->all());

        return $trip;
    }
    protected function addDay($data,$idx,$trip_id){
        TripDay::create([
            'trip_id'=> $trip_id,
            'day_number'=> $idx,
            'title'=> $data['title_'.$idx],
            'details'=> $data['details_'.$idx],
        ]);
    }
    protected function addDate($request)
    {
        $request['current_reserved_people'] = 0;
        TripDate::create($request->all());
        return $this->success(null,'Date added successfully');
    }
    protected function addMultiplePhotos($request,$id)
    {
        $names=array();

        if($files=$request->photos){
            foreach($files as $file){
                $extension = $file->getClientOriginalName();
                $name = time().$extension;
                $file->move('images/attraction',$name);
                $names[]=$name;
            }
        }

        foreach($names as $name){
            TripPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$name,
                'trip_id'=>$id,
            ]);
        }

        return $this->success(null,'Photos added successfully');
    }
    protected function addOnePhoto($request,$id)
    {
        if($request->hasFile('photo')) {
            $file_extension = $request->photo->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/attraction';
            $request->photo->move($path, $file_name);
            TripPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$file_name,
                'trip_id'=>$request->trip_id,
            ]);
        }
        return $this->success(null,'Photo added successfully');
    }
    protected function deleteCompany($id)
    {
        TripCompany::where('id','=',$id)->delete();
        return $this->success(null,'Company deleted successfully');
    }
    protected function deleteTrip($id)
    {
        Trip::where('id','=',$id)->delete();
        return $this->success(null,'Trip deleted successfully');
    }
    protected function deleteOffer($id)
    {
        TripOffer::where('id','=',$id)->delete();
        return $this->success(null,'Offer deleted successfully');
    }
    protected function deleteDate($id)
    {
        TripDate::where('id','=',$id)->delete();
        return $this->success(null,'Date deleted successfully');
    }
    protected function deletePhoto($id)
    {
        TripPhoto::where('id',$id)->delete();
        return $this->success(null,'Photo deleted successfully');
    }
    protected function deleteDay($id)
    {
        TripDate::where('id',$id)->delete();
        return $this->success(null,'Day deleted successfully');
    }

    // Authorization functions

    private function hasPhoto($id,$admin_id): bool
    {
        $photo = TripPhoto::where('id',$id)->first();
        if(!isset($photo)) return false;
        return $this->hasTrip($photo['trip_id'],$admin_id);
    }
    private function hasTrip($id,$admin_id): bool
    {
        $trip = Trip::where('id',$id)->first();
        if(!isset($trip)) return false;
        return $this->hasCompany($trip['trip_company_id'],$admin_id);
    }
    private function hasCompany($id,$admin_id): bool
    {
        $company = TripCompany::where('id',$id)->first();
        if(!isset($company)) return false;
        return ($company['trip_admin_id'] == $admin_id);
    }
}
