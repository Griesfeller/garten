<?php
namespace Grisu\Util;

class Logger
{

    /**
     * Placeholder for logfile name
     * @var unknown_type
     */
    var $logfile = "/tmp/textlog.txt";
    /**
     * Placeholder ot enable and disable the loggin
     * @var integer
     */
    var $loggen = "1"; // später mal auf 0 setzten

    /**
     *
     * Konstruktor für datenbankverbindung
     */
    function __construct(){
        $this->sprache  = new sprache();
        if(isset($GLOBALS["db"]["loggen"]["dbx"]) && isset($GLOBALS["db"]["loggen"]["dbxlesen"])){
            $this->dbx = $GLOBALS["db"]["loggen"]["dbx"];
            $this->dbx_lesen = $GLOBALS["db"]["loggen"]["dbx_lesen"];
        }else{
            parent::__construct(array("user"=>DBUSERloggen,"password"=>DBPASSloggen,"host"=>DBHOSTloggen,"database"=>DBDBloggen),array("user"=>DBUSERloggen,"password"=>DBPASSloggen,"host"=>DBHOST_lesenloggen,"database"=>DBDBloggen));
            $GLOBALS["db"]["loggen"]["dbx"] = $this->dbx;
            $GLOBALS["db"]["loggen"]["dbx_lesen"] = $this->dbx_lesen;
        }
        $this->nologgen = "1";
        #hier muß dann die kontrolle rein ob man loggen darf
        # möglichkeiut  ist eine Session wert setze und hier dann den $this->loggen = 1 oder 0 setzen
        if($_SESSION["loggen_db_loggen"]=="1"){ # wird gesetzt in /config.php
            $this->loggen = "1";
        }

    }

    /**
     *
     * Funktion zum erstellen eine Logging eintragens
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $text
     * @param string $werte
     */
    function loggen_sonstiges($userhash,$skript,$zeile,$thema,$text,$werte = ""){
        $sql = "insert into logging (userhash,skript,zeile,thema,text,werte,datum) values(?,?,?,?,?,?,NOW())";
        $array = array($userhash,$skript,$zeile,$thema,$text,$werte);
        if($this->loggen == "1"){
            $result = $this->exec($sql,$array);
        }
    }

    /**
     *
     * Funktion zum erstellen eine Logging eintragens
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $datenbank
     * @param string $tabelle
     * @param string $vorher
     */
    function loggen_geloescht($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$vorher){
        $sql = "insert into logging (userhash,skript,zeile,thema,datenbank,tabelle,vorher,datum) values(?,?,?,?,?,?,?,NOW())";
        $array = array($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$vorher);
        if($this->loggen == "1"){
            $result = $this->exec($sql,$array);
        }
    }

    /**
     *
     * Funktion zum erstellen eine Logging eintragens
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $datenbank
     * @param string $tabelle
     * @param string $vorher
     * @param string $nachher
     */
    function loggen_geaendert($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$vorher,$nachher){
        $sql = "insert into logging (userhash,skript,zeile,thema,datenbank,tabelle,vorher,nachher,datum) values(?,?,?,?,?,?,?,?,NOW())";
        $array = array($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$vorher,$nachher);
        if($this->loggen == "1"){
            $result = $this->exec($sql,$array);
        }
    }
    /**
     *
     * Funktion zum erstellen eine Logging eintragens
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $datenbank
     * @param string $tabelle
     * @param string $nachher
     */
    function loggen_erstellt($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$nachher){
        $sql = "insert into logging (userhash,skript,zeile,thema,datenbank,tabelle,nachher,datum) values(?,?,?,?,?,?,?,NOW())";
        $array = array($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$nachher);
        if($this->loggen == "1"){
            $result = $this->exec($sql,$array);
        }
    }

    /**
     * Funktion zum erstellen eine Logging eintragens
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $datenbank
     * @param string $tabelle
     * @param string $nachher
     */
    function loggen_error($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$nachher){
        $sql = "insert into logging (userhash,skript,zeile,thema,datenbank,tabelle,nachher,datum) values(?,?,?,?,?,?,?,NOW())";
        $array = array($userhash,$skript,$zeile,$thema." ERROR",$datenbank,$tabelle,$nachher);
        if($this->loggen == "1"){
            $result = $this->exec($sql,$array);
        }
    }
    /**
     * Funktion zum loggen von Ereignissen in eine Textdaten
     * @param string $userhash
     * @param string $skript
     * @param string $zeile
     * @param string $thema
     * @param string $datenbank
     * @param string $tabelle
     * @param string $nachher
     */
    function loggen_textfile($userhash,$skript,$zeile,$thema,$datenbank,$tabelle,$nachher){
        $fp = fopen($this->logfile,"a+");
        fputs($fp,"\n".date("Y-m-d H:i:s")."/////"."$userhash///$skript///$zeile///$thema///$datenbank///$tabelle///$nachher");
        fclose($fp);
    }




}