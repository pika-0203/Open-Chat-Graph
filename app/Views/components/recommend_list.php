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
            min-width: 110px;
            max-width: 110px;
            margin: 0;
            padding: 0;
            padding: 13px 3px;
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

        .recommend-list a {
            cursor: pointer;
            text-decoration: none;
            color: #111;
            user-select: none;
            display: flex;
            flex-direction: column;
            /* align-items: center; */
            height: 180px;
            justify-content: space-evenly;
        }

        .recommend-list img {
            display: block;
            width: 100%;
            aspect-ratio: 1;
            border-radius: 22.5%;
            object-fit: cover;
        }

        .css-162gv95 {
            user-select: none;
            width: 1em;
            height: 1em;
            display: inline-block;
            fill: currentcolor;
            flex-shrink: 0;
            color: rgb(7, 181, 59);
            font-size: 12px;
            margin: -1px -3px;
        }

        @media screen and (min-width: 512px) {
            .recommend-list {
                overflow-x: unset;
                flex-wrap: wrap;
                justify-content: space-evenly;
            }

            .recommend-list li {
                padding: 8px 3px;
            }

            .recommend-list li:nth-child(n+11) {
                display: none;
            }

            .recommend-list.show-all li:nth-child(n+11) {
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

            .recommend-list a {
                height: 174px;
            }

            .recommend-list img {
                width: 100px;
                margin: 0 auto;
            }
        }
    </style>
    <div class="btn-wrapper">
        <div style="display: flex; flex-direction: row; /* align-items: center; */ margin-left: 1rem;">
            <div aria-hidden="true" style="font-size: 12px; user-select: none;">🎖</div>
            <h3 style="all: unset;
                font-weight: bold;
                font-size: 13px;
                color: #777;
                display: flex;
                align-items: center;
                gap: 3px;
            "><?php echo $category ?>のおすすめ<small style="font-size: 11px; font-weight:normal; color:#b7b7b7;">最新</small></h3>
        </div>
        <button type="button" class="read-more-list-btn" onclick="this.textContent = this.parentElement.nextElementSibling.classList.toggle('show-all') ? '一部を表示' : 'もっと見る';">もっと見る</button>
    </div>
    <ul class="recommend-list">
        <?php foreach ($recommend as $roc) : ?>
            <li>
                <a href="<?php echo url('/oc/' . $roc['id']) . ($roc['table_name'] === 'statistics_ranking_hour' ? '?limit=hour' : '') ?>">
                    <img loading="lazy" alt="<?php echo $roc['name'] ?>" src="<?php echo imgUrl($roc['id'], $roc['img_url']) ?>" />
                    <h4 style=" 
                        font-size: 12px;
                        display: -webkit-box;
                        -webkit-box-orient: vertical;
                        -webkit-line-clamp: 3;
                        overflow: hidden;
                        word-break: break-all;
                        margin: 0;
                        margin-top: 6px;
                        font-weight: bold;
                        /* text-align: center; */
                        line-height: 125%;
                        overflow-wrap: anywhere;
                    ">
                        <?php echo $roc['name'] ?>
                    </h4>
                    <div style="font-size: 11px; color: #777; line-height: 125%; margin-top: 3px; white-space: nowrap;">
                        <span>
                            <?php if ($roc['member'] === $max) : ?>
                                <span aria-hidden="true" style="margin: 0 -2px; font-size: 9px; user-select: none;">🏆</span>
                                <span style="font-weight: bold;">メンバー <?php echo formatMember($roc['member']) ?>人</span>
                            <?php else : ?>
                                <span>メンバー <?php echo formatMember($roc['member']) ?>人</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === 'statistics_ranking_hour') : ?>
                                <span aria-hidden="true" style="margin: 0 -2px; font-size: 8px; user-select: none;">🔥</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === 'statistics_ranking_day') : ?>
                                <span aria-hidden="true" style="margin: 0 -2px; font-size: 9px; user-select: none;">🚀</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === 'statistics_ranking_week') : ?>
                                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                    <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                </svg>
                            <?php endif ?>
                        </span>
                    </div>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>