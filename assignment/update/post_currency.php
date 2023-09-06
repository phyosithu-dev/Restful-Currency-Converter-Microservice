<?php

//create a post_currency() function
function post_currency(){
    //get the real_time
    $currentDateTime = date("Y-m-d H:i:s");
    //
    //get the currency_code
    $code = strtoupper($_POST['currency_iso']);
    //get the currency_name
    $name = $_POST['currency'];
    //get the currency_rate
    $rate = $_POST['rate'];
    //get the currency_location
    $location = $_POST['location'];


    //load the rates.xml file
    $xml = simplexml_load_file("rates.xml");

    // append the child action node to the parent node
    $newNode = $xml -> addChild('action');
    $newNode->addAttribute('type', 'post');
    // Add child elements
    $newNode->addChild('at', $currentDateTime);
    $newNode->addChild('rate', $rate);

// Create the "curr" element and its child elements
    $curr = $newNode->addChild('curr');
    $curr->addChild('code', $code);
    $curr->addChild('name', $name);
    $curr->addChild('loc', $location);

// Convert the XML object to a string
    $xml->asXML("rates.xml");

//respond with default xml format
    header('Content-type: text/xml');
// Output the XML
    echo $newNode->asXML();

}
