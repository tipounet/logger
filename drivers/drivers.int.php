<?php
namespace logger\drivers;

interface DriversInt {
	public function __construct(\util\Properties $properties);
	public function init(); // private ?
	public function addLog($severite, $message);
	public function delLog($id = null);
	public function getLogs();
}