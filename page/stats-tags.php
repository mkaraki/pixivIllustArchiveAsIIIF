<?php
require_once __DIR__ . '/../_config.php';

$client = new MongoDB\Client(MONGO_URL);
$collection = $client->pixivarchive->illustmetadatas;

$tags = $collection->distinct('tags.name');

$tagist = [];
foreach($tags as $tag)
    $tagist[] = $collection->count(['tags.name' => $tag]);

array_multisort($tagist, SORT_DESC, SORT_NUMERIC, $tags);

$tags = array_slice($tags, 0, 100);
$tagist = array_slice($tagist, 0, 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive Stats - Pixiv Illust Archive Inspector</title>
    <script src="https://cdn.plot.ly/plotly-2.18.0.min.js"></script>
</head>
<body style="margin: 0;">
    <div id="tree" style="width:100vw;height:100vh;"></div>
    <script>
        var data = [{
              type: "treemap",
              labels: <?= json_encode($tags) ?>,
              values: <?= json_encode($tagist) ?>,
              parents: <?= json_encode(array_fill(0, count($tags), 'Tags')) ?>,
              textinfo: "label+value",
            }];
        
        Plotly.newPlot('tree', data)
    </script>
</body>
</html>
