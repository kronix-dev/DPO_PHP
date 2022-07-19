<?php 
class dpo_payment extends auth{
    private $ctoken='CBA13EE8-D08E-4F96-8C0D-9DF54D8115D1';
    private $redir = "http://localhost/dpo_api/verify.php";
    // private 
    function xmlRequest($xml){
                //The XML string that you want to send.
        

        //The URL that you want to send your XML to.
        $url = 'http://secure.sandbox.directpay.online/API/v5/';

        //Initiate cURL
        $curl = curl_init($url);

        //Set the Content-Type to text/xml.
        curl_setopt ($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

        //Set CURLOPT_POST to true to send a POST request.
        curl_setopt($curl, CURLOPT_POST, true);

        //Attach the XML string to the body of our request.
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

        //Tell cURL that we want the response to be returned as
        //a string instead of being dumped to the output.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //Execute the POST request and send our XML.
        $result = curl_exec($curl);

        //Do some basic error checking.
        if(curl_errno($curl)){
            throw new Exception(curl_error($curl));
        }

        //Close the cURL handle.
        curl_close($curl);

        //Print out the response output.
        
//         echo $result."<br/>";
        $xml = simplexml_load_string($result);

        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array;
    }
    function verifyToken($d){
        $xml='<?xml version="1.0" encoding="utf-8"?>
        <API3G>
          <CompanyToken>'.$this->ctoken.'</CompanyToken>
          <Request>verifyToken</Request>
          <VerifyTransaction>0</VerifyTransaction>
          <TransactionToken>'.$d.'</TransactionToken>
        </API3G>';
        $re=$this->xmlRequest($xml);
        if($re["Result"]=='000' || $re["Result"]=='001'){
            // Iko Poa
        }
    }
    function verifyAll(){
        $d=$this->select_tbl("votes","*");
        foreach($d as $a){
            $this->verifyToken($a["token"]);
        }
    }
    function createToken($d){
        $uid=substr(str_shuffle(uniqid().$this->randomizer()),0,10);
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
        <CompanyToken>'.$this->ctoken.'</CompanyToken>
        <Request>createToken</Request>
        <Transaction>
        <PaymentAmount>500</PaymentAmount>
        <PaymentCurrency>tzs</PaymentCurrency>
        <CompanyRef>'.$uid.'</CompanyRef>
        <RedirectURL>http://kismatymedia.com/participants/verifyPayment.php</RedirectURL>
        <CompanyRefUnique>0</CompanyRefUnique>
        <PTL>5</PTL>
        </Transaction>
        <Services>
          <Service>
            <ServiceType>47029</ServiceType>
            <ServiceDescription>'.$d.'</ServiceDescription>
            <ServiceDate>'.date('Y/m/d H:i').'</ServiceDate>
          </Service>
        </Services>
        </API3G>';
        $x=$this->xmlRequest($xml);
        
        $this->ins_to_db("votes",["token","cid","vid"],[$x["TransToken"],$d["id"],$x["TransRef"]]);
        // header('location:);
            $response =[];
            $response["transactionToken"] = $x["TransToken"];
            $response["transaction"] = $x;
            return $response;
    }
}
