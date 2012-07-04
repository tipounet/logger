<?php
/**
 * test unitaire de la classe \logger\logger
 */

namespace logger\tests\units;

require_once 'mageekguy.atoum.phar';
require '../logger.class.php';

use \mageekguy\atoum;

class logger extends atoum\test {
    /**
     *Test insertion log 
     */
    public function testAddLog() {
        $log = \logger\Logger::getInstance();

        $this->assert()
                ->boolean($log->addLog(52, 'insertion', 'Insertion test unitaire avec atoum' .
                                "\r\n" . __FILE__ .
                                "\r\n" . __CLASS__ .
                                "\r\n" . __METHOD__))->isEqualTo(true)
        ;
    }
    /**
     * Test recup instance \logger\logger
     */
    public function testGestInstance() {
        $log = \logger\Logger::getInstance();
        $this->assert('Test de la methode getInstance')
                ->object(\logger\Logger::getInstance())
                ->isInstanceOf('\Logger\logger')
                ->isIdenticalTo(\logger\logger::getInstance());
    }
    /**
     *Test de la recup de la liste des logs 
     */
    public function testGetLog(){
        $log = \logger\logger::getInstance();
        $this->assert('Test de la methode getLog')
        ->array($log->getLog())->isNotEmpty();
    }
    /**
     *Test des la suppression des log 
     */
    public function testDelLog(){
        $log = \logger\Logger::getInstance();
        $this->assert('Test de la methode delLog')
        ->boolean($log->delLog())->isEqualTo(true);
    }
}

?>