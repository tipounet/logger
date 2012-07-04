<?php
namespace logger\drivers;
include_once 'log.class.php';
class DriversBase{
	private $pdo;
	/**
	 * Récupère les logs pour les afficher
	 * @return array of \log\log
	 * @throws \Exception
	 */
	public function getLogs(){
		$sql = 'SELECT *
		FROM \'v_listlogs\' order by dateLog DESC';
		try {
			$result = $this->pdo->query($sql);
			if ($result === false){
				$e = $this->pdo->errorInfo();
				throw new \Exception('Erreur SQL : '.$e[2],
						$this->pdo->errorCode());
			}
			else {
				$ret = $result->fetchAll(\PDO::FETCH_CLASS,  'logger\pojo\log');
				$result->closeCursor();
				return $ret;
			}
		}
		catch(\PDOException $e){
			throw new \Exception(
					'Erreur SQL : '.$e->getMessage()."\r\n".$sql, 0,
					$e->getPrevious()
			);
		}
	}
	/**
	 * suppression d'un log ou de tous
	 * @param type $id
	 * @throws \Exception
	 */
	public  function delLog($id = null){
		$sql = 'delete from logs';
		if ($id !== null && is_numeric($id)){
			$sql .= ' where idlog='. $this->pdo->quote($id,\PDO::PARAM_INT);
		}
		try{
			$r = $this->pdo->query($sql);
			if ($r === false){
				throw new \Exception('Erreur SQL : '.$e->getMessage()."\r\n".
						$sql);
			}
			else {
				return true;
			}
		}
		catch (\PDOException $e){
			throw new \Exception('Erreur SQL : '.$e->getMessage()."\r\n".
					$sql,
					0,
					$e->getPrevious());
		}
	}

	/**
	 * permet d'ajouter une action dans le log
	 * @param string $action    : nom de l'acion sur 50 charactères
	 * @param string $infos     : Les informations supplémentaires (requete, avant / après etc
	 * @return boolean          : retour
	 * @throws \Exception
	 */
	public function addLog($severite, $message){
		if (!empty($severite)){
			if (!empty($message)){
				// on véfie que l'id utilisateur est connu du système
				try {
					$sql = 'insert into logs ( dateAction,severite,message) values(\''.date('Y/m/d h:i:s').'\', '.$this->pdo->quote($severite).',
					'.$this->pdo->quote($message).');';
					$r = $this->pdo->query($sql);
					if ($r === false){
						$e = $this->pdo->errorInfo();
						throw new \Exception('Erreur SQL : '. $e[2],$e[1]);
					}
					else {
						return true;
					}
				}
				catch (\PDOException $e){
					throw new \Exception(
							'Erreur SQL : '. $e->getMessage()."\r\n".
							'Avec la requete : '.$sql, 0, $e->getPrevious());
				}
			}
			else {
				//infos vide, est ce grave ?
				throw new \Exception('Le message est obligatoire');
			}
		}
		else {
			throw new \Exception('La sévérité est obligatoire');
		}

	}

}