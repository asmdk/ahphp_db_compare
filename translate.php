<?php

	require 'vendor/autoload.php';
	
	$starttime = microtime(1);
	echo 'begin', "\r\n";
	
	$yandexApiKey = 'trnsl.1.1.20150706T200912Z.551f7daf3c5b3618.4ba2250ea6a0f212eaa3aa351c817c53274baef8';
	$translateUrl = "https://translate.yandex.net/api/v1.5/tr.json/translate?key={$yandexApiKey}&lang=ru&text=";
	$words = [
		'white',
		'green',
		'yellow',
		'bear',
		'tiger',
		'wolf',
		'table',
		'door',
		'chair',
		'car',
		'bus',
		'plane',
	];
	
	$dsn = 'mysql:dbname=injection;host=127.0.0.1';
	$user = 'root';
	$password = 'root';

	try {
		$dbh = new PDO($dsn, $user, $password);
		
		$price = 0;
		$wordsCount = count($words);
		for ($i = 0; $i < 100; $i++) {
			$orName = implode(' ', [
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
			]);
			$text = send($translateUrl . urlencode($orName));
			$sth = $dbh->prepare("INSERT records SET name = :name, text = :text, price = :price, status = :status, timestamp = :timestamp, date = :date");
			$fields = ['name' => 'This is a long name!', 'text' => $text, 'price' => $price++, 'status' => rand(1, 10), 'timestamp' => date('Y-m-d h:i:s'), 'date' => date('Y-m-d h:i:s')];
			$sth->execute($fields);
		}
		
		$dbh=null;
		
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
	
	echo 'end', "\r\n";
	echo microtime(1) - $starttime;
	
	function send($url) 
	{
		usleep(250000);
		$request = json_decode(file_get_contents($url));		
		return current($request->text);
	}