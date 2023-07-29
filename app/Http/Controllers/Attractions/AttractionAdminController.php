<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionPhoto;
use App\Models\AttractionReservation;
use App\Models\AttractionUpdating;
use App\Models\UpdateAcceptance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\DateTime;

class AttractionAdminController extends Controller
{
    // This controller contains all operations that the attraction admin can do on the attraction he's responsible for.

    /**
     * Add Attraction Company For The First Time
     * @param Request $request
     * @return JsonResponse
     */
    public function addAttractionCompany(Request $request): JsonResponse
    {
//        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();
        $add_request = AttractionUpdating::where('attraction_admin_id',$request->user()->id)
            ->where('rejected',0)->first();

        if(isset($add_request)){
            return $this->error('You no longer have the ability to add your company');
        }

        $validated_data = Validator::make($request->all(), [
            'city_id'=> 'required',
            'attraction_type_id'=> 'required',
            'name'=> 'required',
            'email'=> 'required',
//            'password'=> 'required',
            'location'=> 'required',
            'phone_number'=> 'required',
            'details'=> 'required',
            'open_at'=> 'required',
            'close_at'=> 'required',
            'Saturday'=> 'required',
            'Sunday'=> 'required',
            'Monday'=> 'required',
            'Tuesday'=> 'required',
            'Wednesday'=> 'required',
            'Thursday'=> 'required',
            'Friday'=> 'required',
            'website_url'=> 'required',
            'adult_price'=> 'required',
            'child_price'=> 'required',
            'child_ability_per_day'=> 'required',
            'adult_ability_per_day'=> 'required',
            'points_added_when_booking'=> 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $data = $request;
        $data['attraction_admin_id'] = $request->user()->id;
        $data['add_or_update'] = 0;
        $data['accepted'] = 0;
        $data['rejected'] = 0;
        $data['seen'] = 0;

        if(isset($request->Saturday)){
            $week = ['Saturday'=>$data['Saturday'], 'Sunday'=>$data['Sunday'],'Monday'=>$data['Monday'],
                'Tuesday'=>$data['Tuesday'],'Wednesday'=>$data['Wednesday'],'Thursday'=>$data['Thursday'],
                'Friday'=>$data['Friday'],];

            $data['available_days'] = $this->convertWeekArrayToBitmask($week);
        }

        AttractionUpdating::create($data->all());
        return $this->success(null,'Form sent successfully, pending approval.');
    }

    /**
     * Edit Attraction Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editAttractionDetails(Request $request): JsonResponse
    {
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();

        $data = $request;
        $data['attraction_admin_id'] = $request->user()->id;
        $data['attraction_id'] = $attraction['id'];
        $data['add_or_update'] = 1;
        $data['accepted'] = 0;
        $data['rejected'] = 0;
        $data['seen'] = 0;


        if(isset($request->Saturday)){
            $week = ['Saturday'=>$data['Saturday'], 'Sunday'=>$data['Sunday'],'Monday'=>$data['Monday'],
                'Tuesday'=>$data['Tuesday'],'Wednesday'=>$data['Wednesday'],'Thursday'=>$data['Thursday'],
                'Friday'=>$data['Friday'],];

            $data['available_days'] = $this->convertWeekArrayToBitmask($week);
        }

        AttractionUpdating::create($data->all());

        return $this->success(null,'Updates sent successfully, pending approval.');
        //return $this->editDetails($request,$id);
    }

    /**
     * Shows Attraction Details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttractionDetails(Request $request): JsonResponse
    {
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();
        return $this->attractionDetails($attraction['id']);
    }

    /**
     * Uploading Multiple Photos
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiplePhotos(Request $request): JsonResponse
    {
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();
        return $this->addMultiplePhotos($request,$attraction['id']);
    }

    /**
     * Uploading One Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadOnePhoto(Request $request): JsonResponse
    {
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();
        return $this->addPhoto($request,$attraction['id']);
    }

    /**
     * Deleting Some Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteOnePhoto(Request $request): JsonResponse
    {
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();

        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:attraction_photos',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $photo = AttractionPhoto::where('id','=',$request->id)->first();

        // checking if this photo belongs to the desired attraction
        if($attraction['id'] != $photo['attraction_id']){
            return $this->error('Unauthorized to delete this photo');
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
        $attraction = Attraction::where('attraction_admin_id','=',$request->user()->id)->first();

        $reservations = AttractionReservation::where('attraction_id','=',$attraction['id'])
            ->with([
                'user'=>function($q){
                     $q->select('id','first_name','last_name','email','phone_number');
                }
            ])
            ->orderBy('id','desc')
            ->paginate(10);
        return $this->success($reservations,'Reservations returned successfully');
    }

    // todo: get reservations using real time !!!!!



    // helpful functions

    protected function attractionDetails($id)
    {
        $attraction  = Attraction::where('id','=',$id)
            ->with([
                'photos',
                'type',
                'city',
                'city.country',
                'reviews',
                'admin',
            ])
            ->first();

        $attraction['available_days'] = $this->convertBitmaskToWeekArray($attraction['available_days']);

        return $this->success($attraction);
    }
    protected function editDetails($request,$id)
    {
        $week = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
        foreach($week as $day){
            if(isset($request->$day)){
                if($request->$day == 1){

                }
            }
        }
        $user = Attraction::findOrFail($id);
        $user->fill($request->all());
        $user->save();

        return $this->success(null,'Attraction updated successfully');
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
            AttractionPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$name,
                'attraction_id'=>$id,
            ]);
        }

        return $this->success(null,'Photos added successfully');
    }
    protected function addPhoto($request,$id)
    {
        if($request->hasFile('photo')) {
            $file_extension = $request->photo->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/attraction';
            $request->photo->move($path, $file_name);
            AttractionPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$file_name,
                'attraction_id'=>$id,
            ]);
        }
        return $this->success(null,'Photo added successfully');
    }
    protected function deletePhoto($id){
        AttractionPhoto::where('id','=',$id)->delete();
        return $this->success(null,'Photo deleted successfully');
    }
}
