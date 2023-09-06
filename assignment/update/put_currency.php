<?php

//create a put_currency function
function put_currency(){
    //load the rates.xml file
    $xml = simplexml_load_file("rates.xml");
    $curl = curl_init();
//
//declare $cur variable
    $cur = strtoupper($_POST['currency_iso']);
//    //get the update value of a user selected currency
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.apilayer.com/fixer/latest?symbols=$cur&base=GBP",
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
//declare the action variable
    $targetAction = null;
    curl_close($curl);
//decode the json response
    $data = json_decode($response, true);

    foreach ($xml->action as $actionElement) {
        $currencyCode = (string) $actionElement->code;
        if ($currencyCode === $cur) {
            $targetAction = $actionElement;
            break;
        }
    }

    foreach ($xml->action as $actionElement) {
        $currencyCode = (string) $actionElement->curr->code;
        if ($currencyCode === $cur) {
            $targetAction = $actionElement;
            break;
        }
    }
// Change the <rate> tag to <old_tag> and create a new <rate> tag with the updated value
    if ($targetAction !== null) {
        if($targetAction->curr->code == "GBP"){
            error(2400,"Cannot update base currency");
        }
        else if($targetAction['type'] == 'del'){
            error(2900,"Already Deleted, currency unavaialble to the service ");
        }
        else if($targetAction->rate == "unavailable"){
            error(2300,"No rate listed for this currency");
        }
        else{
            $targetAction['type'] = 'put';
            $targetAction->at = date('Y-m-d H:i:s', $data['timestamp']);
            if(isset($targetAction->old_rate)){
                //delete the original old_tag
                unset($targetAction->old_rate);
            }
            $targetAction->addChild('old_rate', (float) $targetAction->rate);
            //delete the original rate
            unset($targetAction->rate);
            $targetAction->addChild('rate', $data['rates'][$cur]);
            // Save the modified XML back to the original file
            $xml->asXML("rates.xml");
            //respond with default xml format
            header('Content-type: text/xml');
            echo $targetAction -> asXML();
        }
    }
}