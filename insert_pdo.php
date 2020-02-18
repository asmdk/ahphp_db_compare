<?php

	require 'vendor/autoload.php';
	
	$starttime = microtime(1);
	echo 'begin', "\r\n";
	
	$dsn = 'mysql:dbname=injection;host=127.0.0.1';
	$user = 'root';
	$password = 'root';

	try {
		$dbh = new PDO($dsn, $user, $password);
		
		$price = 0;
		for ($i = 0; $i < 100; $i++) {
			$sth = $dbh->prepare("INSERT records SET name = :name, text = :text, price = :price, status = :status, timestamp = :timestamp, date = :date");
			$fields = ['name' => 'This is a long name!', 'text' => 'This is a long text', 'price' => $price++, 'status' => rand(1, 10), 'timestamp' => date('Y-m-d h:i:s'), 'date' => date('Y-m-d h:i:s')];
			$sth->execute($fields);
		}
		
		$dbh=null;
		
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
	
	echo 'end', "\r\n";
	echo microtime(1) - $starttime;