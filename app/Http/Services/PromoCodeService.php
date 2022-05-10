<?php
namespace App\Http\Services;

use App\Models\PromoCode;
use App\Models\UsedPromoCodes;
use emcconville\Polyline\GoogleTrait;

class PromoCodeService 
{
    use GoogleTrait;

    protected $promoCodeModel;

    public function __construct(PromoCode $promoCodeModel)
    {
        $this->promoCodeModel = $promoCodeModel;
    }

    public function getAllCodes()
    {
        return $this->promoCodeModel->select('code_value', 'max_rides', 'status', 'expiry_date')->get(); 
    }

    public function getActiveCodes()
    {
        return $this->promoCodeModel->select('code_value', 'max_rides', 'status', 'expiry_date')
                    ->where('status', 1)
                    ->get(); 
    }

    public function deactivateCode($id)
    {
        $promoCode = $this->promoCodeModel->find($id);
        if(!$promoCode) {
            return false;
        }

        $promoCode->status = 0;
        return $promoCode->save();
    }

    public function createCode($requestData)
    {
        //when "safeboda" is sent as promo code, promo code auto generated.
        $promoCode = $this->promoCodeModel->create([
            'code_value' => $requestData->promo_code !== 'safeboda' ? $requestData->promo_code : $this->generatePromoCode(),
            'max_rides' => $requestData->max_rides,
            'radius' => $requestData->radius,
            'expiry_date' => $requestData->expiry_date,
            'event_id' => $requestData->event_id
        ]);

        return $promoCode;
    }

    public function updateRadius($requestData, $id)
    {
        $promoCode = $this->promoCodeModel->select('radius')->first();
        if(!$promoCode) {
            return false;
        }

        $promoCode->radius = $requestData->radius;
        return $promoCode->save();
    }

    public function usePromoCode($requestData)
    {
        $promoCode = $this->promoCodeModel->select('id', 'max_rides', 'expiry_date', 'radius', 'event_id')
                            ->with(['event' => function($query){
                                $query->select('id', 'event_lat', 'event_long');
                            }])
                            ->where('code_value', $requestData->promo_code)
                            ->where('status', 1)
                            ->first();
        
        if(!$promoCode) {
            return ['status' => 'error', 'message' => 'Promo code does not exist!'];
        }

        //Check if promo code has rides
        if($promoCode->max_rides == 0) {
            return ['status' => 'error', 'message' => 'Invalid Promo code!'];
        }

        //Check if promo code has not expired
        $today = date('Y-m-d');
        if($today > $promoCode->expiry_date) {
            return ['status' => 'error', 'message' => 'Promo code has expired!'];
        }

        //check if pickup is within promo radius
        $pickUpWithinRadius = $this->checkUserWithinRadius($requestData->pick_up_latitude, 
                                                            $requestData->pick_up_longitude, 
                                                            $promoCode->event->event_lat, 
                                                            $promoCode->event->event_long, 
                                                            $promoCode->radius); 
        //check if destination is within promo radius                                                    
        $destinationWithinRadius = $this->checkUserWithinRadius($requestData->destination_latitude, 
                                                                $requestData->destination_longitude, 
                                                                $promoCode->event->event_lat, 
                                                                $promoCode->event->event_long, 
                                                                $promoCode->radius);
                                                                
        if($pickUpWithinRadius === false && $destinationWithinRadius === false) {
            return ['status' => 'error', 'message' => 'Promo code not valid for your trip!'];
        }

        //encoded polyline from pick up and destination points
        $polyline = $this->createPolyline($requestData->pick_up_latitude, 
                                        $requestData->pick_up_longitude,  
                                        $requestData->destination_latitude, 
                                        $requestData->destination_longitude);

        $userPromoCode = $promoCode;
        $promoCodeID = $promoCode->id;

        $promoCode->max_rides--;
        $promoCode->save();

        $this->logUsedPromoCode($requestData, $promoCodeID); //log used promo code

        return ['status' => 'success', 'data' => $userPromoCode, 'polyline' => $polyline];


    }

    private function generatePromoCode()
    {
        $promoCode = $this->randomCode();
        while($this->promoCodeModel->where('code_value', $promoCode)->first()) {
            $promoCode = $this->randomCode();
        }

        return $promoCode;
    }

    private function randomCode()
    {
        $safeBoda = 'safeboda';
        $random = random_int(1000, 9999);
        return $safeBoda .'-'. $random;
    }

    private function checkUserWithinRadius($latitude, $longitude, $eventLatitude, $eventLongitude, $radius) {  
        $earthRadius = 6371; //in kilometers
      
        $latitudeDifference = deg2rad($eventLatitude - $latitude);  
        $longitudeDifference = deg2rad($eventLongitude - $longitude);  
      
        $result = sin($latitudeDifference/2) * sin($latitudeDifference/2) + cos(deg2rad($latitude)) 
                * cos(deg2rad($eventLatitude)) * sin($longitudeDifference/2) * sin($longitudeDifference/2);  
        $result = 2 * asin(sqrt($result));  
        $distance = $earthRadius * $result;
      
        return $distance < $radius ? true : false;  //$radius is in kilometers
    }

    private function logUsedPromoCode($requestData, $promoCodeID)
    {
        return UsedPromoCodes::create([
            'pick_up' => $requestData->pick_up,
            'pick_up_lat' => $requestData->pick_up_latitude,
            'pick_up_long' => $requestData->pick_up_longitude,
            'destination' => $requestData->destination,
            'destination_lat' => $requestData->destination_latitude,
            'destination_long' => $requestData->destination_longitude,
            'promo_code_id' => $promoCodeID
        ]);
    }

    private function createPolyline($pickUpLat, $pickUpLong, $destinationLat, $destinationLong)
    {
        $points = [
            [$pickUpLat, $pickUpLong],
            [$destinationLat, $destinationLong]
        ];
        
        return $this->encodePoints($points);
    }

}