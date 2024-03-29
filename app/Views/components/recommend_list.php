<div style="padding-top: 2rem">
    <style>
        .recommend-list {
            overflow-x: scroll;
            flex-wrap: nowrap;
        }

        @media screen and (min-width: 512px) {
            .recommend-list {
                overflow-x: unset;
                flex-wrap: wrap;
            }
        }
    </style>
    <h2 style="font-size: 13px; color: #777">おすすめの人気オプチャ・<?php echo $category ?></h2>
    <ol style="display: flex; flex-direction: row; padding: 0; margin: 0" class="recommend-list">
        <?php foreach ($recomend as $roc) : ?>
            <ul style="width: 100px; padding: 0; margin: 0; padding: 10px 3px">
                <a style="cursor: pointer; text-decoration: none; color: #111; user-select: none" href="<?php echo url('/oc/' . $roc['id']) ?>">
                    <img style="display: block; width: 92px; border-radius: 22.5%;" loading="lazy" alt="オープンチャット「<?php echo $roc['name'] ?>」のアイコン" src="<?php echo imgPreviewUrl($roc['id'], $roc['img_url']) ?>" />
                    <h3 style="
                font-size: 11px;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 4;
                overflow: hidden;
                word-break: break-all;
                margin: 4px 0;
              ">
                        <?php echo $roc['name'] ?>
                    </h3>
                    <div style="font-size: 11px; color: #aaa">
                        <span>メンバー <?php echo $roc['member'] ?></span>
                    </div>
                </a>
            </ul>
        <?php endforeach ?>
    </ol>