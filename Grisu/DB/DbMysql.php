<?php
namespace Grisu\DB;

use Web\Core\Logger;
use Grisu\Interfaces\DbConnectionInterface;

class DbMysql implements DbConnectionInterface
{

    /**
     * Placeholder for connection array
     * @var array
     */
    var $connect = array();
    /**
     * Placeholder for databasename
     * @var string
     */
    var $database = "";
    /**
     * Placeholder for errormessages
     * @var string
     */
    var $dberror;
    /**
     * Placeholder for dbx Object
     * @var objekt
     */
    var $dbx;
    /**
     * Placeholder for dbx Object readed
     * @var objekt
     */
    var $dbx_lesen;
    /**
     * Placeholder for transaction
     * @var string
     */
    var $starttrans;
    /**
     * Placeholder for error in the transaction
     * @var string
     */
    var $errorintrans;
    /**
     * Placeholder if a rollback was made
     * @var string
     */
    var $hasrollback;

    /**
     * Placeholder for enable/disable Logging
     * @var init
     */
    var $nologgen = "1";

    /**
     * Function constructor with create db connection
     * @param array $dbconnectarray
     * @param array $dbconnectarray_lesen
     */
    function __construct(array $dbconnectarray=array(),array $dbconnectarray_lesen=array()){
        if(key_exists("user",$dbconnectarray) && key_exists("password",$dbconnectarray) && key_exists("host",$dbconnectarray) ){
            if(!key_exists("database",$dbconnectarray)){
                $dbconnectarray["database"] = DBDB;
            }
        }else{
            if(!key_exists("database",$dbconnectarray)){
                $dbconnectarray["database"] = DBDB;
            }
            $dbconnectarray = array("user"=>DBUSER,"password"=>DBPASS,"host"=>DBHOST,"database"=>$dbconnectarray["database"]);
        }

        if(is_object($GLOBALS["dbver"][$dbconnectarray["database"]]["dbx"])){
            $this->dbx = $GLOBALS["dbver"][$dbconnectarray["database"]]["dbx"];
        }else{
            $this->dbx = new MainDbx($dbconnectarray);
            $GLOBALS["dbver"][$dbconnectarray["database"]]["dbx"] = $this->dbx;
        }

        if(is_array($dbconnectarray_lesen) && count($dbconnectarray_lesen)>="1"){
            if(key_exists("user",$dbconnectarray_lesen) && key_exists("password",$dbconnectarray_lesen) && key_exists("host",$dbconnectarray_lesen) ){
                if(!key_exists("database",$dbconnectarray_lesen)){
                    $dbconnectarray_lesen["database"] = DBDB;
                }
            }else{
                if(!key_exists("database",$dbconnectarray_lesen)){
                    $dbconnectarray_lesen["database"] = DBDB;
                }
                $dbconnectarray = array("user"=>DBUSER,"password"=>DBPASS,"host"=>DBHOST_lesen,"database"=>$dbconnectarray_lesen["database"]);
            }

            if(is_object($GLOBALS["dbver"][$dbconnectarray["database"]]["dbx_lesen"])){
                $this->dbx_lesen = $GLOBALS["dbver"][$dbconnectarray["database"]]["dbx_lesen"];
            }else{
                $this->dbx_lesen = new MainDbx($dbconnectarray_lesen);
                $GLOBALS["dbver"][$dbconnectarray["database"]]["dbx_lesen"] = $this->dbx_lesen;
            }
        }
        $this->database = $dbconnectarray["database"];

    }

