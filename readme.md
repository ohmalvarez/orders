<p align="center"><img src="https://www.gbm.com.mx/Content/Images/logo.png"></p>

## About this challenge

A brokerage firm needs to have an API service to process a set of buy/sell orders.

This API must have two endpoints, the first one to create an account with the initial balance, and the second one to send the
buy/sell orders.

For each order, the API expect to receive a timestamp (for when it took place), an operation type (buy or sell), issuer name
(stock’s identifier), a total amount of shares (a positive integer number), and the unitary price of the share (a positive
real number).

Please make sure that all the following business rules are validated:

- Insufficient Balance: When buying stocks, you must have enough cash in order to fulfill it.
- Insufficient Stocks: When selling stocks, you must have enough stocks in order to fulfill it.
- Duplicated Operation: No operations for the same stock at the same amount must happen within a 5 minutes interval, as
they are considered duplicates.
- Closed Market: All operations must happen between 6am and 3pm.
- Invalid Operation: Any other invalidity must be prevented.

A business rule violation is not consired an error, since in the real world they may happen. In case any happens, you must list
them in the output as an array, and have no changes applied for that order, following to process the next order.

## Technologies

- PHP version >= 5.6.4 
- Laravel version 5.4
- Mysql version ...

## Configuration Project

The following instruction will help you through the set up of this project:

- Navigate to folder where web developments are allocated.
- On the command line, run **git clone https://github.com/ohmalvarez/orders.git**
- Create Data Table ...
- On the root path, copy and paste the **.env.example** with the name .env
- Edit **.env file** to set DB host, DB port, DB name, DB user and DB password.
- On the command line, run **php artisan generate:key** to add **APP_KEY** on the .env file which will be used as a **header param** of the api request. 

## Api Requests

Both request needs a header param **'TOKEN'** with the value of the APP_KEY param equals to that value, otherwise cannot be possible to reach the apis.

Also, both apis are POST method.


