<?php
    //import the iso code file to get currency code and name
    require 'iso_codes.php';
    //import the errors file to process the errors with format
    require 'errors.php';
    //import currency file to accept important functions
    require 'currency.php';

    #creating is_decimal function to check the amnt value is decimal or not
    function is_decimal($val){
        if(is_numeric($val)){
            return true;
        }
        return false;
    }

    #ensure the count of parameters match the requirement
    if (count(array_intersect(['from', 'to','amnt'], array_keys($_GET))) < 3){
        $code = 1000;
        $message = 'Required Parameter is missing';
        errors($code, $message);
    }
    #condition to check if there are more than four parameters
    else if(count($_GET) > 4 ){
        $code = 1100;
        $msg = "Parameter not recognized";
        errors($code,$msg);


    }
    else{

        #declare $currency variable to check the currency is valid
        $currencyFrom = false;
        $currencyTo = false;

        //get the currency codes and their name
        $codes = curr_name();
        //get the location for the currency
        $location = location();
        #checking the currency is valid and recognized
        foreach ($codes as $key => $value) {
            if ($_REQUEST['from'] == $key) {
                $currencyFrom = true;
                break;
            }
        }

        foreach ($codes as $key => $value) {
            if ($_REQUEST['to'] == $key) {
                $currencyTo = true;
                break;
            }
        }

        if(!$currencyFrom || !$currencyTo){
            $code = 1200;
            $message = "Currency type not recognized";
            errors($code, $message);
        }


        #condition to check if amount is decimal number
        else if(!is_decimal($_REQUEST['amnt'])){
            $code = 1300;
            $msg = "Currency amount must be a decimal number";
            errors($code,$msg);

        }
        else if(empty($_GET['format'])){
            //change the amnt value to decimal
            $b = floatval(($_GET['amnt']));
            if(strlen(($_GET['amnt'])) == 1){
                $formattedAmnt = number_format($b, 2);
            }
            else{
                $formattedAmnt = $b;
            }
//
            generate_xml($_REQUEST['from'], $_REQUEST['to'], $formattedAmnt, $codes, $location);

        }
        #assgin the url parameter as values to variables
        else if($_GET["format"] == "json"){
            //change the amnt value to decimal
            $b = floatval(($_GET['amnt']));
            if(strlen(($_GET['amnt'])) == 1){
                $formattedAmnt = number_format($b, 2);
            }
            else{
                $formattedAmnt = $b;
            }
            $data = get_rates($_REQUEST['from'], $_REQUEST['to'], $formattedAmnt);
            $jsonFormat = [
                'conv' => [
                    "at" => date('Y-m-d H:i:s', $data['info']['timestamp']),
                    "rate" => $data['info']['rate'],
                    "from" => [
                        "code" => $_REQUEST['from'],
                        "curr" => $codes[$_REQUEST['from']],
                        "loc"  => $location[$_REQUEST['from']],
                        "amnt" => $formattedAmnt,
                    ],
                    "to" => [
                        "code" => $_REQUEST['to'],
                        "curr" => $codes[$_REQUEST['to']],
                        "loc"  => $location[$_REQUEST['to']],
                        "amnt" => $data['result'],

                    ],
                ],
            ];
            //respond with json format
            header('Content-type: text/javascript');
            $customJson = json_encode($jsonFormat, JSON_PRETTY_PRINT);
            print_r($customJson);
        }
        else if($_GET['format'] == "xml"){
            $b = floatval(($_GET['amnt']));
            if(strlen(($_GET['amnt'])) == 1){
                $formattedAmnt = number_format($b, 2);
            }
            else{
                $formattedAmnt = $b;
            }
            
            generate_xml($_REQUEST['from'], $_REQUEST['to'], $formattedAmnt, $codes, $location);
        }
        else if($_GET["format"] != "xml" || $_GET["format"] != "json" ){
            $code = 1400;
            $message = "Format must be xml or json";
            errors($code, $message);

        }

    }




