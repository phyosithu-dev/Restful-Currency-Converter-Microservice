<?php
function errors($code, $message){

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $conv = $dom->createElement('conv');
    $error = $dom->createElement('error');
    $code = $dom->createElement('code', $code);
    $msg = $dom->createElement('msg', $message);

    $error->appendChild($code);
    $error->appendChild($msg);
    $conv->appendChild($error);
    $dom->appendChild($conv);

    $dom->save('error.xml');
//    libxml_use_internal_errors();
//    libxml_get_errors();
    #load xml file
    $xml = simplexml_load_file('error.xml');

    if(empty($_REQUEST['format']) || $_REQUEST['format'] == 'xml'){
        //respond with default xml format
        header('Content-type: text/xml');
        echo $xml -> asXML();
    }
    //condition to check if the format is in json
    else if($_REQUEST['format'] == 'json'){
        //respond with json format
        header('Content-type: text/javascript');
        $json = json_encode($xml, JSON_PRETTY_PRINT);
        echo $json;
    }
    else{
        //respond with default xml format
        header('Content-type: text/xml');
        echo $xml -> asXML();
    }

}
