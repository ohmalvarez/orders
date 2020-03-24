<?php

namespace App\Http\Controllers;

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

            if ($this->app_key !== $this->token)
                exit($this->generalResponse(self::CREDENTIALS));

        }catch (\Exception $exception){
            $this->generalResponse($exception->getMessage(),$exception->getCode());
        }
    }

    public function store(){
        dd($this->params);
    }

    public function orders(){
        dd(__METHOD__);
    }
}
