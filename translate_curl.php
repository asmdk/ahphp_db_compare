<?php

	require 'vendor/autoload.php';
	
	$starttime = microtime(1);
	echo 'begin', "\r\n";
	
	$yandexApiKey = '<api_code>';
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
		$multiCurl = [];
		$result = [];
		$mh = curl_multi_init();
		
		for ($i = 0; $i < 100; $i++) {
			$orName = implode(' ', [
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
				$words[rand(0, $wordsCount)],
			]);
			
			$multiCurl[$i] = getCurl($translateUrl . urlencode($orName));
			curl_multi_add_handle($mh, $multiCurl[$i]);
		}
		
		$index=null;
		do {
		  curl_multi_exec($mh,$index);
		} while($index > 0);
		
		foreach($multiCurl as $k => $ch) {
			$result = json_decode(curl_multi_getcontent($ch));
			$sth = $dbh->prepare("INSERT records SET name = :name, text = :text, price = :price, status = :status, timestamp = :timestamp, date = :date");
			$fields = ['name' => 'This is a long name!', 'text' => current($result->text), 'price' => $price++, 'status' => rand(1, 10), 'timestamp' => date('Y-m-d h:i:s'), 'date' => date('Y-m-d h:i:s')];
			$sth->execute($fields);
			curl_multi_remove_handle($mh, $ch);
		}
		curl_multi_close($mh);
		
		$dbh=null;
		
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
	
	echo 'end', "\r\n";
	echo microtime(1) - $starttime;
	
	function getCurl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		return $ch;
	}