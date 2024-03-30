<div style="margin: 0 -1rem;">
    <style>
        .recommend-list {
            overflow-x: scroll;
            flex-wrap: nowrap;
            display: flex;
            flex-direction: row;
            margin: 0;
            padding: 0 1rem;
        }

        .recommend-list li {
            all: unset;
            min-width: 100px;
            max-width: 100px;
            margin: 0;
            padding: 0;
            margin: 10px 3px;
        }

        .recommend-list::-webkit-scrollbar {
            display: none;
        }

        .btn-wrapper {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .read-more-list-btn {
            display: none;
        }

        @media screen and (min-width: 512px) {
            .recommend-list {
                overflow-x: unset;
                flex-wrap: wrap;
                justify-content: space-evenly;
            }

            .recommend-list li:nth-child(n+6) {
                display: none;
            }

            .recommend-list.show-all li:nth-child(n+6) {
                display: block;
            }

            .read-more-list-btn {
                display: inline-block;
                margin: 0;
                height: 36px;
                border: 1px solid #efefef;
                border-radius: 9rem;
                background-color: #fff;
                font-size: 12px;
                cursor: pointer;
            }
        }
    </style>
    <div class="btn-wrapper">
        <h3 style="all: unset;
        font-weight: bold;
        font-size: 13px;
        color: #777;
        margin-left: 1rem;
    "><?php echo $category ?>の注目オプチャ</h3>
        <button type="button" class="read-more-list-btn" onclick="this.textContent = this.parentElement.nextElementSibling.classList.toggle('show-all') ? '一部を表示' : 'もっと見る';">もっと見る</button>
    </div>
    <ul class="recommend-list">
        <?php foreach ($recommend as $roc) : ?>
            <li>
                <a style="cursor: pointer;
                        text-decoration: none;
                        color: #111;
                        user-select: none;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    " href="<?php echo url('/oc/' . $roc['id']) ?>">
                    <img style="display: block; width: 50px; height: 50px; border-radius: 50%;" loading="lazy" alt="オープンチャット「<?php echo $roc['name'] ?>」のアイコン" src="<?php echo imgPreviewUrl($roc['id'], $roc['img_url']) ?>" />
                    <h4 style=" 
                font-size: 11px;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
                overflow: hidden;
                word-break: break-all;
                margin: 4px 0;
                text-align: center;
                line-height: 125%;
              ">
                        <?php echo $roc['name'] ?>
                    </h4>
                    <div style="font-size: 11px; color: #aaa">
                        <span>メンバー <?php echo $roc['member'] ?></span>
                    </div>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>