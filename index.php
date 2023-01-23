<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/_config.php';

define('EXT_MIME_MAP', [
	'jpg' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'tif' => 'image/tiff',
	'tiff' => 'image/tiff',
    'png' => 'image/png',
	'gif' => 'image/gif',
	'webp' => 'image/webp',
]);

$klein = new \Klein\Klein();

$klein->respond('GET', '/users/[i:uid]', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/illusts.php', ['filter' => ['user.id' => intval($request->uid)]]);
});

$klein->respond('GET', '/artworks', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/illusts.php', ['filter' => []]);
});

$klein->respond('GET', '/artworks/[i:id]', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/illust.php', ['id' => $request->id]);
});

$klein->respond('GET', '/tags/[:tagname]/artworks', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/illusts.php', ['filter' => ['tags.name' => $request->tagname]]);
});

$klein->respond('GET', '/tags', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/tags.php', []);
});

$klein->respond('GET', '/viewer', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/viewer-artworks.php', ['ep' => 'index.json']);
});

$klein->respond('GET', '/viewer/artworks', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/viewer-artworks.php', ['ep' => 'artwork.json']);
});

$klein->respond('GET', '/viewer/users/[i:uid]', function ($request, $response, $service) {
    $service->render(__DIR__ . '/page/viewer-artworks.php', ['ep' => "users/$request->uid.json"]);
});

require_once __DIR__ . '/iiif/metadata.php';
require_once __DIR__ . '/iiif/artwork-collection.php';

$klein->respond('GET', '/iiif/[:identifier]/[:region]/[:size]/[:rotation]/[:quality].[:format]', function ($request, $response, $service) {
    $id = $request->identifier;
    $idn = explode('_', $id)[0];
    $file = ARCHIVEDIR . "/img/$idn/" . $id;

    switch(true) {
        case is_file($file . '.jpg'):
            $file .= '.jpg';
            break;

        case is_file($file . '.png'):
            $file .= '.png';
            break;

        default:
            $response->code(404);
            return;
    }

    $factory = new \Conlect\ImageIIIF\ImageFactory;

    $file = $factory()->load($file)
        ->withParameters($request->params())
        ->stream();

    $response->header('Content-Type', EXT_MIME_MAP[$request->format]);
    $response->header('Access-Control-Allow-Origin', '*');

    return $file;
});

$klein->respond('GET', '/iiif/[:identifier]/info.json', function ($request, $response, $service) {
    $id = $request->identifier;
    $idn = explode('_', $id)[0];
    $file = ARCHIVEDIR . "/img/$idn/" . $id;

    switch(true) {
        case is_file($file . '.jpg'):
            $file .= '.jpg';
            break;

        case is_file($file . '.png'):
            $file .= '.png';
            break;

        default:
            $response->code(404);
            return;
    }

    $factory = new \Conlect\ImageIIIF\ImageFactory ([
        'base_url' => BASEURL . '/iiif/'
    ]);

    $file = $factory()->load($file)
        ->info(null, $request->identifier);

    $response->header('Access-Control-Allow-Origin', '*');
    $file['id'] = BASEURL . '/iiif/' . $id;
    $response->json($file);
});

$klein->dispatch();