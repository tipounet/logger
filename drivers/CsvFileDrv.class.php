<?php
namespace logger\drivers;
/*
 use util\Properties;
use logger\drivers\DriversInt;
use logger\log\log;
use logger\loggerv2;
*/
require_once 'util/Properties.class.php';
require_once 'pojo/log.class.php';
require_once 'drivers/drivers.int.php';

/**
 * Cette classe permet l'utilisation de fichier texte brute pour les logs
 * prendre une format type csv, le séparateur sera la tube (|)
 * @author moogli
 * @version 1.0
 *
 */
class CsvFileDrv implements DriversInt {
	/**
	 * Chemin vers le fichier de log
	 * @var string
	 */
	private $fileName;
	/**
	 * Le format de date à utiliser
	 * @var String
	 * TODO faut voir comment valider un format de date !
	 */
	private $dateFormat;
	/**
	 * Le délimiteur du fichier CSV defaut |
	 * @var unknown_type
	 */
	private $delimiteur;
	/**
	 * La sévérité info
	 * @var String
	 */
	const info = 'info';
	/**
	 * La sévérité sevère
	 * @var String
	 */
	const severe = 'severe';
	/**
	 * La sévérité warning
	 * @var String
	 */
	const warning = 'warning';
	/**
	 * La sévérité info
	 * @var String
	 */
	const critique = 'critique';
	/**
	 *
	 * @see DriversInt::initBase()
	 *
	 */
	public function init() {
		$this->delimiteur = '|';
	}

	/**
	 *
	 * @see DriversInt::getLogs()
	 *
	 */
	public function getLogs() {
		$f = file($this->fileName,FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
		$f = array_reverse($f, true);
		$return = array();
		foreach ($f as $k => $ligne){
			list($date,$severite,$message) = str_getcsv($ligne,'|','','\\');
			$l = new \logger\pojo\log();
			$l->setDateLog($date);
			$l->setMessage($message);
			$l->setSeverite($severite);
			$l->setId($k);
			$return[] = $l;
		}
		return $return;
	}

	/**
	 *
	 * @see DriversInt::addLog()
	 *
	 */
	public function addLog($severite, $message) {
		$severite = strtolower($severite);
		$sArray = ['info','severe','warning','critique'];
		if (in_array($severite, $sArray)){
			if (!empty($message)){
				$ligne = date($this->dateFormat).'|'.$severite.'|'.str_replace(["\r","\n"], '', $message)."\r\n";
				file_put_contents($this->fileName, $ligne,FILE_APPEND );
				return true;
			}
			else {
				throw new  \Exception('Le message ne peux être vide');
			}
		}
		else {
			throw new \Exception('La sévérité n\'existe pas !');
		}
	}

	/**
	 *
	 * @see DriversInt::__construct()
	 *
	 */
	public function __construct(\util\Properties $properties) {
		if ($properties->containsKey('path')){
			$this->fileName = $properties->get('path');
			if (empty($this->fileName)){
				$this->fileName = 'logs/'.date('Ymdhis').'.log';
			}
		}
		else {
			// nom de fichier par defaut ?
			$this->fileName = 'logs/'.date('Ymdhis').'.log';
		}
		if ($properties->containsKey('dateFormat')){
			$this->dateFormat = $properties->get('dateFormat');
			if (empty($this->dateFormat))
				$this->dateFormat = 'Y-m-d H:i:s';
		}
		else {
			$this->dateFormat = 'Y-m-d H:i:s';
		}
		$this->init();
	}

	/**
	 *
	 * @see DriversInt::delLog()
	 *
	 */
	public function delLog($id = null) {
		if (empty($id) === true){
			return unlink($this->fileName);
		}
		else {
			$f = file($this->fileName);
			if (isset($f[$id])) {
				unset($f[$id]);
				file_put_contents($this->fileName, implode('',$f));
				return true;
			}
		}
		return false;
	}
	/**
	 * Permet de protéger le délimiteur du fichier
	 * @param String $str
	 * @return String
	 */
	private function sanitizeStr($str){
		if (!empty($str)){
			$str = str_replace('|', '\\'.$this->delimiteur, $str);
		}
		return $str;
	}
}

?>