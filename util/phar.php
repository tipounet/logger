<?php
namespace logger\util\phar;

/**
 * classe permttant la transformatio en phar
 * @author Moogli
 * @ todo a finir :)
 */
class phar extends \Phar{
private $filename;
private
	public final  function __construct($fname, $flags = null, $alias = null){
		parent::__construct($fname,$flags, $alias);
	}

}


$phar = $this->factory->build('phar', array($pharFile));

$phar['versions'] = serialize(array('1' => atoum\version, 'current' => '1'));

$phar->setStub($stub);
$phar->setMetadata(
		array(
				'version' => atoum\version,
				'author' => atoum\author,
				'support' => atoum\mail,
				'repository' => atoum\repository,
				'description' => $description,
				'licence' => $licence
		)
);
$phar->setSignatureAlgorithm(\phar::SHA1);