<?php

#get the iso codes for the currency
define ('ISO_XML', 'https://www.six-group.com/dam/download/financial-information/data-center/iso-currrency/lists/list-one.xml', false);

function curr_name(){

    # set iso-xml variable to store the loaded data
    $iso_xml = simplexml_load_file(ISO_XML) or die("Error: Cannot load currencies file");

    # get all the currency codes
    $iso_codes = $iso_xml->xpath("//CcyNtry");

    #get all iso codes to the codes array
    $codes=[];
    foreach ($iso_codes as $code) {
        $ccy = (string) $code -> Ccy;
        $ccyNm = (string) $code -> CcyNm;
        $codes[$ccy] = $ccyNm;
    }

    #removing all the duplicate values from an array
    $codes = array_unique($codes);
    return $codes;

}

function location(){
    # set iso-xml variable to store the loaded data
    $iso_xml = simplexml_load_file(ISO_XML) or die("Error: Cannot load currencies file");

    # get all the currency codes
    $iso_codes = $iso_xml->xpath("//CcyNtry");

    # declare the location array
    $location = [];

    foreach ($iso_codes as $code) {
        $ccy = (string) $code -> Ccy;
        $ctryNm = (string) $code -> CtryNm;
        $location[$ccy] = $ctryNm;
    }
    #removing all the duplicate values from an array
    $location = array_unique($location);
    return $location;
}
