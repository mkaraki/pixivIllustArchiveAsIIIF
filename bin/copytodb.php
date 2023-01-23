<?php
$dir = '/mnt/c/temp/pxmeta/rawmeta';

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../_config.php';

$client = new MongoDB\Client(MONGO_URL);
$collection = $client->pixivarchive->illustmetadatas;

$files = scandir($dir);

if ($files == false)
	die('Failed to retrive files');

foreach ($files as $file)
{
	print($file . ' -> ');

	if (!str_ends_with($file, ".json"))
	{
		print("Ignore\n");
		continue;
	}

	$readfile = file_get_contents($dir . '/' . $file);
	$jsonobj = json_decode($readfile, true);
	if (isset($jsonobj['illust']))
		$jsonobj = $jsonobj['illust'];
	else
	{
		print("Ignore format error A\n");
		continue;
	}

	if (!isset($jsonobj['id']))
	{
		print("Ignore format error B\n");
		continue;
	}

	if ($collection->count(['id' => $jsonobj['id']]) > 0)
	{
		print("Already in DB\n");
		continue;
	}

	if (isset($jsonobj['image_urls']))
		unset($jsonobj['image_urls']);
	if (isset($jsonobj['user']) && isset($jsonobj['user']['profile_image_urls']))
		unset($jsonobj['user']['profile_image_urls']);
	if (isset($jsonobj['meta_single_page']))
		unset($jsonobj['meta_single_page']);
	if (isset($jsonobj['meta_pages']))
		unset($jsonobj['meta_pages']);

	$res = $collection->insertOne($jsonobj);

	print("Ok\n");
}
