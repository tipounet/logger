<?php
namespace logger\drivers;
define('PATH', 'h:/web/docRoot/logger/');
include_once PATH.'util/Properties.class.php';
include_once PATH.'drivers/drivers.int.php';
include_once PATH.'pojo/log.class.php';
// include_once 'drivers/DriverBase.class.php';
/**
 * Permet l'utilisation du logger avec oracle (11g pourles test)
 * @author moogli
 * @version 1.0
 *
 */
class OracleDrv implements DriversInt {
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
			if (extension_loaded ( 'pdo_oci' )) {
				// dans les autres cas on vérifie qu'il y ai au moins un "host"
				if ($proprietes->containsKey ( 'host' ) == true) {
					$host = $proprietes->get ( 'host' );
					if (empty ( $host) === false) {
						if ($proprietes->containsKey ( 'SID' ) === true) {
							if ($proprietes->containsKey ( 'username' ) === true) {
								// si passwd n'existe pas on le créer vide
								if ($proprietes->containsKey ( 'passwd' ) === false)
									$proprietes->put('passwd','');
								// si le port n'est pas indiqué on utilise le port par defaut (1521)
								if ($proprietes->containsKey ( 'port' ) === false)
									$proprietes->put('port',1521);
								$this->pdo = new \PDO ( 'oci:dbname=//' .
										$proprietes->get ('host') . ':'.
										$proprietes->get ('port').'/' .
										$proprietes->get ('SID') . ';',
										$proprietes->get ('username'),
										$proprietes->get ('passwd') );
								$this->pdo->setAttribute( \PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION );
								$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
								$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
								$this->pdo->setAttribute (\PDO::ATTR_CASE, \PDO::CASE_LOWER);
								return true;
							} else {
								throw new \Exception ( 'Le nom d\'utilisateur est obligatoire' );
							}
						}else {
							throw new \Exception ( 'Le SID est obligatoiree' );
						}
					} else {
						throw new \Exception ( 'Le nom serveur ne peux être vide' );
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
		$trgSev = <<<trg
		create or replace
TRIGGER trg_severite before insert
on severites for each row
declare
    integrity_error  exception;
    errno            integer;
    errmsg           char(200);
    dummy            integer;
    found            boolean;
begin
    if :new.idSeverite is null or :new.idSeverite = 0 then
  		select sq_severites.NEXTVAL INTO :new.idSeverite from dual;
    end if;
--  Traitement d'erreurs
exception
    when integrity_error then
       raise_application_error(errno, errmsg);
end
trg;
		$trgLogs = <<<trgSev
		create or replace
TRIGGER trg_log before insert
on logs for each row
declare
    integrity_error  exception;
    errno            integer;
    errmsg           char(200);
    dummy            integer;
    found            boolean;
begin
    if :new.idLog is null or :new.idLog = 0 then
  		select sq_logs.NEXTVAL INTO :new.idLog from dual;
    end if;
--  Traitement d'erreurs
exception
    when integrity_error then
       raise_application_error(errno, errmsg);
end
trgSev;
		$requete = array (
				'dropView' => 'Drop view v_listlogs',
				'dropLog' => 'Drop table logs',
				'dropSev' => 'Drop table  severites',
				'dropSqSev' => 'Drop sequence sq_severites',
				'dropSqLogs' => 'Drop sequence sq_logs',
				'sev' => 'Create table severites(
				idSeverite integer not null  primary key,
				nom varchar(10)  not null)',
				'logs' => 'CREATE TABLE logs (
				idLog integer not null primary key,
				dateAction DATE NOT NULL ,
				message varchar2(500) not null,
				idSeverite integer not null,
				constraint fk_severite foreign key(idSeverite) references severites(idSeverite))',
				'view' => 'create view v_listlogs as select logs.idLog AS id,to_char(logs.dateAction,\'DD/MM/YYYY HH24:MI:SS\') AS dateLog,
				logs.message AS message,severites.nom AS severite from
				logs join severites using(idSeverite) order by logs.dateAction desc',
				'sqSev' =>'CREATE SEQUENCE sq_severites MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 21  noCACHE  NOORDER NOCYCLE',
				'sqLog' => 'CREATE SEQUENCE sq_logs MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 21 noCACHE  NOORDER NOCYCLE',
				'trgSev' => $trgSev,
				'trgLog' => $trgLogs,
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
	 * retourne le nombre de table de la base O_o
	 * est ce vraiment utile ?
	 * @throws \Exception
	 * @deprecated
	 */
	protected function getNbtable() {
		$requete = 'select (select count(*)  from USER_TABLES )as nbTable, (select count(*)  from USER_VIEWS) as nbvue from dual';
		try {
			$ret = $this->pdo->exec($requete);
			if ($ret === false){
				$err = $this->pdo->errorInfo();
				throw new \Exception('Erreur SQL : ' . $e [2], $this->pdo->errorCode ());
			}
			else{
				$data = $ret->fetch(\PDO::FETCH_OBJ);
				if (isset($data->NBTABLE) && isset($data->NBVUE))
					return $data->NBTABLE;
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
					$sql = 'insert into logs ( dateAction,idSeverite,message)
					values(to_date(\'' . date ( 'Y/m/d H:i:s' ) . '\', \'YYYY/MM/DD HH24:MI:SS\'),
					' . $this->getIdSeverite ( $severite ) . ',' . $this->pdo->quote ( $message ) . ')';
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
		$sql = 'select idseverite from severites where nom=' . $this->pdo->quote ( $severite );
		$ret = $this->pdo->query ( $sql );
		if ($ret === false) {
			$e = $this->pdo->errorInfo ();
			throw new \Exception ( 'Erreur SQL : ' . $e [2] . "\r\n Avec la requete : " . $sql, $e [1] );
		} else {
			// ok ?
			$data = $ret->fetch( \PDO::FETCH_OBJ );
			if ($data === false){
				$e = $ret->errorInfo();
				throw new \Exception('Erreur sur le retour des données : '.$ret->errorInfo()[2]);
			}
			if (isset($data->idseverite))
				return $data->idseverite;
			else
				return null;
		}
	}
}