<?php
namespace logger\pojo;

class severite {
	private $idseverite;
	private $nom;
	/**
	 * constructeur les elementd sont facultif
	 * @param int $idseverite
	 * @param String $nom
	 */
	public function __construct($idseverite = null, $nom = null){
		$this->idseverite = intval($idseverite);
		$this->nom = $nom;
	}
	/**
	 * retourne l'id de la severité
	 */
	public function getIdSeverite(){
		return $this->idseverite;
	}
	/**
	 * set l'id de la sevérité
	 * @param int $id
	 */
	public function setIdSeverite($id){
		$this->idseverite = intval($id);
	}
	/**
	 * retourne le nom de la severité
	 */
	public function getNom(){
		return $this->nom;
	}
	/**
	 * set le nom de la sevérité
	 * @param String $nom
	 */
	public function setIdSeverite($nom){
		$this->nom = $nom;
	}
	
}