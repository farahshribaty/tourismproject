<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionPhoto;
use App\Models\AttractionReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nette\Utils\DateTime;

class AttractionAdminController extends Controller
{
    //

    public function adminRegister(Request $request)
    {
        AttractionAdmin::create([
            'user_name'=>$request->user_name,
            'password'=>$request->password,
            'attraction_id'=>2,
        ]);

        $attraction = AttractionAdmin::where('user_name','=',$request->user_name)->first();
        $attraction['token'] = $attraction->createToken('MyApp')->accessToken;
        return response()->json([
            'data'=>$attraction,
        ]);
    }

    public function dashboard(Request $request)
    {
        return $request->user();
    }

    public function addAttraction(Request $request)
    {

        $request->validate([
            'open_at'=>'required',
            'close_at'=>'required',
            'email'=>'required|unique:attractions',
        ]);

        $att = Attraction::create([
            'city_id'=>1,
            'attraction_type_id'=>1,
            'name'=>'hello',
            'email'=>$request->email,
            'password'=>'helloh',
            'location'=>'damascus',
            'phone_number'=>324354,
            'rate'=>3,
            'num_of_ratings'=>23,
            'open_at'=> $request->open_at,
            'close_at'=> $request->open_at,
            'available_days'=>1010101,
            'child_ability_per_day'=>34,
            'adult_ability_per_day'=>34,
            'details'=>'hello',
            'website_url'=>'ejlksjf',
            'adult_price'=>354,
            'child_price'=>343,
            'points_added_when_booking'=>43,
        ]);

        $date = $att['open_at'];

        $new_date = DateTime::createfromformat('Y-m-d H:i:s',$date);
        $att['open_at'] = $new_date->format('H:i');

        //      $att['open_at']=$att['open_at']->format('H:i:s');

        return response()->json([
            $att,
        ]);
    }




    /**
     * Shows Attraction Details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttractionDetails(Request $request): JsonResponse
    {
        $id = $request->user()->attraction_id;
        return $this->attractionDetails($id);
    }

    /**
     * Edit Attraction Details
     * @param Request $request
     * @return JsonResponse
     */
    public function editAttractionDetails(Request $request): JsonResponse
    {
        $id = $request->user()->attraction_id;
        return $this->editDetails($request,$id);
    }

    /**
     * Uploading Multiple Photos
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiplePhotos(Request $request): JsonResponse
    {
        $id = $request->user()->id;
        return $this->addMultiplePhotos($request,$id);
    }

    /**
     * Uploading One Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadOnePhoto(Request $request): JsonResponse
    {
        $id = $request->user()->id;
        return $this->addPhoto($request,$id);
    }

    /**
     * Deleting Some Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteOnePhoto(Request $request): JsonResponse
    {
        $photo = AttractionPhoto::where('id','=',$request->id)->first();

        if(!isset($photo)){
            return $this->error('Photo not found');
        }

        // checking if this photo belongs to the desired attraction
        if($request->user()->attraction_id != $photo['attraction_id']){
            return $this->error('Unauthorized to delete this photo');
        }

        return $this->deletePHoto($request->id);
    }


    public function getLatestReservations(Request $request): JsonResponse
    {
        $id = $request->user()->attraction_id;

        $reservations = AttractionReservation::where('attraction_id','=',$id)
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




    protected function attractionDetails($id)
    {
        $attraction  = Attraction::where('id','=',$id)
            ->with([
                'photos',
                'type',
                'city',
                'city.country',
                'reviews'
            ])
            ->first();

        return $this->success($attraction);
    }
    protected function editDetails($request,$id)
    {
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
    public function addPhoto($request,$id)
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
    public function deletePhoto($id){
        AttractionPhoto::where('id','=',$id)->delete();
        return $this->success(null,'Photo deleted successfully');
    }
}