    /**
     *
     * Function to exec a SQL Satement on the writed dbconnection
     * @param string $sql
     * @param array $array
     * @return string
     */
    function exec($sql,array $array = array()){
        if($this->starttrans == "1" ){
            if($this->hasrollback == ""){
                $result =  $this->dbx->ExecStatement($sql,$array);
            }else{
                # sql noch execute because  transactionerror
            }
        }else{
            $result =  $this->dbx->ExecStatement($sql,$array);
        }

        if(!$this->error()){
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            }
            return $result;
        }else{
            if($this->starttrans == "1" ){
                $this->errorintrans = "1";
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($this, true));
                $this->rollback();
            }
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($this, true));
            }
            return NULL;
        }
    }

    /**
     *
     * Function to select many rows from a SQL Query
     * @param string $sql
     * @param array $array
     * @return array|NULL
     */
    function selectall($sql, array $array = array()){
        if($this->starttrans == "1" ){
            if($this->hasrollback == ""){
                $result = $this->dbx->FetchArray($sql,$array);
            }
        }else{
            if(is_object($this->dbx_lesen)){
                $result = $this->dbx_lesen->FetchArray($sql,$array);
            }else{
                $result = $this->dbx->FetchArray($sql,$array);
            }
        }
        #kontrolle Fehlermeldung
        if(!$this->error()){
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            }
            return $result;
        }else{
            if($this->starttrans == "1" ){
                $this->errorintrans = "1";
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
                $this->rollback();
            }
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            }
            return NULL;
        }
    }
    /**
     *
     * Function to select one rows from a SQL Query
     * @param string $sql
     * @param array $array
     * @return array|NULL
     */
    function selectone($sql,array $array = array()){
        if($this->starttrans == "1" ){
            if($this->hasrollback == ""){
                $result = $this->dbx->FetchArray($sql,$array,0,0);
            }
        }else{
            if(is_object($this->dbx_lesen)){
                $result = $this->dbx_lesen->FetchArray($sql,$array,0,0);
            }else{
                $result = $this->dbx->FetchArray($sql,$array,0,0);
            }
        }
        if(!$this->error()){
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            }
            return $result;
        }else{
            if($this->starttrans == "1" ){
                $this->errorintrans = "1";
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
                $this->rollback();
            }
            if($this->nologgen == "0"){
                Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            }
            return NULL;
        }
    }
    /**
     *
     * Function to get Error Messages
     * @return NULL|string
     */
    function error(){
        if(is_array($this->dbx->Error) && count($this->dbx->Error) >= 1){
            return $this->dberror = $this->dbx->Error[0]." -> ".$this->dbx->Error[1]." -> ".$this->dbx->Error[2];
        }
        if(is_array($this->dbx_lesen->Error) && count($this->dbx_lesen->Error) >= 1){
            return $this->dberror = $this->dbx_lesen->Error[0]." -> ".$this->dbx_lesen->Error[1]." -> ".$this->dbx_lesen->Error[2];
        }
    }
    /**
     *
     * Function to get the number of affected Rows
     * @return integer
     */
    function affected(){
        return $this->dbx->AffectedRows;
    }

    /**
     *
     * Function to get last insert ID
     * @return integer
     */
    function last_insert_id(){
        return  $this->dbx->LastInsertId;
    }

    /**
     * Function to start an Transaction
     */
    function starttransaction(){
        #echo "Start Transaction<br>\n";
        $result = $this->dbx->ExecStatement("START TRANSACTION;",array());
        $this->starttrans = "1";
        $this->hasrollback = "";
        $this->errorintrans = "";
        if(!$this->error()){

        }else{
            $loggen = new loggen();
            Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, print_r($sql, true) . " " . print_r($array, true) . " " . print_r($result, true));
            $this->rollback();
        }
    }

    /**
     *
     * Function to rollback a transaction
     */
    function rollback(){
        //$loggen = new loggen();
        //$loggen->loggen_textfile("", __FILE__, __LINE__, __METHOD__,  $sql, serialize($array),serialize($this));
        //echo "Fehler es wird zurÃ¼ckgerollt<br>\n";
        $result = $this->dbx->ExecStatement("ROLLBACK;",array());
        $this->hasrollback = "1";
    }

    /**
     *
     * Function to commit a transaction
     */
    function commit(){
        if($this->hasrollback=="1"){
            #darf nciht commiten muÃŸ Fehler melden
            return NULL;
        }
        $result = $this->dbx->ExecStatement("COMMIT;",array());
        $this->starttrans = "";
        $this->hasrollback = "";
        $this->errorintrans = "";
    }


    /**
     *
     * Function to get all fieldsname of a Table
     * @param string $table
     * @return array
     */
    function getfieldfromtable($table){
        $result = $this->selectall("SHOW FIELDS FROM `".addslashes($table)."`",array());
        foreach($result as $key=>$val){
            $returnarray[$key] = $val["Field"];
        }
        return $returnarray;
    }

    /**
     *
     * Function to get th array of field information form a Table
     * @param string $table
     * @return array
     */
    function getallfieldinfosfromtable($table){
        $result = $this->selectall("SHOW FIELDS FROM `".addslashes($table)."`",array());
        foreach($result as $key=>$val){
            $returnarray[$val["Field"]] = $val;
        }
        return $returnarray;

    }

    /**
     *
     * Function to get all Tablename
     * @return array
     */
    function getalltables(){
        $return = $this->selectall("show tables",array());
        foreach($return as $key=>$val){
            $returnarray[$val["Tables_in_".$this->database]] =$val["Tables_in_".$this->database];
        }
        return $returnarray;
    }

    /**
     *
     * Function to insert an array in an Table
     * @param string $table
     * @param array $array
     * @return integer
     */
    function insert_array_intable($table,$array){
        #setzen der Werte in ein array fÃ¼r den exec befehl
        $cache = new cache();
        if($cache->is_gecached($table."_getfieldfromtable")=="1"){
            $insertarray = $cache->get_gecached($table."_getfieldfromtable");
            #echo "aus dem cache<br>";
        }else{
            $insertarray = $this->getfieldfromtable($table);
            $cache->set_cache($table."_getfieldfromtable", $insertarray,"7200");
            #echo "nicht im cache<br>";
        }
        foreach($insertarray as $key=>$val){
            $wertearray[$val] = $array[$val];
        }
        foreach($wertearray as $key=>$val){
            $sqla .= "$key,";
            $sqlb .= "?,";
            $uebergabearray[] = $val;
        }
        $sql = "insert into `".addslashes($table)."` (".substr($sqla,0,-1).") values (".substr($sqlb,0,-1).")";
        $return = $this->exec($sql,$uebergabearray);
        if($this->error()){
            $loggen = new loggen();
            $loggen->loggen_sonstiges("", "", "", "dbg->insert_array_intable ERROR", print_r($sql,true)." ".print_r($uebergabearray,true), print_r($this->error(),true));
            return "0";
        }
        $loggen = new loggen();
        $loggen->loggen_sonstiges("", "", "", "dbg->insert_array_intable OKAY", print_r($sql,true)." ".print_r($uebergabearray,true), print_r($return,true));
        return "1";
    }

    /**
     * Function to update an array in an Table
     * @param string $table
     * @param array $array
     * @param string $wherezellenname
     * @return integer
     */
    function update_array_intable($table,$array,$wherezellenname){
        $cache = new cache();
        if($cache->is_gecached($table."_getfieldfromtable")=="1"){
            $updatearray = $cache->get_gecached($table."_getfieldfromtable");
            #echo "aus dem cache<br>";
        }else{
            $updatearray = $this->getfieldfromtable($table);
            $cache->set_cache($table."_getfieldfromtable", $updatearray,"7200");
            #echo "nicht im cache<br>";
        }
        foreach($updatearray as $key=>$val){
            $wertearray[$val] = $array[$val];
        }
        foreach($wertearray as $key=>$val){
            $sqla .= "$key = ? ,";
            $uebergabearray[] = $val;
        }
        $uebergabearray[] = $array[$wherezellenname];
        $sql =  "update `".addslashes($table)."` set  ".substr($sqla,0,-1)."    where `".$wherezellenname."` = ?";
        $return = $this->exec($sql,$uebergabearray);
        if($this->error()){
            $loggen = new loggen();
            $loggen->loggen_sonstiges("", "", "", "dbg->update_array_intable ERROR", print_r($sql,true)." ".print_r($uebergabearray,true), print_r($this->error(),true));
            return "0";
        }
        $loggen = new loggen();
        $loggen->loggen_sonstiges("", "", "", "dbg->update_array_intable OKAY", print_r($sql,true)." ".print_r($uebergabearray,true), print_r($return,true));
        return "1";
    }

    /**
     *
     * Function to convert date formats
     * @param string $datum
     * @param string $art
     * @param string $zurueck
     * return string|array
     */
    function convert_datum($datum,$art="/",$zurueck="iso"){
        if(strstr($datum,"." && $art == "/")){
            $art = ".";
        }
        switch ($art){
            case "-":
                $temp = explode("-",$datum);
                $timestamp = @mktime(0,0,0,$temp["1"],$temp["2"],$temp["0"]);
                break;
            case "-umgekehrt":
                $temp = explode("-",$datum);
                $timestamp = @mktime(0,0,0,$temp["1"],$temp["0"],$temp["1"]);
                break;

            case "iso":
                $temp = explode(" ",$datum);
                $datum = $temp[0];
                $uhrzeit = $temp[1];
                $tempdatum = explode(".",$datum);
                $tempuhrzeit = explode(":",$uhrzeit);
                $timestamp = @mktime($tempuhrzeit["0"],($tempuhrzeit["1"]<="29"?"00":"30"),"0",$tempdatum[1],$tempdatum[2],$tempdatum[0]);
                break;

            case "-iso":
                $temp = explode(" ",$datum);
                $datum = $temp[0];
                $uhrzeit = $temp[1];
                $tempdatum = explode("-",$datum);
                $tempuhrzeit = explode(":",$uhrzeit);

                $timestamp = @mktime($tempuhrzeit["0"],($tempuhrzeit["1"]<="14"?"00":($tempuhrzeit["1"]<="29"?"15":($tempuhrzeit["1"]<="44"?"30":"45"))),"0",$tempdatum[1],$tempdatum[2],$tempdatum[0]);
                break;

            case "-isovolle minuten":
                $temp = explode(" ",$datum);
                $datum = $temp[0];
                $uhrzeit = $temp[1];
                $tempdatum = explode("-",$datum);
                $tempuhrzeit = explode(":",$uhrzeit);
                $timestamp = @mktime($tempuhrzeit["0"],$tempuhrzeit["1"],"0",$tempdatum[1],$tempdatum[2],$tempdatum[0]);
                break;
            case ".":
                $temp = explode(".",$datum);
                $timestamp = @mktime(0,0,0,$temp[1],$temp[0],$temp[2]);
                break;
            case "leer":
                $jahr = substr($datum,0,4);
                $monat = substr($datum, 4,2);
                $tag = substr($datum, 6,2);
                $timestamp = @mktime(0,0,0,$monat,$tag,$jahr);
                break;
            case "/":
            default:
                $temp = explode("/",$datum);
                $timestamp = @mktime(0,0,0,$temp[0],$temp[1],$temp[2]);
                break;

        }
        switch ($zurueck){
            case "iso":
                return date("Y-m-d H:i:s",$timestamp);
                break;
            case "-date":
                return date("Y-m-d",$timestamp);
                break;
            case ".":
                return date("d.m.Y",$timestamp);
            case "timestamp":
                return $timestamp;
                break;
            case "array":
                return array("Jahr"=>date("Y",$timestamp),"Monat"=>date("m",$timestamp),"Tag"=>date("d",$timestamp),"Stunde"=>date("H",$timestamp),"Minute"=>date("i",$timestamp),"Sekunden"=>date("s",$timestamp));

        }




    }




    /**
     *
     * Function to sort an multi array
     * @param array $array
     * @param string $key
     * @param boolean $asc
     * @return array
     */
    function sortByOneKey(array $array, $key, $asc = true) {
        $result = array();

        $values = array();
        foreach ($array as $id => $value) {
            $values[$id] = isset($value[$key]) ? $value[$key] : '';
        }

        if ($asc) {
            asort($values);
        }
        else {
            arsort($values);
        }

        foreach ($values as $key => $value) {
            $result[$key] = $array[$key];
        }

        return $result;
    }

    /**
     * Function to create an HashCode
     * @param string $userhash
     * @param string $string
     * @param string $datei
     * @param string $hashart
     * @return string
     */
    function get_hashcode($userhash,$string,$datei="",$hashart="sha1"){
        $this->hashberechnen++;
        if($hashart=="md5"){
            $hash =  md5($this->userhash."//".$string."//".time().time());
        }elseif($datei>=" "){
            $hash = sha1($this->userhash."//".$string."//".$datei); # hash von dateien sind nur doppelt wenn es die selbe datei ist
            return $hash;
        }else{
            $hash =  sha1($this->userhash."//".$string."//".time().time());
        }
        $sql = "insert into test.benutztehashes (benutztehashes) values (?)";
        $array = array($hash);
        $this->exec($sql,$array);
        if($this->error()){
            if($this->hashberechnen>=500){

                return $this->get_hashcode($userhash,$string.$this->hashberechnen);
                //die("ALARM keine Hashs errechenbar");
            }
            return $this->get_hashcode($userhash,$string);
        }
        unset($this->hashberechnen);

        return $hash;

    }

    /**
     *
     * Function to convert an enum String to an array
     * @param string $string
     * @return array
     */
    function get_enum_inhalt($string){
        $enum = substr($string,5,-1);
        $enum = str_replace("'", "",$enum);
        $temp = explode(",",$enum);
        foreach($temp as $key=>$val){
            $neureturn[$val] = $val;
        }
        return $neureturn;
    }

    /**
     * Function to change , to . for numbers
     * @param string $string
     * @return string
     */
    function convertstring2number($string){
        return str_replace(",", ".", $string);
    }

    /**
     *
     * Function to generate a radom String
     * @param string $art
     * @param int $anzahl
     * @return string
     */
    function zufall($art,$anzahl){
        switch($art){
            case "numeric":
            case "n":
                $array = array("0"=>"9","1"=>"8","2"=>"7","3"=>"6","4"=>"5","5"=>"4","6"=>"3","7"=>"2","8"=>"1","9"=>"0",);
                break;
            case "alpha":
            case "a":
                $array = array("0" => "a","1" => "b","2" => "c","3" => "d","4" => "e","5" => "f","6" => "g","7" => "h","8" => "j","9" => "k","10" => "m","11" => "n","12" => "p","13" => "q","14" => "r","15" => "s","16" => "t","17" => "u","18" => "v","19" => "w","20" => "x","21" => "y","22" => "z",);
                break;
            case "numericalpha":
            case "alphanumeric":
            case "an":
            case "na":
                $array = array("0" => "a","1" => "b","2" => "c","3" => "d","4" => "e","5" => "f","6" => "g","7" => "h","8" => "j","9" => "k","10" => "m","11" => "n","12" => "p","13" => "q","14" => "r","15" => "s","16" => "t","17" => "u","18" => "v","19" => "w","20" => "x","21" => "y","22" => "z","23" => "0","24" => "1","25" => "2","26" => "3","27" => "4","28" => "5","29" => "6","30" => "7","31" => "8","32" => "9",);
                break;
            default:
                $array = array("0" => "a","1" => "b","2" => "c","3" => "d","4" => "e","5" => "f","6" => "g","7" => "h","8" => "j","9" => "k","10" => "m","11" => "n","12" => "p","13" => "q","14" => "r","15" => "s","16" => "t","17" => "u","18" => "v","19" => "w","20" => "x","21" => "y","22" => "z","23" => "0","24" => "1","25" => "2","26" => "3","27" => "4","28" => "5","29" => "6","30" => "7","31" => "8","32" => "9",);
                break;
        }
        $max = count($array) - 1;
        $anzahl = (int) $anzahl;
        for($i=0;$i<$anzahl;$i++){
            $zufall .= $array[mt_rand(0,$max)];
        }
        return $zufall;
    }

    /**
     * Function to convert a n Integer Byte in home clean string
     * @param integer $size
     * @param boolean $praefix
     * @param boolean $short
     * @return string
     */
    function byteconvert($size, $praefix=true, $short= true)
    {
        if($praefix === true)
        {
            if($short === true)
            {
                $norm = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            }
            else
            {
                $norm = array('Byte',
                    'Kilobyte',
                    'Megabyte',
                    'Gigabyte',
                    'Terabyte',
                    'Petabyte',
                    'Exabyte',
                    'Zettabyte',
                    'Yottabyte'
                );
            }
            $factor = 1000;
        }
        else
        {
            if($short === true)
            {
                $norm = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
            }
            else
            {
                $norm = array('Byte',
                    'Kibibyte',
                    'Mebibyte',
                    'Gibibyte',
                    'Tebibyte',
                    'Pebibyte',
                    'Exbibyte',
                    'Zebibyte',
                    'Yobibyte'
                );
            }
            $factor = 1024;
        }
        $count = count($norm) -1;
        $x = 0;
        while ($size >= $factor && $x < $count)
        {
            $size /= $factor;
            $x++;
        }
        $size = sprintf("%01.2f", $size) . ' ' . $norm[$x];
        return $size;
    }




    /**
     * Function to set an Errormessages
     * @param string $meldungart
     * @param string $text
     */
    function set_errormeldung($meldungart,$text){
        $GLOBALS["errormeldungen"]["anzahl"]["wert"]++;
        $wert = $GLOBALS["errormeldungen"]["anzahl"]["wert"];
        $GLOBALS["errormeldungen"]["meldungen"][$meldungart][$wert] = "$text";
    }

    /**
     * Function to check if errormessages are setting
     * @param string $meldungart
     */
    function check_errormeldungen_vorhanden($meldungart=""){
        if($GLOBALS["errormeldungen"]["anzahl"]["wert"]>=1){
            if(isset($meldungart) && strlen($meldungart) >1){
                if(is_array($GLOBALS["errormeldungen"]["meldungen"][$meldungart])){
                    #echo "1.1";
                    return "1";
                }
                #echo "0.1";
                return "0";
            }
            #echo "1.2";
            return "1";
        }
        #echo "0.2";
        return "0";
    }

    /**
     * Function to display all errormessages
     * @param string $meldungart
     */
    function display_errormeldungen($meldungart = ""){
        $divarray = array(
            "warning" => "alert-box",
            "success" => "alert-box success",
            "error" => "alert-box alert",
            "info" => "alert-box secondary",
        );
        if(isset($meldungart) && strlen($meldungart) >1){
            $return = $this->check_errormeldungen_vorhanden($meldungart);
        }else{
            $return = $this->check_errormeldungen_vorhanden();
        }
        if($return == "1"){
            if(isset($meldungart) && strlen($meldungart) >1){
                foreach($GLOBALS["errormeldungen"]["meldungen"][$meldungart] as $key=>$val){
                    $this->errormessage_string .= "$meldungart -> ".$val." -- ";
                    echo  "<div class=\"".$divarray[$meldungart]."\" >".$val."<a href=\"\" class=\"close\">&times;</a></div><br>";
                }
            }else{
                foreach($GLOBALS["errormeldungen"]["meldungen"] as $meldungart => $errorarray){
                    foreach($errorarray as $key=>$val){
                        echo "<div class=\"".$divarray[$meldungart]."\" >".$val."<a href=\"\" class=\"close\">&times;</a></div><br>";
                        $this->errormessage_string .= "$meldungart -> ".$val." -- ";
                    }
                }
            }
        }
    }

    /**
     * Function to return Error message without echo
     * @param string $meldungart
     * @return string
     */
    function echo_display_errormeldungen($meldungart = ""){
        $divarray = array(
            "warning" => "alert-box",
            "success" => "alert-box success",
            "error" => "alert-box alert",
            "info" => "alert-box secondary",
        );
        if(isset($meldungart) && strlen($meldungart) >1){
            $return = $this->check_errormeldungen_vorhanden($meldungart);
        }else{
            $return = $this->check_errormeldungen_vorhanden();
        }
        if($return == "1"){
            if(isset($meldungart) && strlen($meldungart) >1){
                foreach($GLOBALS["errormeldungen"]["meldungen"][$meldungart] as $key=>$val){
                    $this->errormessage_string .= "$meldungart -> ".$val." -- ";
                    $return .=  "".$val."<br>";
                }
            }else{
                foreach($GLOBALS["errormeldungen"]["meldungen"] as $meldungart => $errorarray){
                    foreach($errorarray as $key=>$val){
                        $return .=  "".$val."<br>";
                        $this->errormessage_string .= "$meldungart -> ".$val." -- ";
                    }
                }
            }
        }
        return $return;
    }


    /**
     *
     * Testfunktion ohne funktionen
     */
    function test(){
        #testfunktion fÃ¼r die Klasse
    }

}

