<p align="center"><img src="https://www.gbm.com.mx/Content/Images/logo.png"></p>

About this challenge
==

A brokerage firm needs to have an **API service** to process a set of buy/sell orders.

This API must have two endpoints, the first one _to create an account_ with the initial balance, and the second one to _send the buy/sell orders_.

For each order, the API expect to receive a **_timestamp_** (for when it took place), an operation **_type_** (buy or sell), **_issuer name_** (stock’s identifier), a total amount of **_shares_** (a positive integer number), and the unitary **_price_** of the share (a positive real number).

Please make sure that all the following business rules are validated:

- **Insufficient Balance:** When buying stocks, you must have enough cash in order to fulfill it.
- **Insufficient Stocks:** When selling stocks, you must have enough stocks in order to fulfill it.
- **Duplicated Operation:** No operations for the same stock at the same amount must happen within a 5 minutes interval, as they are considered duplicates.
- **Closed Market:** All operations must happen between 6am and 3pm.
- **Invalid Operation:** Any other invalidity must be prevented.

A business rule violation is not consired an error, since in the real world they may happen. In case any happens, you must list them in the output as an array, and have no changes applied for that order, following to process the next order.

## Technologies and Requirements

- PHP version >= 5.6.4 
- Laravel version 5.4
- Mysql version >= 5.6.43
- Composer

## Configuration Project

The following instruction will help you through the set up of this project:

- Navigate to folder where web developments are allocated.
- On the command line, run **git clone https://github.com/ohmalvarez/orders.git**
- Create Data Base Schema from the path: `orders/database/challenge.sql`
- On the root path, copy and paste the **.env.example** with the new name **.env**
- Edit **.env** file to set DB host, DB port, DB name, DB user and DB password.
- On the command line, run **php artisan generate:key** to add **APP_KEY** on the **.env** file (this value will be used as a **header param** of each api request). 

## Api Requests

Both requests needs a header param '**TOKEN**' with the value of the **APP_KEY** param equals to that value, otherwise cannot be possible to reach the apis. Also, both apis are **POST method**.

## Create Investment Account

Endpoint: `/accounts`

Body Params:

    "cash" : numeric

Expected response example:

    {
        "id": 2,
        "cash": "20000",
        "issuers": []
    }

## Send a buy/sell Order

Endpoint: `/accounts/{id}/orders`

Body Params:

    "timestamp" : numeric,
    "operation" : string("BUY","SELL"),
    "issuer_name" : string(long: 255),
    "total_shares" : integer,
    "share_price" : numeric

Expected response example:

        "current_balance": {
            "issuers": [
                {
                    "issuer_name": "ALVZ",
                    "total_shares": 10,
                    "share_price": "1000.00"
                },
                {
                    "issuer_name": "ARGN",
                    "total_shares": 4,
                    "share_price": "500.00"
                }
            ],
            "cash": 8000
        },
        "business_errors": []
    
---

For a better experience, you can check the Collection on `Postman` to see how apis are consumed. Just copy and paste this route on Postman _(https://www.getpostman.com/collections/6a1f0c3fa5509b0e28a6)_ or click on the next link to see documentation on a Postman page [link](https://documenter.getpostman.com/view/5837810/SzYW2zmd "Postman Collection").

##### Enjoy! 

<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>
