<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../_config.php';

$client = new MongoDB\Client(MONGO_URL);
$collection = $client->pixivarchive->illustmetadatas;

$next = $_GET['p'] ?? '1';
$next = intval($next) - 1;

$tags = $collection->distinct('tags.name');
$totalItemCnt = count($tags)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixiv Illust Archive Inspector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="p-3">
    <div class="row row-cols-1 row-cols-md-4 row-cols-lg-4 row-cols-xl-6 g-4">
        <?php foreach($tags as $tag) : ?>
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><a href="/tags/<?= urlencode($tag) ?>/artworks"><?= htmlentities($tag) ?></a></h5>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!--
    <div class="text-center">
        <nav aria-label="pagination">
            <ul class="pagination">
              <?php if ($next > 0) : ?>
                <li class="page-item">
                  <a class="page-link" href="?p=<?= $next ?>">Previous</a>
                </li>
              <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
              <?php endif; ?>
              <?php for ($i = 1; $i <= ($totalItemCnt / 50) + 1; $i++) : ?>
                <li class="page-item"><a class="page-link<?= $i == ($next + 1) ? ' active' : '' ?>" href="?p=<?= $i ?>"><?= $i ?></a></li>
              <?php endfor; ?>
              <li class="page-item">
                <a class="page-link" href="?p=<?= $next + 2 ?>">Next</a>
              </li>
            </ul>
        </nav>
    </div>
    -->
</body>
</html>
