<?php

namespace App\Http\Models;

use Carbon\Carbon;
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

        $messages = [ 'operation.in' => "The string operation param is not recognoized." ];

        return Validator::make($params,$rules,$messages);
    }

    public function validateRules($params = []){
        try{
            $result = [];

            if ( isset($params["now"]) && ($params["now"] < "06:00:00" || $params["now"] > "15:00:00"))
                $result[] = "CLOSE_MARKET";
            if ( isset($params["dayWeek"]) && ($params["dayWeek"] === "Saturday" || $params["dayWeek"] === "Sunday") )
                $result[] = "CLOSE_MARKET";
//            validations to buy/sell
            if (isset($params["operation"])) {
                if ( $params["operation"] == "BUY" && $params["bigTotal"] > $params["newCash"] )
                    $result[] = "Amount to operate out of range.";
                if ( $params["operation"] == "SELL" && $params["total_shares"] > $params["share_operations"] )
                    $result[] = "Not enough stocks to operate";
                if ( $params["last_op_time"] != null && $params["last_op_time"]->diffInMinutes( Carbon::now()->toTimeString() ) < 5)
                    $result[] = "Duplicate operation, try in 5 minutes.";
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
            $operations = $this->totalOperationsByTypeId(["id_order" => $id, "type" => $params["operation"]]);
            $lastOperation = $this->lastOperationCreatedById($id);
            $lastOperation = ($lastOperation != null) ? $lastOperation->toArray() : null;

            $new_cash = ($params["operation"] == "BUY") ? ($order["cash"] - $operations["totals"]) : "";

            $result["errors"] = $this->validateRules([
                "newCash" =>  $new_cash,
                "bigTotal" => $bigTotal,
                "total_shares" => $params["total_shares"],
                "share_operations" => $operations["shares"],
                "last_op_time" => isset($lastOperation["created_at"]) ? Carbon::createFromTimeString($lastOperation["created_at"]) : null,
                "operation" => $params["operation"] ]);

            $result["new_cash"] = ($params["operation"] == "BUY") ? ($new_cash - $bigTotal) : "";
            
            return $result;
        }catch (\Exception $exception){
            return$exception->getMessage();
        }
    }

    public function timestampToDatetimeStr($timestamp){
        return Carbon::createFromTimestamp($timestamp)->toDateTimeString();
    }

    public function lastOperationCreatedById($id){
        return Operation::byorderid($id)->orderBy('created_at', 'desc')->first();
    }

    public function totalOperationsByTypeId($params){
        $shares = $totals = 0;
        $rslt = Operation::byorderid($params["id_order"])->bytype($params["type"])->get()->toArray();

        foreach ($rslt as $item){
            $shares += $item["shares"];
            $totals += ($item["shares"] * $item["price"]);
        }

        return ["shares" => $shares, "totals" => $totals];
    }

}
