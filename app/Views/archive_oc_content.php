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
            <div class="overlay-link-box unset">
                <div class="talkroom_banner_img_area unset">
                    <img class=" talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgUrlLocal($oc['id'], $oc['img_url']) ?>">
                </div>
                <a href="<?php echo url('/oc/' . $oc['id']) ?>">
                    <h1 class="talkroom_link_h1 unset" <?php aliveStyleColor($oc) ?>><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></span></h1>
                </a>
            </div>
            <div class="talkroom_number_of_members">
                <span><?php echo convertDatetimeAndOneDayBefore($oc['archived_at']) ?> 時点</span><span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?></span>
            </div>
            <div class="talkroom_description_box">
                <?php if ($oc['update_description']) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>変更前</th>
                                <th>変更後<br><small style="color: white;"><?php echo convertDatetime($oc['archived_at']) ?> 以降</small></th>
                                <th>差分</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="a"><?php echo $oc['description'] ?></td>
                                <td id="b"><?php echo $updated['description'] ?></td>
                                <td id="result-td">
                                    <pre id="result"><del>rest</del>aura<del>nt</del></pre>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p id="talkroom-description" class="talkroom_description"><?php echo nl2brReplace($oc['description']) ?></p>
                <?php endif ?>
            </div>
        </header>
        <aside style="margin-top: 1rem;">
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
                            echo ', ';
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
    <?php if ($oc['update_description']) : ?>
        <script src="<?php echo fileurl("/js/jsdiff.js") ?>"></script>
        <script defer="">
            var a = document.getElementById('a');
            var b = document.getElementById('b');
            var result = document.getElementById('result');
            window.diffType = 'diffChars'

            function changed() {
                var diff = JsDiff[window.diffType](a.textContent, b.textContent);
                var fragment = document.createDocumentFragment();
                for (var i = 0; i < diff.length; i++) {

                    if (diff[i].added && diff[i + 1] && diff[i + 1].removed) {
                        var swap = diff[i];
                        diff[i] = diff[i + 1];
                        diff[i + 1] = swap;
                    }

                    var node;
                    if (diff[i].removed) {
                        node = document.createElement('del');
                        node.appendChild(document.createTextNode(diff[i].value));
                    } else if (diff[i].added) {
                        node = document.createElement('ins');
                        node.appendChild(document.createTextNode(diff[i].value));
                    } else {
                        node = document.createTextNode(diff[i].value);
                    }
                    fragment.appendChild(node);
                }

                result.textContent = '';
                result.appendChild(fragment);
            }

            window.onload = function() {
                changed();
            };

            a.onpaste = a.onchange =
                b.onpaste = b.onchange = changed;

            if ('oninput' in a) {
                a.oninput = b.oninput = changed;
            } else {
                a.onkeyup = b.onkeyup = changed;
            }
        </script>
    <?php endif ?>
</body>

</html>