<?php
////import the iso_codes file to get the currency names
//require "../iso_codes.php";
//create update_currency function
function update_currency(){
    //get the up-to-date currency and save to rate.xml file
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.apilayer.com/fixer/latest?symbols?base=GBP",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            "apikey: 7TYGGwWPPbvGGjCL3OBu7goFpgSDZuxN"
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));

    $response = curl_exec($curl);
    curl_close($curl);
//decode the response with json-decode function
    $data = json_decode($response, true);
    //if the service fail, this error will show
    if(is_null($data)){
        $code = 1500;
        $message = "Error in service";
        error($code, $message);
    }
    else{
        //print_r($data['rates']['USD']);
//make the file as rates.xml
        $file = 'rates.xml';
//get the ISO code
        $codes = curr_name();
//get the location
        $location = location();
//make the xml format
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');

        foreach ($codes as $key => $value) {
            $action = $xml->addChild('action');
            $action->addAttribute("type", "get");
            $at = $action->addChild('at', date('Y-m-d H:i:s', $data['timestamp']));
            if (isset($data['rates'][$key])) {
                $action->addChild('rate', $data['rates'][$key]);
            } else {
                $action->addChild('rate', "unavailable");
            }
            $curr = $action->addChild('curr');
            $curr->addChild('code', $key);
            $curr->addChild('name', $value);
            if(isset($location[$key])){
                $curr->addChild('loc', $location[$key]);
            }else{
                $curr->addChild('loc', "Location Not available");
            }

        }
        // Create a DOMDocument object
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

// Import the SimpleXMLElement into the DOMDocument
        $dom->loadXML($xml->asXML());
        file_put_contents($file, $dom->saveXML());
    }

}
