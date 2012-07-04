use logger\logger;

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
        Remove this if you use the .htaccess -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>Test logger</title>
<meta name="description" content="" />
<meta name="author" content="Moogli" />
<meta name="viewport" content="width=device-width; initial-scale=1.0" />
<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
<link rel="shortcut icon" href="../img/favicon.png" />
<link rel="apple-touch-icon" href="../img/apple-touch-icon.png" />
<!-- CSS depuis http://sickdesigner.com/resources/HTML5-starter-pack/index.html -->

<link rel="stylesheet" href="css/main.css" type="text/css"
	media="screen">
<!-- Prise en charge des navigateurs ie qui ne connaissent pas html 5 -->
<!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
</head>
<body>
	<div id="content" role="main">
		<?php
		include ('util/Properties.class.php');
		include ('logger.class.php');
		try {
			$proprietes = new util\Properties ();
			/* file * /
			$proprietes->put('driver', 'csvFile');
			$proprietes->put('path','log.log');
			/* Sqlite 3* /
			 $proprietes->put('driver', 'sqlite');
			$proprietes->put('path','testpath.sqlite3');

			/* mysql * /
			$proprietes->put ( 'driver', 'mysql' );
			$proprietes->put ( 'dbname', 'logger' );
			$proprietes->put ( 'username', 'logger' );
			$proprietes->put ( 'passwd', 'log' );
			$proprietes->put ( 'host', 'localhost' );
			/* oracle* /
			$proprietes->put ( 'driver', 'oracle' );
			$proprietes->put ( 'SID', 'kertaz' );
			$proprietes->put ( 'username', 'logger' );
			$proprietes->put ( 'passwd', 'log' );
			$proprietes->put ( 'host', 'localhost' );
			/* postgresql*/
			$proprietes->put ('driver', 'Postgres');
			$proprietes->put ('dbname', 'logger');
			$proprietes->put ('username', 'logger');
			$proprietes->put ('passwd', 'log');
			$proprietes->put ('host', '192.168.1.16');
			/**/
			$log = logger\logger::getInstance ( $proprietes );
			//$log->init(); // pour creer les tables

			if (isset ( $_GET ['clear'] )) {
				$log->delLog ( $_GET ['clear'] );
			}
			$r = $log->addLog ( 'warning', 'Il s agit la d\'|un test de log :) '.time() );
		} catch ( Exception $e ) {
			echo '<div class="avertissement erreur">' . utf8_encode ( nl2br ( $e->getMessage () ) ) . '<br />' .
					utf8_encode ( nl2br ( $e->getTraceAsString () ) ) . '</div>';
		}
		echo '<div class="avertissement attention">';
		echo '<h1>Pilote : '.$log->getProperties()->get('driver').'</h1>';
		echo '<p>Version de logger : '.\logger\logger::version;
		echo '</div>';
		try {
			$i = 0;
			$listLog = $log->getLogs ();
			if (is_array($listLog) && !empty($listLog)) {
				echo '<table>
				<thead>
				<tr>
				<th>id</th>
				<th>Date</th>
				<th>action</th>
				<th>Infos supplémentaire</th>
				</tr>
				</thead>
				<tfoot>
				<tr><td colspan="5">&nbsp;</td></tr>
				</tfoot>';
				foreach ( $listLog as $l ) {
					echo '<tr class="';
					if ($i % 2 == 0)
						echo 'even';
					else
						echo 'odd';
					echo '">
					<td>' . $l->getId () . '</td>
					<td>' . $l->getDateLog () . '</td>
					<td>' . $l->getSeverite () . '</td>
					<td>' . $l->getMessage () . '</td>
					</tr>';
					$i ++;
				}
				echo '</table>';
			}
			else {
				echo '<div class="avertissement">Pas de log</div>';
			}
		} catch ( Exception $e ) {
			echo '<div class="avertissement erreur">' . nl2br ( $e->getMessage () ) . '<br />' . nl2br ( $e->getTraceAsString () ) . '</div>';
		}
		?>
		</pre>
	</div>
</body>
</html>
