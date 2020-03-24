<?php

namespace App\Http\Controllers;

use App\Http\Models\Orders;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    const CREDENTIALS = "Access denied.";
    const MISSING_RULE = "Unauthorized Rule";
    const MISSING_DATA = "Unauthorized Data";

    public function __construct(Request $request){
        try {
            $this->app_key = env('APP_KEY');
            $this->token = $request->headers->get('TOKEN');
            $this->params = $request->toArray();

//            Manage initial requirements
            if ($this->app_key !== $this->token)
                exit($this->generalResponse(self::CREDENTIALS));

            if (empty($this->params))
                exit($this->generalResponse(self::MISSING_DATA));

        }catch (\Exception $exception){
            exit($this->generalResponse($exception->getMessage(),$exception->getCode()));
        }
    }

    public function store(){
        try {
            $errorArr = [];
            $result['id'] = Orders::insertGetId($this->params);
            $result['issuers'] = [];

            return $this->generalResponse(array_merge($result,$errorArr));
        }catch (\Exception $exception){

            exit($this->generalResponse($exception->getMessage()));
        }
    }

    public function orders(){
        dd(__METHOD__);
    }
}
