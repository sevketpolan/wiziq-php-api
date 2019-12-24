<?php
 
#This file is part of Wiziq - http://www.wiziq.com/
#Edited by Şevket Polan
 
set_time_limit(0);
 
class wiziq_authbase {
     
     # This function defines the secret access key and access key.
     # @param string $wiziq_secretaccesskey
     # @param string $wiziq_access_key
      
    public function __construct($wiziq_secretaccesskey, $wiziq_access_key) {
        $this->wiziq_secretaccesskey=$wiziq_secretaccesskey;
        $this->wiziq_access_key=$wiziq_access_key;
    }
    /**
     * This function generates the timestamp.
     *
     * @return integer time.
     */
    public function wiziq_generatetimestamp() {
        return time();
    }
    /**
     * This function generates the signature in order to send a http request.
     *
     * @param string $methodname name of the method.
     * @param string $requestparameters parameters that are required for http request.
     *
     * @return string
     */
    public function wiziq_generatesignature($methodname, &amp;$requestparameters) {
        $signaturebase="";
        $wiziq_secretaccesskey = urlencode($this->wiziq_secretaccesskey);
        $requestparameters["access_key"] = $this->wiziq_access_key;
        $requestparameters["timestamp"] =$this->wiziq_generatetimestamp();
        $requestparameters["method"] = $methodname;
        foreach ($requestparameters as $key => $value) {
            $signaturebaselenght = strlen($signaturebase);
            if ($signaturebaselenght > 0) {
                $signaturebase.="&amp;";
            }
            $signaturebase.="$key=$value";
        }
        return base64_encode($this->wiziq_hmacsha1($wiziq_secretaccesskey, $signaturebase));
    }
 
    /**
     * This function generates the hash based message authentication code(hmac)
     * using cryptographic hash function sha1.
     *
     * @param string $key cryptographic key.
     * @param string $data the data which will be appended.
     *
     * @return string $hmac
     */
    public function wiziq_hmacsha1($key, $data) {
        $blocksize=64;
        $hashfunc='sha1';
        $keylenght = strlen($key);
        if ($keylenght > $blocksize) {
            $key=pack('H*', $hashfunc($key));
        }
        $key=str_pad($key, $blocksize, chr(0x00));
        $ipad=str_repeat(chr(0x36), $blocksize);
        $opad=str_repeat(chr(0x5c), $blocksize);
        $hmac = pack(
            'H*', $hashfunc(
                ($key^$opad).pack(
                    'H*', $hashfunc(
                        ($key^$ipad).$data
                    )
                )
            )
        );
        return $hmac;
    }
}//end class wiziq_authbase
 
class wiziq_httprequest {
    /**
     * This function generates a http request using curl.
     *
     * @param string $url this is wiziq url to which request is to be send.
     * @param array $data which contains the request paramters.
     * @param string $optional_headers
     *
     * @return string $response.
     */
    public function wiziq_do_post_request($url, $data, $optional_headers = null) {
        try {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
 
      //execute post
        $result = curl_exec($ch);
      //close connection
        curl_close($ch);
            return $result;
        } catch (Exception $e) {
            $errorexecption = $e->getMessage();
            $errormsg = get_string('errorinservice', 'wiziq'). " " . $errorexecption;
            print_error($errormsg);
        }
    }
}//end class wiziq_httprequest
