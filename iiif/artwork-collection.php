<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../_config.php';

$klein->respond('GET', '/iiif/collections/index.json', function ($request, $response, $service) {
    $response->json([
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/index.json",
        "@type" => 'sc:Collection',
        'collections' => [
            [
                "@id" => BASEURL . "/iiif/collections/artworks.json",
                "@type" => 'sc:Collection',
                'label' => [ 'en' => [ 'All artworks' ] ],
            ],
            [
                "@id" => BASEURL . "/iiif/collections/users.json",
                "@type" => 'sc:Collection',
                'label' => [ 'en' => [ 'All users' ] ],
            ],
            [
                "@id" => BASEURL . "/iiif/collections/tags.json",
                "@type" => 'sc:Collection',
                'label' => [ 'en' => [ 'All tags' ] ],
            ],
        ],
        'label' => 'Index'
    ]);
});


$klein->respond('GET', '/iiif/collections/artworks.json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $metas = $collection->find([]);
    
    $ret = [
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/artwork.json",
        "@type" => 'sc:Collection',
        'manifests' => [],
        'label' => 'All artworks'
    ];

    foreach($metas as $meta)
    {
        $ret['manifests'][] = [
            '@id' => BASEURL . '/iiif/' . $meta['id'] . '/manifest.json',
            '@type' => 'sc:Manifest',
            'label' => $meta['title'] . ' - ' . $meta['user']['name'],
            'thumbnail' => BASEURL . '/iiif/' . $meta['id'] . '_p0/full/full/0/default.json',
        ];
    }

    $response->json($ret);
});

$klein->respond('GET', '/iiif/collections/users/[i:uid].json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $metas = $collection->find(['user.id' => intval($request->uid)]);
    $userinfo = $collection->findOne(['user.id' => intval($request->uid)])['user'];
    
    $ret = [
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/users/$request->uid.json",
        "@type" => 'sc:Collection',
        'manifests' => [],
        'label' => $userinfo['name']
    ];

    foreach($metas as $meta)
    {
        $ret['manifests'][] = [
            '@id' => BASEURL . '/iiif/' . $meta['id'] . '/manifest.json',
            '@type' => 'sc:Manifest',
            'label' => $meta['title'],
            'thumbnail' => BASEURL . '/iiif/' . $meta['id'] . '/full/full/0/default.json',
        ];
    }

    $response->json($ret);
});

$klein->respond('GET', '/iiif/collections/users.json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $uids = $collection->distinct('user.id');
    
    $ret = [
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/users.json",
        "@type" => 'sc:Collection',
        'collections' => [],
        'label' => 'All users'
    ];

    foreach($uids as $uid)
    {
        $uinfo = $collection->findOne(['user.id' => $uid])['user'];
        $ret['collections'][] = [
            "@id" => BASEURL . "/iiif/collections/users/$uid.json",
            "@type" => 'sc:Collection',
            'label' => $uinfo['name'],
        ];
    }

    $response->json($ret);
});

$klein->respond('GET', '/iiif/collections/tags.json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $tags = $collection->distinct('tags.name');
    
    $ret = [
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/tags.json",
        "@type" => 'sc:Collection',
        'collections' => [],
        'label' => 'All tags'
    ];

    foreach($tags as $tag)
    {
        $ret['collections'][] = [
            "@id" => BASEURL . "/iiif/collections/tags/" . urlencode($tag) . ".json",
            "@type" => 'sc:Collection',
            'label' => $tag,
        ];
    }

    $response->json($ret);
});


$klein->respond('GET', '/iiif/collections/tags/[:tag].json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $metas = $collection->find(['tags.name' => $request->tag]);
    
    $ret = [
        "@context" => "http://iiif.io/api/presentation/2/context.json",
        "@id" => BASEURL . "/iiif/collection/users/$request->uid.json",
        "@type" => 'sc:Collection',
        'manifests' => [],
        'label' => $request->tag
    ];

    foreach($metas as $meta)
    {
        $ret['manifests'][] = [
            '@id' => BASEURL . '/iiif/' . $meta['id'] . '/manifest.json',
            '@type' => 'sc:Manifest',
            'label' => $meta['title'],
            'thumbnail' => BASEURL . '/iiif/' . $meta['id'] . '_p0/full/full/0/default.json',
        ];
    }

    $response->json($ret);
});