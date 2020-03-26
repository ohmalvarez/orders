<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Accounts extends Model
{
    //
    public function validateCash($params = []){
        $rules['cash'] = 'required|numeric|min:1000|max:1000000000';
        return Validator::make($params,$rules);
    }

    public function validateData($params= []){
        $rules = [
            'timestamp' =>      'required',
            'operation' =>      'required|string|in:BUY,SELL',
            'issuer_name' =>    'required|string|min:4',
            'total_shares' =>   'required|numeric|min:1|max:50',
            'share_price' =>    'required|numeric|min:1.00|max:200000'
        ];

        $messages = [
            'operation.in' => 'String not recognoized.',
        ];

        return Validator::make($params,$rules,$messages);
    }

    public function validateRules($params = []){
        try{
            $result = [];

//            if ( isset($params["now"]) && ($params["now"] < "06:00:00" || $params["now"] > "15:00:00"))
//                $result[] = "CLOSE_MARKET";
            if ( isset($params["dayWeek"]) && ($params["dayWeek"] === "Saturday" || $params["dayWeek"] === "Sunday") )
                $result[] = "CLOSE_MARKET";
//            validations to buy/sell
            if (isset($params["operation"])) {
                if ($params["operation"] == "BUY" && ($params["bigTotal"] > $params["newCash"]) )
                    $result[] = "Amount to " . $params["operation"] . " out of range.";
                if ($params["operation"] == "SELL" && ($params["total_shares"] > $params["share_operations"]) )
                    $result[] = "Not enough stocks to ".$params["operation"];
//            availableOperationTime
            }

            return $result;
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function validateTransaction($id, $params = []){
        try{
            $result = [];
            $bigTotal = $params["total_shares"] * $params["share_price"];// Total amount to buy or sell

            $order = Orders::find($id)->toArray();
            $operations = ["shares" => 0, "totals" => 0];//$operationObj->totalCashOrder(["id_order" => $id, "type" => $params["operation"]]);
//            $lastOperation = // Last operation to check the diff of time

            $new_cash = $order["cash"] - $operations["totals"];

            $result = $this->validateRules([
                "newCash" => $new_cash,
                "bigTotal" => $bigTotal,
                "total_shares" => $params["total_shares"],
                "share_operations" => $operations["shares"],
                "operation" => $params["operation"] ]);
            
            return $result;
        }catch (\Exception $exception){
            return$exception->getMessage();
        }
    }
}
