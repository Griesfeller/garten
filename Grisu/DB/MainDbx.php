<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 03.03.2017
 * Time: 14:26
 */

namespace Grisu\DB;



/**
 *
 * Klassen mainDBX
 * @package medisDOC
 * @author sgriesfeller
 * @version 1.0
 */
class MainDbx {
    /**
     *
     * variable fÃ¼r DB Objekt
     * @var objekt
     */
    var $DBH;
    /**
     *
     * Variable fÃ¼r DSN Verbindungsdaten
     * @var string
     */
    var $DSN;
    /**
     *
     * Variable fÃ¼r dbuser
     * @var string
     */
    var $User;
    /**
     *
     * Variable fÃ¼r DB Passwort
     * @var string
     */
    var $Password;
    /**
     *
     * Variable fÃ¼r Affected Rows
     * @var integer
     */
    var $AffectedRows = 0;
    /**
     *
     * Variable fÃ¼r LastinsertID
     * @var integer
     */
    var $LastInsertId;
    /**
     *
     * Varibale fÃ¼r Errormeldungen
     * @var string
     */
    var $Error;
    /**
     *
     * Variable fÃ¼r letztes SQL Query
     * @var string
     */
    var $LastSqlQuery;

    /**
     *
     * Funktion zum erstellen der DB Verbindung
     * @param array $con
     */
    function mainDBXg($con = array()){

        if(count($con) > 0){
            $this->User = $con["user"];
            $this->Password = $con["password"];
            if(strstr($con["host"],":")){
                $temp = explode(":",$con["host"]);
                $this->DSN = "mysql:dbname=".$con["database"].";host=".$temp["0"].";port=".$temp["1"];
            }else{
                $this->DSN = "mysql:dbname=".$con["database"].";host=".$con["host"];
            }
            $this->Connect();
        }
    }


    /**
     *
     * Funktion zum erneuten setzen der DB Verbindung
     * @param array $con
     */
    function SetConnection($con){
        $this->User = $con["user"];
        $this->Password = $con["password"];
        if(strstr($con["host"],":")){
            $temp = explode(":",$con["host"]);
            $this->DSN = "mysql:dbname=".$con["database"].";host=".$temp["0"].";port=".$temp["1"];
        }else{
            $this->DSN = "mysql:dbname=".$con["database"].";host=".$con["host"];
        }
        $this->Connect();
    }


    /**
     *
     * Funktion erneuern der DB Verbindung mit andern DB Benutzern
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    function SetDsn($dsn,$user,$password){
        $this->DSN = $dsn;
        $this->User = $user;
        $this->Password = $password;
        $this->Connect();
    }



    /**
     *
     * Setzen der Verbindung
     * @return boolean
     */
    function Connect(){
        $this->driver_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try {
            $this->DBH = @new PDO($this->DSN, $this->User, $this->Password, $this->driver_options);

        }
        catch(PDOException $e) {
            $GLOBALS["MASTERDB_FAILED"] = "1";
            return 0;
        }
        return 1;
    }



    /**
     *
     * Funktion zum liefern des Ergebnisses einer datenbankabfrage
     * @param string $SQL
     * @param array $ArrayParameters
     * @param boolean $style
     * @param boolean $all
     * @return array
     */
    function FetchArray($SQL, $ArrayParameters, $style=0, $all=1) {

        if($this->CheckDbh()){
            $this->LastSqlQuery = "SQL: ".$SQL." Werte: ".(is_array($ArrayParameters)?implode(",",$ArrayParameters):"");
            $stmt = $this->DBH->prepare($SQL);
            if($style != 0) $style = PDO::FETCH_BOTH; else $style = PDO::FETCH_ASSOC;
            if ($stmt->execute($ArrayParameters)) {
                if($all == 1)   {
                    $row = $stmt->fetchAll($style);
                } else {
                    $row = $stmt->fetch($style);
                }
            }
            $err = $stmt->errorInfo();
            if($err[0] != "0000") $this->Error = $stmt->errorInfo(); else unset($this->Error);
            $this->AffectedRows = $stmt->rowCount();
        }
        else{
            $this->Error = "\$this->DBH ist kein Objekt";
        }
        return $row;
    }


    /**
     *
     * Funktion zum absetzen einer Datenbankabfrage ohne ergebnisrÃ¼ckgabe
     * @param string $SQL
     * @param array $ArrayParameters
     * @return boolean
     */
    function ExecStatement($SQL, $ArrayParameters){
        if(!$this->CheckDbh()) return 0;
        $this->LastSqlQuery = "SQL: ".$SQL." Werte: ".(is_array($ArrayParameters)?implode(",",$ArrayParameters):"");
        $stmt = $this->DBH->prepare($SQL);
        if(is_object($stmt)){
            $ret = $stmt->execute($ArrayParameters);
            $err = $stmt->errorInfo();
            if($err[0] != "0000") $this->Error = $stmt->errorInfo(); else unset($this->Error);
            $this->LastInsertId = $this->DBH->lastInsertId();
        }
        else{
            $this->Error = "\$stmt ist kein Objekt";
        }
        return $ret;
    }


    /**
     *
     * Funktion zum checken ob die datenbankverbindung vorhanden ist
     * @return boolean
     */
    function CheckDbh(){
        if(is_object($this->DBH)) return 1; else return 0;
    }



    /**
     *
     * Funktion fÃ¼r SQL Statments die man zeilenweise abarbeiten (mit Befehl nextRow)
     * @param string $SQL
     * @param array $ArrayParameters
     * @param boolean $style
     */
    function FetchRows($SQL, $ArrayParameters, $style=0) {
        if($this->CheckDbh()) {
            $this->LastSqlQuery = "SQL: ".$SQL." Werte: ".(is_array($ArrayParameters)?implode(",",$ArrayParameters):"");
            $this->stmt = $this->DBH->prepare($SQL);
            if($style != 0) $style = PDO::FETCH_BOTH; else $style = PDO::FETCH_ASSOC;
            $this->stmt->execute($ArrayParameters);
            $err = $this->stmt->errorInfo();
            if($err[0] != "0000") $this->Error = $this->stmt->errorInfo(); else unset($this->Error);
        }
        else{
            $this->Error = "\$this->DBH ist kein Objekt";
        }
    }


    /**
     *
     * Liefert den nï¿½chsten DB Record zurï¿½ck
     * @param boolean $style
     * @return array
     */
    function NextRow($style=0){
        if($style != 0) $style = PDO::FETCH_BOTH; else $style = PDO::FETCH_ASSOC;
        return $this->stmt->fetch($style);
    }

}