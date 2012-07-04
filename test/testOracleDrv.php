<?php
/**
 * test unitaire de la classe \logger\logger
 */

namespace logger\tests\units;

require_once 'mageekguy.atoum.phar';
require '../drivers/OracleDrv.class.php';

use \mageekguy\atoum;

class OracleDrv extends atoum\test {
    /**
     *Test insertion log
     */
    public function testAddLog() {
    	$proprietes = new util\Properties ();
    	$proprietes->put ( 'driver', 'oracle' );
    	$proprietes->put ( 'SID', 'kertaz' );
    	$proprietes->put ( 'username', 'logger' );
    	$proprietes->put ( 'passwd', 'log' );
    	$proprietes->put ( 'host', 'localhost' );

        $drv = new \logger\drivers\OracleDrv($proprietes);

        $this->assert()
                ->boolean($drv->addLog(52, 'insertion', 'Insertion test unitaire avec atoum' .
                                "\r\n" . __FILE__ .
                                "\r\n" . __CLASS__ .
                                "\r\n" . __METHOD__))->isEqualTo(true)
        ;
    }

    /**
     *Test de la recup de la liste des logs
     */
    public function testGetLog(){
        $proprietes = new util\Properties ();
    	$proprietes->put ( 'driver', 'oracle' );
    	$proprietes->put ( 'SID', 'kertaz' );
    	$proprietes->put ( 'username', 'logger' );
    	$proprietes->put ( 'passwd', 'log' );
    	$proprietes->put ( 'host', 'localhost' );

        $drv = new \logger\drivers\OracleDrv($proprietes);

    }
    /**
     *Test des la suppression des log
     */
    public function testDelLog(){
        $proprietes = new util\Properties ();
    	$proprietes->put ( 'driver', 'oracle' );
    	$proprietes->put ( 'SID', 'kertaz' );
    	$proprietes->put ( 'username', 'logger' );
    	$proprietes->put ( 'passwd', 'log' );
    	$proprietes->put ( 'host', 'localhost' );

        $drv = new \logger\drivers\OracleDrv($proprietes);

    }
}

?>