<?php
namespace Grisu\Util;

class MyCrypt
{

    /**
     * Crypt Key
     * @var string
     */
    var $ky = "lkirwf897+22#bbtrm8814z5qq=498j5";
    /**
     * Crypt salt
     * @var string
     */
    var $iv = "741952hheeyy66#cs!9hjv887mxx7@8y";

    /**
     * Placeholder form unithash
     * @var string
     */
    var $userunit = "";
    /**
     * placeholder for flag to old decryption
     * @var integer
     */
    var $oldencrypt = "";
    /**
     * Function to decrypt an String
     * @param string $string_to_decrypt
     */
    function DecryptData($string_to_decrypt){
        if($string_to_decrypt<=" "){
            return "";
        }
        $sub = substr($string_to_decrypt, 0,6);
        $endsub = substr($string_to_decrypt, -6,6);
        //echo $sub." - ".$endsub."<br>\n";
        if($sub=="yxzzxy" && $endsub == "yxzzxy"){
            #neueverschluesselung
            $string_to_decrypt = substr($string_to_decrypt,6,-6);
            $string_to_decrypt = base64_decode($string_to_decrypt);
            if($this->userunit >= " "){
                $crypt = new crypt($this->userunit);
            }else{
                $crypt = new crypt(UNIT);
            }

            $this->crypt = $crypt;
            $rtn = $crypt->xDecrypt($string_to_decrypt);
        }else{
            $string_to_decrypt = base64_decode($string_to_decrypt);
            $rtn = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->ky, $string_to_decrypt, MCRYPT_MODE_CBC, $this->iv);
            $rtn = rtrim($rtn, "\0\4");
        }
        return($rtn);
    }

    /**
     * Function to encrypt a string
     * @param string $string_to_encrypt
     * @return string
     */
    function EncryptData($string_to_encrypt){

        if(CRYPTDEFAULT=="0" || $this->oldencrypt =="1"){
            $rtn = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->ky, $string_to_encrypt, MCRYPT_MODE_CBC, $this->iv);
            $rtn = base64_encode($rtn);
        }elseif(CRYPTDEFAULT=="1"){
            if($this->userunit >= " "){
                $crypt = new crypt($this->userunit);
            }else{
                $crypt = new crypt(UNIT);
            }
            $rtn = $crypt->xEncrypt($string_to_encrypt);
            $rtn = "yxzzxy".base64_encode($rtn)."yxzzxy";
        }
        return($rtn);
    }

    /**
     * Function string to hex
     * @param string $str
     * @return string
     */
    function ToHex($str){
        for($i=0; $i<=strlen($str)-1; $i++){
            $hex = dechex(ord(substr($str,$i,1)));
            if(strlen($hex) < 2) $hex = "0".$hex;
            $out .= $hex;
        }
        return $out;
    }

    /**
     * Function hex to string
     * @param string $hex
     * @return string
     */
    function ToStr($hex){
        while($i < strlen($hex)){
            $out .= chr(hexdec(substr($hex,$i,2)));
            $i += 2;
        }
        return $out;
    }


}