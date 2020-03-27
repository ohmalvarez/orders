<?php

namespace App\Http\Controllers;

use App\Http\Models\Accounts;
use App\Http\Models\Operation;
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
            $this->date = Carbon::now(); // Takes the server time
            $this->accountsObj = new Accounts();

//            Validate initial requirements
            if ($this->app_key !== $this->token)
                exit($this->generalResponse(self::CREDENTIALS));

            if (empty($this->params))
                exit($this->generalResponse(self::MISSING_DATA));

//            Validation Rules. At this moment only there are two validation: day of the week and hour of day.
            $validationRules = $this->accountsObj->validateRules(["now" => $this->date->toTimeString(), "dayWeek" => $this->date->format('l')]);
            if ($validationRules !== [])
                exit($this->generalResponse(array_push($validationRules,self::MISSING_RULE)));

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
            $dataErrors = [];
            $transacition = [];

//            Exists id
            if (!Orders::find($id))
                $arrError["business_errors"][] = "Order Id $id do not exists.";
            else{
//              Validate integrity of data
                $validationData = $this->accountsObj->validateData($this->params);
//              Validate transaction
                $transacition = $this->accountsObj->validateTransaction($id,$this->params);

                if ($validationData->fails())
                $dataErrors = $validationData->messages()->all();
                $arrError["business_errors"] = array_merge($dataErrors, $transacition["errors"]);

                if ($arrError["business_errors"] == null){
                    $timestampTostr = $this->accountsObj->timestampToDatetimeStr($this->params["timestamp"]);

                    Operation::insert([
                        "id_order" => $id,
                        "issuer_name" => $this->params["issuer_name"],
                        "type" => $this->params["operation"],
                        "shares" => $this->params["total_shares"],
                        "price" => $this->params["share_price"],
                        "created_at" => $timestampTostr
                    ]);

//                    Create array response current_balance
                    $result["current_balance"]["issuers"] = Operation::select("issuer_name","shares as total_shares","price as share_price")->byorderid($id)->bytype($this->params["operation"])->get()->toArray();
                    $result["current_balance"]["cash"] = $transacition["new_cash"];
                }

            }

            return $this->generalResponse(array_merge($result,$arrError));
        }catch (\Exception $exception){
            exit($this->generalResponse($exception->getMessage()));
        }
    }

}
