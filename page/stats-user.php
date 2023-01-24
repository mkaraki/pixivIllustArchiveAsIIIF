<?php
require_once __DIR__ . '/../_config.php';

$client = new MongoDB\Client(MONGO_URL);
$collection = $client->pixivarchive->illustmetadatas;

$uids = $collection->distinct('user.id');

$uidcnt = [];
$unames = [];
foreach($uids as $uid)
{
    $uidcnt[] = $collection->count(['user.id' => $uid]);
    $unames[] = $collection->findOne(['user.id' => $uid])['user']['name'];
}

array_multisort($uidcnt, SORT_DESC, SORT_NUMERIC, $unames);

$uidcnt = array_slice($uidcnt, 0, 100);
$unames = array_slice($unames, 0, 100);
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
              labels: <?= json_encode($unames) ?>,
              values: <?= json_encode($uidcnt) ?>,
              parents: <?= json_encode(array_fill(0, count($uidcnt), 'USers')) ?>,
              textinfo: "label+value",
            }];
        
        Plotly.newPlot('tree', data)
    </script>
</body>
</html>
