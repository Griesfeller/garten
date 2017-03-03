<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 03.03.2017
 * Time: 10:51
 */

namespace Grisu\Util;


class csrfcheck
{

    /**
     *
     * verschlÃ¼sselung key
     * @var string
     */
    var $key = "YzIhsHf3847iXajsfDkaj";

    /**
     *
     * konstrutor
     */
    function __construct(){

    }

    /**
     *
     * Funktion zum erstellen eiens CSRF Codes mit einer haltbarkeit
     * @param integert $haltbarkeit # default one day
     * @return string
     */
    function GetTolken($haltbarkeit = 86400){
        $stamp = time() + $haltbarkeit;
        $prefix = substr(uniqid(rand()),0,8);
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);
        $encrypted_data = mcrypt_generic($td, $prefix.$stamp);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return base64_encode(trim($encrypted_data));
    }


    /**
     *
     * Funktion zum checken ob der stering ein gÃ¼tliger CSRF Code ist und noch ind ergÃ¼ltigkeit ist
     * @param string $tolken
     * @return string
     */
    function CheckTolken($tolken){
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);
        $decrypted_data = mdecrypt_generic($td, base64_decode($tolken));
        $decrypted_data = substr($decrypted_data,8,strlen($decrypted_data));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $expires = trim($decrypted_data);
        if($expires < time()){
            $loggen = new loggen();
            $loggen->loggen_error(USERID, __FILE__, __LINE__, "CSRF ", $tolken, $expires, $decrypted_data." ".print_r($_REQUEST,TRUE));
            return 1; // testweise muÃŸ spÃ¤ter wieder heraus
            return 0;
        }else{
            return 1;
        }

    }

    /**
     *
     * Hex encode
     * @param string $input
     * @return string
     */
    function HexCode($input){
        for($i=0; $i<=strlen($input); $i++){
            $hex = dechex(ord(substr($input, $i, 1)));
            if(strlen($hex) == 1) $hex = "0$hex";
            $out .= $hex;
        }
        return $out;
    }

    /**
     *
     * Hex decode
     * @param string $hexinput
     * @return string
     */
    function HexDecode($hexinput){
        for($i=0; $i<=strlen($hexinput); $i++){
            $out .= chr(hexdec(substr($hexinput,$i,2)));
            $i++;
        }
        return $out;
    }

}