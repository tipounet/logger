<?php
namespace logger\drivers;
include_once 'util/Properties.class.php';
include_once 'drivers/drivers.int.php';
include_once 'pojo/log.class.php';
/**
 * Permet l'utilisation du logger avec sqlite 3
 *
 * @author moogli
 * @version 1.0
 */
class SqliteDrv implements DriversInt {
	/**
	 * Instance de PDO
	 *
	 * @var \PDO
	 */
	private $pdo;
	const info = 'info';
	const severe = 'severe';
	const warning = 'warning';
	const critique = 'critique';
	/**
	 * constructeur
	 *
	 * @param \util\Properties $proprietes
	 * @throws \Exception
	 */
	public function __construct(\util\Properties $proprietes) {
		if ($proprietes->count () > 1) {
			// on vérifie que le driver est bien chargé
			if (extension_loaded ( 'pdo_sqlite' )) {
				if ($proprietes->containsKey ( 'path' ) == true) {
					$path = $proprietes->get ( 'path' );
					if (empty ( $path ) === true) {
						$this->pdo = new \PDO ( 'sqlite:logger.sqlite3' );
						$this->pdo->setAttribute (\PDO::ATTR_CASE, \PDO::CASE_LOWER);
						if ($this->getNbtable() < 0){
							$this->init();
						}
					} else {
						$this->pdo = new \PDO ( 'sqlite:' . $path );
						$this->pdo->setAttribute (\PDO::ATTR_CASE, \PDO::CASE_LOWER);
					}
				} else {
					throw new \Exception ( 'Le chemin vers la base est obligatoire' );
				}
			} else {
				throw new \Exception ( 'Le driver pdo_sqlite n\'est pas chargé, impossible de l\'utiliser' );
			}
		} else {
			// cas par défaut reste a espérer qu'un utilisateur par défaut
			// existe, ou accès anonyme :)
			echo 'path par defaut';
			$this->pdo = new \PDO ( 'sqlite:logger.sqlite3' );
		}
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see logger\drivers.DriversInt::initBase()
	 */
	public function init() {
		$severite = 'create table severites( idSeverite integer primary key autoincrement not null, nom varchar(10));';
		$sevData = <<<insert
insert into severites (nom) values('warning');
insert into severites (nom) values('info');
insert into severites (nom) values('severe');
insert into severites (nom) values ('critique');
insert;
		$logs = 'CREATE TABLE logs ( idlog integer primary key autoincrement not null, dateAction DATETIME NOT NULL DEFAULT current_date, message text not null, idSeverite integer not null,
		constraint fk_severite foreign key(idSeverite) references severites(idSeverite) );';
		$vue = 'create view \'v_listlogs\' as select logs.idLog AS id,strftime(\'%d/%m/%Y %h:%i:%s\',logs.dateAction) AS dateLog,logs.message AS message,severites.nom AS severite from
		(logs join severites on((logs.idSeverite = severites.idSeverite))) order by logs.dateAction desc;';

		try {
			$r = $this->pdo->exec ( 'Drop table if exists logs;' );
			$r = $this->pdo->exec ( 'Drop view if exists severites;' );
			$r = $this->pdo->exec ( 'Drop view if exists v_listlogs;' );
			echo 'creation de la severite <br />';
			$r = $this->pdo->exec ( $severite );
			$e = $this->pdo->errorInfo();
			var_dump($r,$e);
			echo '<hr />';
			echo 'data severite<br />';
			$r = $this->pdo->exec ( $sevData );
			$e = $this->pdo->errorInfo();
			var_dump($r,$e);
			echo '<hr />';
			echo 'creation des logs<br />';
			$r = $this->pdo->exec ( $logs );
			$e = $this->pdo->errorInfo();
			var_dump($r,$e);
			echo '<hr />';
			echo 'creation de la vue <br />';
			$r = $this->pdo->exec ( $vue );
			$e = $this->pdo->errorInfo();
			var_dump($r,$e);
			echo '<hr />';
		} catch ( \PDOException $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage (), 0, $e->getPrevious () );
		}
	}
	/**
	 * Permet de savoir combien il y a les tables dans la base pour ne pas essayer
	 * de la créer !
	 *
	 * @todo Modif
	 *       Le plus simple va être de compter si les 3 tables sont la avec un
	 *       in () dans le where ?
	 * @throws \Exception
	 * @deprecated
	 */
	protected function getNbtable() {
		$sql = 'select count(*) as nb from sqlite_master';
		try {
			$ret = $this->pdo->query ( $sql );
			if ($ret === false) {
				$err = $this->pdo->errorInfo ();
				throw new \Exception ( $err [2] );
			} else {
				$data = $ret->fetch ( \PDO::FETCH_OBJ );
				$ret->closeCursor ();
				return $data->nb;
			}
		} catch ( \Exception $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage (), 0, $e->getPrevious () );
		}
	}

	/**
	 * Récupère les logs pour les afficher
	 *
	 * @return array of \log\log
	 * @throws \Exception
	 */
	public function getLogs() {
		$sql = 'SELECT *
		FROM \'v_listlogs\' order by dateLog DESC';
		try {
			$result = $this->pdo->query ( $sql );
			if ($result === false) {
				$e = $this->pdo->errorInfo ();
				throw new \Exception ( 'Erreur SQL : ' . $e [2] );
			} else {
				$ret = $result->fetchAll ( \PDO::FETCH_CLASS, 'logger\pojo\log' );
				$result->closeCursor ();
				return $ret;
			}
		} catch ( \PDOException $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () . "\r\n" . $sql, 0, $e->getPrevious () );
		}
	}
	/**
	 * suppression d'un log ou de tous
	 *
	 * @param type $id
	 * @throws \Exception
	 */
	public function delLog($id = null) {
		$sql = 'delete from logs';
		if ($id !== null && is_numeric ( $id )) {
			$sql .= ' where idlog=' . $this->pdo->quote ( $id, \PDO::PARAM_INT );
		}
		try {
			$r = $this->pdo->query ( $sql );
			if ($r === false) {
				$e = $this->pdo->errorInfo ();
				throw new \Exception ( 'Erreur SQL : ' . $e [2] . "\r\n" . $sql, $this->pdo->errorCode () );
			} else {
				return true;
			}
		} catch ( \PDOException $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () . "\r\n" . $sql, 0, $e->getPrevious () );
		}
	}

	/**
	 * permet d'ajouter une action dans le log
	 *
	 * @param string $action
	 *        	: nom de l'acion sur 50 charactères
	 * @param string $infos
	 *        	: Les informations supplémentaires (requete, avant / après etc
	 * @return boolean : retour
	 * @throws \Exception
	 */
	public function addLog($severite, $message) {
		if (! empty ( $severite )) {
			if (! empty ( $message )) {
				// on véfie que l'id utilisateur est connu du système
				try {
					$sql = 'insert into logs ( dateAction,idSeverite,message) values(\'' . date ( 'Y/m/d H:i:s' ) . '\', ' .
							$this->getIdSeverite ( $severite ) . ',' . $this->pdo->quote ( $message ) . ');';
					$r = $this->pdo->query ( $sql );
					if ($r === false) {
						$e = $this->pdo->errorInfo ();
						throw new \Exception ( 'Erreur SQL : ' . $e [2], $e [1] );
					} else {
						return true;
					}
				} catch ( \PDOException $e ) {
					throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () . "\r\n" . 'Avec la requete : ' . $sql, 0, $e->getPrevious () );
				}
			} else {
				// infos vide, est ce grave ?
				throw new \Exception ( 'Le message est obligatoire' );
			}
		} else {
			throw new \Exception ( 'La sévérité est obligatoire' );
		}

	}
	/**
	 * Permet de retourner la clef primaire de la sévérité
	 *
	 * @param string $severite
	 * @throws \Exception
	 */
	private function getIdSeverite($severite) {
		$sql = 'select idSeverite from severites where nom=' . $this->pdo->quote ( $severite );
		$ret = $this->pdo->query ( $sql );
		if ($ret === false) {
			$e = $this->pdo->errorInfo ();
			throw new \Exception ( 'Erreur SQL : ' . $e [2] . "\r\n Avec la requete : " . $sql, $e [1] );
		} else {
			$data = $ret->fetch(\PDO::FETCH_OBJ);
			if ($data === false){
				$e = $ret->errorInfo();
				throw new \Exception($ret->errorInfo()[2]);
			}
			if (isset($data->idseverite))
				return $data->idseverite;
			else
				return null;
		}
	}
}