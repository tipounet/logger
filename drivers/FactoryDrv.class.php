<?php
/**
 * Cette classe permet l'abstraction totale du sgbd
 *
 * @author moogli
 *
 */
namespace logger\drivers;

class FactoryDrv{
	/**
	 * Permet de récupérer le driver instancier avec les infos.
	 * @param \util\Properties $properties
	 * @throws \Exception
	 * @return unknown
	 */
	public static function getDriver(\util\Properties $properties){
		if ($properties->containsKey('driver')){
			$class = ucfirst($properties->get('driver')).'Drv';
			if (file_exists('drivers/'.$class.'.class.php')){
				include_once('drivers/'.$class.'.class.php');
				$reflection_object = new \ReflectionClass( 'logger\drivers\\'.$class );
				$drv = $reflection_object->newInstanceArgs( array($properties) );
				return $drv;
			}
			else{
				throw new \Exception('le fichier '.$class.' existe pas');
			}
		}
		else {
			throw new \Exception(__FILE__.' ligne ' . __LINE__ .' =>Il faut indiquer un driver');
		}
	}
}
