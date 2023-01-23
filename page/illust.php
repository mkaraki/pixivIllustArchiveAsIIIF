<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixiv Illust Archive Inspector</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://unpkg.com/mirador@latest/dist/mirador.min.js"></script>
  <style>
  #uv {
      width: 800px;
      height: 600px;
  }
  </style>
</head>
<body class="p-3">

  <div id="view"></div>

  <script>
    const config = {
      id: 'view',
      windows: [{
        imageToolsEnabled: true,
        imageToolsOpen: true,
        manifestId: '<?= BASEURL ?>/iiif/<?= $this->id ?>/manifest.json',
      }],
      theme: {
        palette: {
          primary: {
            main: '#1967d2',
          },
        },
      },
    };

    Mirador.viewer(config, [
      //...miradorImageToolsPlugin,
    ]);
  </script>

</body>
</html>