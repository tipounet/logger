<?php
namespace logger\drivers;
include_once 'util/Properties.class.php';
include_once 'drivers/drivers.int.php';
include_once 'pojo/log.class.php';
// include_once 'drivers/DriverBase.class.php';
/**
 * Permet l'utilisation du logger avec mysql
 * @author moogli
 * @version 1.0
 */
class MysqlDrv implements DriversInt {
	/**
	 * Instance de PDO
	 *
	 * @var \PDO
	 */
	private $pdo;
	/**
	 * constructeur
	 *
	 * @param \util\Properties $properties
	 * @throws \Exception
	 */
	public function __construct(\util\Properties $proprietes) {
		if ($proprietes->count () > 1) {
			// on vérifie que le driver est bien chargé
			if (extension_loaded ( 'pdo_mysql' )) {
				// dans les autres cas on vérifie qu'il y ai au moins un "host"
				if ($proprietes->containsKey ( 'host' ) == true) {
					$host = $proprietes->get ( 'host' );
					if (empty ( $host) === false) {
						if ($proprietes->containsKey ( 'username' ) === true) {
							if ($proprietes->containsKey ( 'passwd' ) === false)
								$proprietes->put('passwd','');
							$this->pdo = new \PDO ( 'mysql:host=' .
									$proprietes->get ( 'host' ) . ';dbname=' .
									$proprietes->get ( 'dbname' ) . ';',
									$proprietes->get ( 'username' ),
									$proprietes->get( 'passwd' ) );
							$this->pdo->setAttribute( \PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION );
							$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
							$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
							$this->pdo->setAttribute (\PDO::ATTR_CASE, \PDO::CASE_LOWER);
							/**/
							//$this->pdo = new \PDO ( 'mysql:host=localhost;dbname=logger;', 'root', '' );
							return true;
						} else {
							throw new \Exception ( 'Le nom d\'utilisateur est obligatoire' );
						}
					} else {
						throw new \Exception ( 'Le nom de base de données ne peux être vide' );
					}
				} else {
					throw new \Exception ( 'L\'adresse du serveur est obligatoire' );
				}
			} else {
				throw new \Exception ( 'le driver pdo_mysql n\'est pas chargé, impossible de l\'utiliser' );
			}
		} else {
			// cas par défaut reste a espérer qu'un utilisateur par défaut
			// existe, ou accès anonyme :)
			$this->pdo = new \PDO ( "mysql:host=localhost;" );
		}
	}
	/**
	 * Permet l'initialisation de la base
	 * @throws \Exception
	 */
	public function init() {
		$sevData = <<<insert
insert into severites (nom) values('warning');
insert into severites (nom) values('info');
insert into severites (nom) values('severe');
insert into severites (nom) values ('critique');
insert;
		$requete = array (
				'dropView' => 'Drop view if exists v_listlogs;',
				'dropLog' => 'Drop table if exists logs;',
				'dropSev' => 'Drop table if exists severites;',
				'sev' => 'Create table severites( idSeverite integer primary key auto_increment not null, nom varchar(10)  not null);',
				'logs' => 'CREATE TABLE logs ( idLog integer primary key auto_increment not null, dateAction DATETIME NOT NULL , message text not null, idSeverite integer not null,
				constraint fk_severite foreign key(idSeverite) references severites(idSeverite) );',
				'view' => 'create view v_listlogs as
				select logs.idLog AS id,date_format(logs.dateAction,\'%d/%m/%Y %h:%i:%s\') AS dateLog,
				logs.message AS message,severites.nom AS severite from
				logs join severites using(idSeverite) order by logs.dateAction desc;',
				'sevData' => $sevData
		);
		try {
			// init auto ^^
			foreach($requete as $key => $rq){
				$this->pdo->exec ( $rq );
			}
		} catch ( \PDOException $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () , 0, $e->getPrevious () );
		}
	}
	/**
	 * Retourne le nombre de table de la base O_o
	 * est ce vraiment utile ?
	 * @throws \Exception
	 * @deprecated
	 */
	protected function getNbtable() {
		$requete = 'select count(*) as nb from information_schema.`TABLES` where TABLE_SCHEMA = \'logger\'';
		try {
			$ret = $this->pdo->exec($requete);
			if ($ret === false){
				$err = $this->pdo->errorInfo();
				throw new \Exception('Erreur SQL : ' . $e [2], $this->pdo->errorCode ());
			}
			else{
				$data = $ret->fetch(\PDO::FETCH_OBJ);
				if (isset($data->nb))
					return $data->nb;
				else
					return null;
			}
		}catch ( \PDOException $e ) {
			throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () , 0, $e->getPrevious () );
		}
	}
	/**
	 * Récupère les logs pour les afficher
	 *
	 * @return array of \log\log
	 * @throws \Exception
	 */
	public function getLogs() {
		$sql = 'SELECT * FROM v_listlogs order by dateLog DESC';
		try {
			$result = $this->pdo->query ( $sql );
			if ($result === false) {
				$e = $this->pdo->errorInfo ();
				throw new \Exception ( 'Erreur SQL : ' . $e [2], $this->pdo->errorCode () );
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
	 * Suppression d'un log ou de tous
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
				$e = $this->pdo->errorInfo();
				throw new \Exception ( 'Erreur SQL : ' . $e->getMessage () . "\r\n" . $sql );
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
					$sql = 'insert into logs ( dateAction,idSeverite,message) values(\'' . date ( 'Y/m/d H:i:s' ) . '\', ' . $this->getIdSeverite ( $severite ) . ',' . $this->pdo->quote ( $message ) . ');';
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
			// ok ?
			$data = $ret->fetch( \PDO::FETCH_OBJ );
			if ($data === false){
				$e = $ret->errorInfo();
				echo '<p class="avertissement erreur">Erreur : '.var_dump($e).'</p>';
				throw new \Exception($ret->errorInfo()[2]);
			}
			if (isset($data->idseverite))
				return $data->idseverite;
			else
				return null;
		}
	}
}