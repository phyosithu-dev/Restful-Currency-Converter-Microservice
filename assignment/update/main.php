<?php
//import the update_rates file to update all the data from rate.xml
require "update_rates.php";
//import the put_currency function to make post request
require "put_currency.php";
//import the del_currency function to make delete request
require "del_currency.php";
//import the post_currency file to update all the data from rate.xml
require "post_currency.php";
//import error php file to show the errors
require "error.php";
//import the iso file
require "../../assignment/iso_codes.php";

//update all the currencies
//update_currency();

//get the ISO code
$codes = curr_name();
//check the errors
if(empty($_POST['action'])){
    error(2000, "Action not recognized or is missing");
}
else if(!in_array($_POST["action"], ['post', 'put', 'del'])){
    error(2000, "Action not recognized or is missing");
}
else if(empty($_POST['currency_iso'])){
    error(2100,"Currency code in wrong format or is missing" );

}
else if(strlen($_POST['currency_iso']) != 3 || preg_match('/\d/', $_POST["currency_iso"])){
    error(2100,"Currency code in wrong format or is missing" );
}
else if($_POST['action'] == "post"){
    //use post_currency function
    post_currency();
}
else if(!in_array(strtoupper($_POST["currency_iso"]), array_keys($codes))){
    error(2200, "Currency code not found for update");
}
else if($_POST['action'] == "put"){
    //use put_currency function
    put_currency();
}
else if($_POST['action'] == "del"){
    //use del_currency function
    del_currency();
}