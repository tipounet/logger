<?php
namespace util;
/**
 * Cette classe contient une collection générique
 * @author moogli
 *
 */
class Properties implements \Iterator{
	/**
	 * les propriétées
	 * @var unknown_type
	 */
	private $p = array();

	public final function __construct(){
		// ?
		// p ets un couple clef valeur un simple tableau quoi :)
	}
	/**
	 * Permet l'insertion d'une nouvelle propriété
	 * @param mixed $k
	 * @param mixed $v
	 * @throws \Exception
	 */
	public final function put($k,$v){
		if (!empty($k)){
			$this->p[$k] = $v;
		}
		else {
			throw new \Exception('La clef ne peux être vide');
		}
	}
	/**
	 * Permet d'ajouter un tableau contenant plusieurs propriétés
	 * @param unknown_type $v
	 * @throws \Exception
	 */
	public final function putAll($v){
		if(is_array($v)){
			foreach ($v as $k => $vv){
				$this->p[$k] = $vv;
			}
		}
		else {
			throw new \Exception('Il faut passer un tableau en paramètre');
		}
	}
	/**
	 * Retourne une propriété à partir de son nom
	 * @param unknown_type $k
	 */
	public final function get($k){
		if (isset($this->p[$k])){
			return $this->p[$k];
		}
		else {
			//throw new \Exception('La clef '.$k . 'n\existe pas');
			return null;
		}
	}

	// implementation de l'interface itérator
	/**
	 * remettre a "zéro" l'itérateur
	 */
	public function rewind(){
		reset($this->p);
	}
	/**
	 * fournit l'élément courant du parcourt du tableau
	 */
	public function current(){
		return current($this->p);
	}
	/**
	 * retourne la clef de l'élément courant
	 */
	public function key(){
		return key($this->p);
	}
	/**
	 * Fait avancer le pointeur de lecture du tableau et retourne l'élément
	 * (retourne donc l'élément N+1 et fait avancer le curseur);
	 *
	 * @return mixed
	 */
	public function next(){
		return next($this->p);
	}
	/**
	 * Indique si l'élément est valid, s'il faut donc s'arreter ou non de boucler.
	 * @return boolean
	 */
	public function valid(){
		$key = key($this->p);
		return ($key !== NULL && $key !== FALSE);
	}
	/**
	 * retourne le nombre de propriété
	 * @return number
	 */
	public function count(){
		return count($this->p);
	}
	/**
	 * Permet de savoir une clef existe dans le tableau de propriétés
	 * array_key_exists ?
	 * @param boolean $key
	 */
	public function containsKey($key){
		return array_key_exists($key, $this->p);
	}
	/**
	 * permet de récupérer la clef d'une valeur précise
	 * une fonction php ? array_search ?
	 * @param mixed $value
	 */
	public function contains($value){
		$r = array_search($value, $this->p);
		if ($r !== false)
			return $this->p[$r];
		return false;
	}
}
?>