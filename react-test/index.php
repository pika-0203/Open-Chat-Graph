<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\AppConfig;

function getFilePath($path, $pattern)
{
  $file = glob(__DIR__ . "/{$path}/{$pattern}");
  if ($file) {
    $fileName = basename($file[0]);
    return fileUrl("react-test/{$path}/{$fileName}", __DIR__ . '/..');
  } else {
    return '';
  }
}

$_css = ['react/OpenChat', 'react/OpenChatList', 'react/SiteHeader'];
$_meta = meta()
  ->setTitle('【毎日更新】参加人数のランキング')
  ->generateTags();


$_jsonData = file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH);
$_rankingInfo = unserialize(file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH));

cache();

?>

<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
  <?php echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php echo $_meta ?>
  <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
  <?php foreach ($_css as $css) : ?>
    <link rel="stylesheet" href="<?php echo fileUrl("style/{$css}.css") ?>">
  <?php endforeach ?>
  <link href="<?php echo getFilePath('static/css', 'main.*.css') ?>" rel="stylesheet" />

  <script>
    window.subCategories = <?php echo $_jsonData; ?>;
    window.rankingUpdatedAt = "<?php echo convertDatetime($_rankingInfo['rankingUpdatedAt'], true); ?>";
  </script>
  <script defer="defer" src="<?php echo getFilePath('static/js', 'main.*.js') ?>"></script>
</head>

<body style="margin: 0">
  <noscript>You need to enable JavaScript to run this app.</noscript>
  <div id="root"></div>
</body>

</html>