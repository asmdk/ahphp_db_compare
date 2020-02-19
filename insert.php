<?php

	require 'vendor/autoload.php';
	
	$starttime = microtime(1);
	echo 'begin', "\r\n";
	
	$config = Amp\Mysql\ConnectionConfig::fromString(
		"host=127.0.0.1 user=root password=root db=injection"
	);
	
	$db = Amp\Mysql\pool($config);
		
	Amp\Loop::run(function () use ($db) {
		
		$statement = yield $db->prepare("INSERT records SET name = :name, text = :text, price = :price, status = :status, timestamp = :timestamp, date = :date");
		$promises = [];
		
		$price = 0;
		for ($i = 0; $i < 100; $i++)  {
			$promises[] = $statement->execute(['name' => 'This is a long name!', 'text' => 'This is a long text', 'price' => $price++, 'status' => rand(1, 10), 'timestamp' => date('Y-m-d h:i:s'), 'date' => date('Y-m-d h:i:s')]);
		}
		
		yield $promises;
		
	});
	
	$db->close();
	
	echo 'end', "\r\n";
	echo microtime(1) - $starttime;