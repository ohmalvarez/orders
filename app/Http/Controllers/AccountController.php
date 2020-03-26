<?php

namespace App\Http\Controllers;

use App\Http\Models\Accounts;
use App\Http\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    const CREDENTIALS = "ACCESS_DENIED";
    const MISSING_RULE = "UNAUTHORIZED_RULE";
    const MISSING_DATA = "UNAUTHORIZED_DATA";

    public function __construct(Request $request){
        try {
            $this->app_key = env('APP_KEY');
            $this->token = $request->headers->get('TOKEN');
            $this->params = $request->toArray();
            $this->date = Carbon::now();
            $this->accountsObj = new Accounts();

//            Validate initial requirements
            if ($this->app_key !== $this->token)
                exit($this->generalResponse(self::CREDENTIALS));

            if (empty($this->params))
                exit($this->generalResponse(self::MISSING_DATA));

//            Validation Rules. At this moment only there are two validation: day of the week and hour of day.
            $validationRules = $this->accountsObj->validateRules(["now" => $this->date->toTimeString(), "dayWeek" => $this->date->format('l')]);
            if ($validationRules !== [])
                exit($this->generalResponse(array_merge([self::MISSING_RULE],$validationRules)));

        }catch (\Exception $exception){
            exit($this->generalResponse($exception->getMessage(),$exception->getCode()));
        }
    }

    public function store(){
        try {
            $result = [];
            $arrError = [];
            $validationCash = $this->accountsObj->validateCash($this->params);

            if ($validationCash->fails()) {
                $arrError["business_errors"] = $validationCash->messages()->all();
                $result = $this->params;
            } else {
                $result['id'] = Orders::insertGetId($this->params);
                $result['cash'] = $this->params['cash'];
                $result['issuers'] = [];
            }

            return $this->generalResponse(array_merge($result,$arrError));
        }catch (\Exception $exception){
            exit($this->generalResponse($exception->getMessage()));
        }
    }

    public function orders($id){
        try{
            $arrError = [];
            $result = [];

//            Validate integrity of data
            $validationData = $this->accountsObj->validateData($this->params);

            if ($validationData->fails())
                $arrError["business_errors"] = $validationData->messages()->all();

//            Exists id
            if (!Orders::find($id))
                $arrError["business_errors"][] = "Order Id $id do not exists.";
            else{
//            Validate transaction
                $validationTransaction = $this->accountsObj->validateTransaction($id,$this->params);
            }

            return $this->generalResponse(array_merge($result,$arrError));
        }catch (\Exception $exception){
            exit($this->generalResponse($exception->getMessage()));
        }
    }

}
