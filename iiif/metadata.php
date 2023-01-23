<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../_config.php';

$klein->respond('GET', '/iiif/[:identifier]/manifest.json', function ($request, $response, $service) {
    $client = new MongoDB\Client(MONGO_URL);
    $collection = $client->pixivarchive->illustmetadatas;
    
    $meta = $collection->findOne(['id' => intval($request->identifier)]);
    
    $canvases = [];

    $pixivUrl = 'https://www.pixiv.net/artworks/' . $meta['id'];
    $pixivUserUrl = 'https://www.pixiv.net/users/' . $meta['user']['id'];

    for ($i = 0; $i < $meta['page_count']; $i++)
    {
        $canvases[] = [
            '@type' => 'sc:Canvas',
            '@id' => BASEURL . '/iiif/' . $request->identifier . '_p' . $i,
            'label' => strval($i + 1),
            'width' => $meta['width'],
            'height' => $meta['height'],
            'images' => [
                [
                    '@type' => 'oa:Annotationc',
                    'motivation' => 'sc:painting',
                    'on' => BASEURL . '/iiif/' . $request->identifier . '_p' . $i,
                    'resource' => [
                        '@id' => BASEURL . '/iiif/' . $request->identifier . '_p' . $i . '/full/full/0/default.jpg',
                        '@type' => 'dctypes:Image',
                        'format' => 'image/jpeg',
                        'width' => $meta['width'],
                        'height' => $meta['height'],
                        'service' => [
                            '@context' => 'http://iiif.io/api/image/2/context.json',
                            '@id' => BASEURL . '/iiif/' . $request->identifier . '_p' . $i,
                        ]
                    ]
                ]
            ],
        ];
    }

    $taghtml = '';
    foreach($meta['tags'] as $tag)
    {
        $taghtml .= '<a href="/tags/' . $tag['name'] . '/artworks">' . $tag['name'] . '</a> ';
    }

    $ret = [
        '@context' => 'http://iiif.io/api/presentation/2/context.json',
        '@type' => 'sc:Manifest',
        '@id' => BASEURL . '/iiif/' . $request->identifier . '/manifest.json',
        'label' => $meta['title'],
        'metadata' => [
            [
                'label' => [ 'en' => [ 'Persistent ID' ] ],
                'value' => [ 'none' => [ 'pixiv:artwork/' . $meta['id']] ]
            ],
            [
                'label' => [ 'en' => [ 'Title'] ],
                'value' => [ 'none' => [ $meta['title']] ]
            ],
            [
                'label' => [ 'en' => [ 'Creator' ] ],
                'value' => [ 'none' => [ '<a href="/users/' . $meta['user']['id'] . '">' . $meta['user']['name'] . '</a>' ] ],
            ],
            [
                'label' => [ 'en' => [ 'URL'] ],
                'value' => [ 'none' => [ "<a href=\"$pixivUrl\">$pixivUrl</a>" ] ],
            ],
            [
                'label' => [ 'en' => [ 'Creator URL' ] ],
                'value' => [ 'none' => [ "<a href=\"$pixivUserUrl\">$pixivUserUrl</a>" ] ],
            ],
            [
                'label' => [ 'en' => [ 'Tags'] ],
                'value' => [ 'none' => [ $taghtml ] ],
            ],
            [
                'label' => [ 'en' => [ 'Caption'] ],
                'value' => [ 'none' => [ $meta['caption']] ],
            ],
        ],
        'sequences' => [
            [
                '@type' => 'sc:Sequence',
                'canvases' => $canvases,
            ]
        ],
    ];

    $response->header('Access-Control-Allow-Origin', '*');
    $response->json($ret);
});

