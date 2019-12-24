<?php
class ScheduleClass
{
    public function __construct($secretAccessKey,$access_key,$webServiceUrl)
    {
        require_once("authbase.php");
        $authBase = new wiziq_authbase($secretAccessKey,$access_key);
        $method = "create";
        $requestParameters["signature"]=$authBase->wiziq_generatesignature($method,$requestParameters);
        #for teacher account pass parameter 'presenter_email'
                //This is the unique email of the presenter that will identify the presenter in WizIQ. Make sure to add
                //this presenter email to your organization's teacher account.For more information visit at: (http://developer.wiziq.com/faqs)
        $requestParameters["presenter_email"]="john@example.com";
        #for room based account pass parameters 'presenter_id', 'presenter_name'
        //$requestParameters["presenter_id"] = "40";
        //$requestParameters["presenter_name"] = "sevketpolan";
        $requestParameters["start_time"] = "12/08/2019 16:00";
        $requestParameters["title"]="my php-class 2"; //Required
        $requestParameters["duration"]="30"; //optional
        $requestParameters["time_zone"]="Europe/Istanbul"; //optional
        $requestParameters["attendee_limit"]=""; //optional
        $requestParameters["control_category_id"]=""; //optional
        $requestParameters["create_recording"]=""; //optional
        $requestParameters["return_url"]=""; //optional
        $requestParameters["status_ping_url"]=""; //optional
                $requestParameters["language_culture_name"]="tr-TR";
        $httpRequest=new wiziq_httprequest();
        try
        {
            $XMLReturn=$httpRequest->wiziq_do_post_request($webServiceUrl.'?method=create',http_build_query($requestParameters, '', '&amp;'));
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        if(!empty($XMLReturn))
        {
            try
            {
              $objDOM = new DOMDocument();
              $objDOM->loadXML($XMLReturn);
 
            }
            catch(Exception $e)
            {
              echo $e->getMessage();
            }
        $status=$objDOM->getElementsByTagName("rsp")->item(0);
        $attribNode = $status->getAttribute("status");
        if($attribNode=="ok")
        {
            $methodTag=$objDOM->getElementsByTagName("method");
            echo "method=".$method=$methodTag->item(0)->nodeValue;
            $class_idTag=$objDOM->getElementsByTagName("class_id");
            echo "<br>Class ID=".$class_id=$class_idTag->item(0)->nodeValue;
            $recording_urlTag=$objDOM->getElementsByTagName("recording_url");
            echo "<br>recording_url=".$recording_url=$recording_urlTag->item(0)->nodeValue;
            $presenter_emailTag=$objDOM->getElementsByTagName("presenter_email");
            echo "<br>presenter_email=".$presenter_email=$presenter_emailTag->item(0)->nodeValue;
            $presenter_urlTag=$objDOM->getElementsByTagName("presenter_url");
            echo "<br>presenter_url=".$presenter_url=$presenter_urlTag->item(0)->nodeValue;
        }
        else if($attribNode=="fail")
        {
            $error=$objDOM->getElementsByTagName("error")->item(0);
            echo "<br>errorcode=".$errorcode = $error->getAttribute("code");
            echo "<br>errormsg=".$errormsg = $error->getAttribute("msg");
        }
     }//end if
   }//end function
 
}
?>
