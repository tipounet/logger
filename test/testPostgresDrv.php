<?php
/**
 * test unitaire de la classe \logger\drivers\PostgresDrv
 */

namespace logger\drivers\tests\units;

require_once 'mageekguy.atoum.phar';
require_once '../util/Properties.class.php';
require '../drivers/PostgresDrv.class.php';

use \mageekguy\atoum;

class PostgresDrv extends atoum\test {
	/**
	 * Test du constructeur
	 */
	public function testConstruct(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		$version = $D->versionPgsql();
		$this->assert
		->string($version)
		->isEqualTo('PostgreSQL 9.1.4 on x86_64-unknown-linux-gnu, compiled by gcc (Debian 4.7.0-11) 4.7.0, 64-bit');
// 		print_r($version);
	}
	/**
	 * test initialisation des tables
	 */
	public function testInit(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		/*
		$ret = $D->init();
		$this->assert->boolean($ret)->isTrue();
		*/
	}
	/**
	 * Test de la récup des logs
	 */
	public function testGetLog(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		$log = $D->getLogs();
		print_r($log);
		$this->assert->array($log)->isNotEmpty();
	}
	/**
	 * Test de l'ajout de log
	 */
	public function testAddLog(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		$ret = $D->addLog ( 'warning', 'Il s agit la d\'|un test de log :) '.time() );
		$this->assert->boolean($ret)->isTrue();
	}
	/**
	 * Test del d'un log
	 * Attention ne fonctionne que s'il existe un log avec id 1 ^^
	 */
	public function tesDelLog(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		/*
		$ret = $D->delLog(1);
		$this->assert->boolean($ret)->isTrue();
		*/
	}
	/**
	 * Test del de tout les logs
	 */
	public function tesDelAllLog(){
		$proprietes = new \util\Properties ();
		$proprietes->put ('driver', 'Postgresql');
		$proprietes->put ('dbname', 'logger');
		$proprietes->put ('username', 'logger');
		$proprietes->put ('passwd', 'log');
		$proprietes->put ('host', '192.168.1.16');
		$D = new \logger\drivers\PostgresDrv($proprietes);
		/*
		$ret = $D->delLog();
		$this->assert->boolean($ret)->isTrue();
		*/
	}
}