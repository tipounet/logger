<?php

namespace \phpjungle\dao;
/**
 * Cette classe étend PDO pour utiliser un fetch classe de compet
 * @author moogli
 * @version 0.1
 *
 */
class WrapperObjet extends \PDO {
	/**
	 * Le contenu du fichier XML
	 * @var String
	 */
	private $xmlFile;
	/**
	 * XML
	 * @var unknown_type
	 */
	private $xml;

	/**
	 * Constructeur
	 * @param String $dsn
	 * @param String $username
	 * @param String $passwd
	 * @param String $options
	 */
	public final function __construct($dsn, $username, $passwd, $options){
		parent::__construct($dsn, $username, $passwd, $options);
	}
	/**
	 * permet de retourner une collection d'objet complexe
	 */
	public final function fetch_multiClass(){
		//
	}
	/**
	 * Permet d'indique le nom du fichier
	 * @param unknown_type $file
	 * @throws \Exception
	 */
	public function setXmlFile($file){
		if (empty($file) === false){
			if(file_exists($file) === true){
				$this->xmlFile = file;
			}
			else {
				throw new \Exception('Le fichier n\'existe pas');
			}
		}
		else {
			throw new \Exception('Il faut indiquer un nom de fichier');
		}
	}

	/**
	 * cette methode permet de "transformer" le fichie xml et un tableu de "properties"
	 * qui aura en clef le nom de la table en value un "properties" avec les propriétés de la classe indiqué
	 *  (clef : nom du champ sql, valeur : nom de la propriété de classe
	 */
	private function parseXML(){

	}
}