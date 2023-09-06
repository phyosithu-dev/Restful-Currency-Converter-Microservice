<?php

function get_rates($from, $to, $amnt){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/convert?to=$to&from=$from&amount=$amnt",
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
        return json_decode($response, true);
    }
    function generate_xml($from, $to, $amnt, $codes, $location){
        #if the format value is xml the output will be xml format
        # use PHP's XML write library and build the document in memory
        $data = get_rates($from, $to, $amnt);
        if(is_null($data)){
            $code = 1500;
            $message = "Error in service";
            errors($code, $message);
        }
        else {
            $writer = new XMLWriter();
            $writer->openMemory();
            $writer->startDocument("1.0", "UTF-8");
            $writer->setIndent(true);

            $writer->startElement("conv");
            $writer->startElement("at");
            $writer->text(date('Y-m-d H:i:s', $data['info']['timestamp']));
            $writer->endElement();

            $writer->startElement("rate");
            $writer->text($data['info']['rate']);
            $writer->endElement();

            $writer->startElement("from");
            $writer->startElement("code");
            $writer->text($from);
            $writer->endElement();

            $writer->startElement("curr");
            $writer->text($codes[$from]);
            $writer->endElement();

            $writer->startElement("loc");
            $writer->text($location[$from]);
            $writer->endElement();

            $writer->startElement("amnt");
            $writer->text(($amnt));
            $writer->endElement();
            $writer->endElement();

            $writer->startElement("to");
            $writer->startElement("code");
            $writer->text($to);
            $writer->endElement();

            $writer->startElement("curr");
            $writer->text($codes[$to]);
            $writer->endElement();

            $writer->startElement("loc");
            $writer->text($location[$to]);
            $writer->endElement();

            $writer->startElement("amnt");
            $writer->text($data['result']);
            $writer->endElement();
            $writer->endElement();

            $writer->endElement();
            $writer->endDocument();
            # write out and save the file
            file_put_contents('currency.xml', $writer->outputMemory());

            #load the xml file
            $xml = simplexml_load_file('currency.xml');

            header('Content-type: text/xml');
            echo $xml->asXML();
        }

    }