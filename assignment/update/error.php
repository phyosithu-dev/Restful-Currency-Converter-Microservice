<?php
function error($code, $message){
    // Create a SimpleXMLElement object
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><action type="tttt"><error></error></action>');

// Add code and message elements with variable values
    $xml->error->addChild('code', $code);
    $xml->error->addChild('msg', $message);

// Set the content type as XML
    header('Content-type: text/xml');

// Output the XML
    echo $xml->asXML();
}
