<?php
//create del_currency function to make delete request
function del_currency(){
    //load the rates.xml file
    $xml = simplexml_load_file("rates.xml");
//declare $cur variable
    $cur = strtoupper($_POST['currency_iso']);

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

    if($targetAction != null){
        if($targetAction->curr->code == "GBP"){
            error(2400, "Cannot delete base currency");
        }
        else if($targetAction['type'] == 'del'){
            error(2900, "currency unavaialble to the service ");
        }
        else{
            $targetAction['type'] = 'del';
            //delete rate tag
            unset($targetAction->rate);
            //delete curr tag
            unset($targetAction->curr);
            //add the currency code
            if(isset($targetAction->old_rate)){
                //delete the old_rate tag
                unset($targetAction->old_rate);
            }
            $targetAction->addChild('code', $cur);

            // Save the modified XML back to the original file
            $xml->asXML("rates.xml");
            //respond with default xml format
            header('Content-type: text/xml');
            echo $targetAction -> asXML();
        }


    }
}

