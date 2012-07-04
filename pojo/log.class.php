<?php

/**
 * Décrit un log
 *
 * @author Moogli
 * @version 1.2
 * @since 1.0 Doublage des propriétés en majuscule pour ce PD d'oracle !!!
 * @since 1.1 Suppression du camel case pour utiliser la propriété \PDO::ATTR_CASE => \PDO::CASE_LOWER de PDO
 */

namespace logger\pojo;

class log {

	private $id;
	private $datelog;
	private $message;
	private $severite;
	/**
	 * constructeur permet de préparer l'objet
	 * @param date $date
	 * @param string $severite
	 * @param string $message
	 * @todo voir si y a moyen d'utiliser le typage de severité en severite si jamais pdo supporte la chose à la EJB style
	 */
	public final function __contruct($date = null,  $severite = null, $message = null) {
		echo 'dans le constructeur du log ?<br />';
		var_dump($date,$severite,$message);
		$this->datelog = $date;
		$this->message = $message;
		$this->severite = $severite;
	}

	// <editor-fold defaultstate="collapsed" desc="Getter and Setter">
	public function getId() {
			return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getDateLog() {
			return $this->datelog;
	}

	public function setDateLog($date) {
		$this->datelog = $date;
	}

	public function getMessage() {
			return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function getSeverite() {
			return $this->severite;
	}

	public function setSeverite($severite) { // severite
		$this->severite = $severite;
	}
	// </editor-fold>
}

?>