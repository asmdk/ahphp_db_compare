<?php 

	use Amp\Artax\DefaultClient;
	use Amp\Loop;
	use Amp\Sync\LocalSemaphore;

	require __DIR__ . "/vendor/autoload.php";
	
	$starttime = microtime(1);
	echo 'begin', "\r\n";
	
	$config = Amp\Mysql\ConnectionConfig::fromString(
		"host=127.0.0.1 user=root password=root db=injection"
	);
	
	$db = Amp\Mysql\pool($config);

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
	
	$urls = [];
	$wordsCount = count($words);
	for ($i = 0; $i < 100; $i++) {
		$orName = implode(' ', [
			$words[rand(0, $wordsCount)],
			$words[rand(0, $wordsCount)],
			$words[rand(0, $wordsCount)],
			$words[rand(0, $wordsCount)],
			$words[rand(0, $wordsCount)],
		]);
		$urls[] = $translateUrl . urlencode($orName);
	}

	Loop::run(function () use ($urls, $db) {
		$concurrency = 10;
		$client = new DefaultClient;
		$semaphore = new LocalSemaphore(10);

		$requestHandler = Amp\coroutine(function ($url) use ($client) {

			usleep(250000);
			$response = yield $client->request($url);
			$body = yield $response->getBody();

			return current(json_decode($body)->text);
		});
		
		$sql = yield $db->prepare("INSERT records SET name = :name, text = :text, price = :price, status = :status, timestamp = :timestamp, date = :date");
		$results = [];

		foreach ($urls as $url) {
			$lock = yield $semaphore->acquire();

			$promise = $requestHandler($url);
			$promise->onResolve(function ($error, $body) use ($lock, $sql, &$results) {
				$lock->release();
				$results[] = yield $sql->execute(['name' => 'This is a long name!', 'text' => $body, 'price' => rand(1, 100), 'status' => rand(1, 10), 'timestamp' => date('Y-m-d h:i:s'), 'date' => date('Y-m-d h:i:s')]);
			});
		}
		
		foreach($results as $result) {
		}
	});
	
	$db->close();
	
	echo 'end', "\r\n";
	echo microtime(1) - $starttime;