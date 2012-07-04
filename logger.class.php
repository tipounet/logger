<?php
namespace logger;
use logger\drivers\FactoryDrv;

include_once 'util/Properties.class.php';
include_once 'drivers/FactoryDrv.class.php';
/**
 *
 * @author moogli
 * @version 2.0
 *
 */
class logger {

	const version = '1.0';
	const  author ='moogli';
	const  support = 'moogli@phpjungle.info';
	/**
	 * Propriété de la connexion au drivers
	 * @var properties
	 */
	private $properties;
	/**
	 * Le driver utilisé
	 * @var objet
	 */
	private $driver;
	/**
	 * Instance du logger
	 * @var  logger\logger
	 */
	private static $instance;
	/**
	 * Les constantes permettant un accès direct au propriété en faisant un abstraction
	 *  de la base
	 * @var String
	 */
	const info = 'info';
	const severe = 'severe';
	const warning = 'warning';
	const critique = 'critique';
	/**
	 * constructeur privé c'est un singleton
	 * @param \util\Properties $p
	 * @throws \Exception
	 */
	private function __construct(\util\Properties $p) {
		$this->properties = $p;
		try {
			$this->driver = FactoryDrv::getDriver( $this->properties );
		} catch ( \Exception $e ) {
			throw new \Exception ( $e->getMessage (), $e->getCode (), $e->getPrevious () );
		}
	}
	/**
	 * permet de recuperer l'instance du logger et de la creer le cas échéant
	 *
	 * @return type
	 */
	public static final function getInstance(\util\Properties $p = null) {
		if ($p === null || !is_object ( $p )) {
			$p = new \util\Properties ();
			$p->put ( 'driver', 'sqlite' );
			$p->put ( 'path', '' );
		}
		if (logger::$instance === null) {
			logger::$instance = new logger($p);
		}
		return logger::$instance;
	}
	/**
	 * Permet l'insertion d'un log
	 *
	 * @param int $severite
	 * @param String $message
	 */
	public function addLog($severite, $message) {
		$this->driver->addLog ( $severite, $message );
	}
	/**
	 * Récupère la liste des logs
	 *
	 * @return array
	 */
	public function getLogs() {
		return $this->driver->getLogs ();
	}
	/**
	 * alias de addLog(info,$message)
	 *
	 * @param string $message
	 */
	public function info($message) {
		$this->addLog ( loggerv2::info, $message );
	}
	/**
	 * alias de addLog(warning,$message)
	 *
	 * @param string $message
	 */
	public function warning($message) {
		$this->addLog ( loggerv2::warning, $message );
	}
	/**
	 * alias de addLog(severe,$message)
	 *
	 * @param string $message
	 */
	public function severe($message) {
		$this->addLog ( loggerv2::severe, $message );
	}
	/**
	 * alias de addLog(critique,$message)
	 *
	 * @param string $message
	 */
	public function critique($message) {
		$this->addLog ( loggerv2::critique, $message );
	}
	/**
	 * Permet l'initialisation du support de sauvegarde depuis le pilote
	 */
	public function init(){
		$this->driver->init();
	}
	/**
	 * Permet la suppression des log ou d'un log précis
	 * @param int $idLog
	 */
	public function delLog($idLog =null){
		return $this->driver->delLog($idLog);
	}

	/**
	 * retourne l'objet properties
	 * @return \logger\properties
	 */
	public function getProperties(){
		return $this->properties;
	}
	/**
	 * Indique l'objet properties
	 * @param \util\Properties $p
	 */
	public function setProperties(\util\Properties $p){
		$this->properties = $p;
	}
}