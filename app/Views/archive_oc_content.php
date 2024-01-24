<!DOCTYPE html>
<html lang="jp">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo fileUrl("style/mvp.css") ?>">
  <link rel="stylesheet" href="<?php echo fileurl("style/site_header.css") ?>">
  <link rel="stylesheet" href="<?php echo fileurl("style/site_footer.css") ?>">
  <link rel="stylesheet" href="<?php echo fileurl("style/room_page.css") ?>">
  <link rel="stylesheet" href="<?php echo fileurl("style/jsdiff_style.css") ?>">
  <meta name="robots" content="noindex" />
  <title><?php echo $oc['name'] ?></title>
</head>

<body>
  <style>
    .overlay-link-box {
      cursor: auto;
    }

    .talkroom_link_h1 {
      display: block;
    }

    .talkroom_banner_img_area img {
      pointer-events: all;
    }

    .talkroom_banner_img_area {
      margin: 0 10rem;
      min-height: 40svh;
    }

    .overlay-link-box:hover .talkroom_link_h1 {
      -webkit-text-decoration: none;
      text-decoration: none;
    }

    .overlay-link-box a:hover {
      filter: brightness(1);
    }

    @media screen and (max-width: 652px) {
      .talkroom_banner_img_area {
        margin: 0 25vw;
      }
    }

    @media screen and (min-width: 512px) {
      .talkroom_banner_img_area {
        min-height: 40svh;
      }
    }

    @media screen and (min-height: 1024px) {
      .talkroom_banner_img_area {
        min-height: 40svh;
        margin: 0 3rem;
      }
    }
  </style>
  <!-- 固定ヘッダー -->
  <?php viewComponent('site_header') ?>
  <!-- オープンチャット表示ヘッダー -->
  <article class="openchat unset">
    <header class="openchat-header unset" id="openchat-header">
      <?php if ($oc['update_img']) : ?>
        <div class="talkroom_description_box">
          <table>
            <thead>
              <tr>
                <th><?php echo convertDatetime($oc['updated_at']) ?> 時点</th>
                <th><?php echo convertDatetime($oc['archived_at']) ?> 以降</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><img src="<?php echo imgUrlLocal($oc['id'], $oc['img_url']) ?>" alt="オープンチャット「<?php echo $oc['name'] ?>」の旧メイン画像"></td>
                <td><img src="<?php echo imgUrlLocal($oc['id'], $updated['img_url']) ?>" alt="オープンチャット「<?php echo $oc['name'] ?>」の新メイン画像"></td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endif ?>
      <div class="overlay-link-box unset">
        <?php if (!$oc['update_img']) : ?>
          <div class="talkroom_banner_img_area unset">
            <img class=" talkroom_banner_img" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgUrlLocal($oc['id'], $oc['img_url']) ?>">
          </div>
        <?php endif ?>
        <a href="<?php echo url('/oc/' . $oc['id']) ?>">
          <h1 class="talkroom_link_h1 unset" <?php aliveStyleColor($oc) ?>><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></span></h1>
        </a>
      </div>
      <div class="talkroom_number_of_members">
        <span><?php echo convertDatetime($oc['updated_at']) ?> 時点</span><span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?></span>
      </div>
      <?php if ($oc['update_name']) : ?>
        <div class="talkroom_description_box">
          <div class="graph-title">
            <h2>タイトル</h2>
          </div>
          <table>
            <thead>
              <tr>
                <th><?php echo convertDatetime($oc['updated_at']) ?> 時点</th>
                <th><?php echo convertDatetime($oc['archived_at']) ?> 以降</th>
                <th>差分</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td id="name-a"><?php echo $oc['name'] ?></td>
                <td id="name-b"><?php echo $updated['name'] ?></td>
                <td class="result-td">
                  <pre class="result" id="name-result"><del>rest</del>aura<del>nt</del></pre>
                </td>
              </tr>
          </table>
        </div>
      <?php endif ?>
      <?php if (!$oc['update_description']) : ?>
        <p id="talkroom-description" class="talkroom_description" style="margin:0.5rem 0;"><?php echo nl2brReplace($oc['description']) ?></p>
      <?php endif ?>
    </header>
    <div class="openchat-header unset">
      <?php if ($oc['update_description']) : ?>
        <div class="talkroom_description_box">
          <div class="graph-title">
            <h2>説明文</h2>
          </div>
          <table>
            <thead>
              <tr>
                <th><?php echo convertDatetime($oc['updated_at']) ?> 時点</th>
                <th><?php echo convertDatetime($oc['archived_at']) ?> 以降</th>
                <th>差分</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td id="desc-a"><?php echo $oc['description'] ?></td>
                <td id="desc-b"><?php echo $updated['description'] ?></td>
                <td class="result-td">
                  <pre class="result" id="desc-result"><del>rest</del>aura<del>nt</del></pre>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endif ?>
    </div>
    <aside style="margin: 1rem 0 0.5rem 0;">
      <small>
        <span><?php echo convertDatetime($oc['archived_at']) ?> 以降の変更箇所: </span>
        <span>
          <?php
          $elements = [];

          if ($oc['update_name']) $elements[] = 'オープンチャット名';
          if ($oc['update_description']) $elements[] = '説明文';
          if ($oc['update_img']) $elements[] = '画像';

          foreach ($elements as $index => $text) {
            if (!$text) {
              continue;
            }
            echo $text;
            if ($elements[$index + 1] ?? false) {
              echo '、';
            }
          }
          ?>
        </span>
      </small>
    </aside>
  </article>
  <footer>
    <?php viewComponent('footer_inner') ?>
  </footer>
  <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
  <script src="<?php echo fileurl("/js/jsdiff.js") ?>"></script>
  <script src="<?php echo fileurl("/js/JsDiffWrap.js") ?>"></script>
  <script defer>
    function generate(key) {
      const a = document.getElementById(`${key}-a`)
      if (!a) return

      const jsdiff = new JsDiffWrap(
        a,
        document.getElementById(`${key}-b`),
        document.getElementById(`${key}-result`)
      )

      jsdiff.changed()
    }

    generate('name')
    generate('desc')
  </script>
</body>

</html>