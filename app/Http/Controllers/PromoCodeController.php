<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PromoCodeService;
use App\Http\Requests\PromoCodeRequest;
use App\Http\Requests\UpdatePromoCodeRadiusRequest;
use App\Http\Requests\PromoRequest;

class PromoCodeController extends Controller
{
    protected $service;

    public function __construct(PromoCodeService $service)
    {
        $this->service = $service;
    }

    public function allCodes(Request $request)
    {
        if($request->exists('active')) {
            $activeCodes  = $this->service->getActiveCodes();
            return response(['status' => 'success', 'data' => $activeCodes], 200);
        }

        $allCodes = $this->service->getAllCodes();
        return response(['status' => 'success', 'data' => $allCodes], 200);
    }

    public function deactivate($id)
    {
        if(!$id) {
            return response(['status' => 'error', 'message' => 'Bad request'], 400);
        }

        $deactivatedCode = $this->service->deactivateCode($id);

        return !$deactivatedCode ? response(['status' => 'error', 'message' => 'Not found'], 404) 
            : response(['status' => 'success', 'message' => 'Promo Code deactivated'], 200);
    }

    public function create(PromoCodeRequest $request)
    {
        $promoCode = $this->service->createCode($request);
        return response(['status' => 'success', 'message' => 'Promo Code created', 'data' => $promoCode], 201);
    }

    public function changeRadius(UpdatePromoCodeRadiusRequest $request, $id)
    {
        if(!$id) {
            return response(['status' => 'error', 'message' => 'Bad request'], 400);
        } 

        $updateRadius = $this->service->updateRadius($request, $id);

        return !$updateRadius ? response(['status' => 'error', 'message' => 'Not found'], 404)
            : response(['status' => 'success', 'message' => 'Promo Code radius updated'], 200);
    }

    public function promo(PromoRequest $request)
    {
        $promoCode = $this->service->usePromoCode($request);
        return $promoCode['status'] == 'error' ? response($promoCode, 403)
            : response($promoCode, 200);
    }
}
