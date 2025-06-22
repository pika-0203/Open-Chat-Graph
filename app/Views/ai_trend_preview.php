<!DOCTYPE html>
<html lang="<?php echo t('ja') ?>">
<?php

use App\Config\AppConfig;
use App\Views\Ads\GoogleAdsence as GAd;
use Shared\MimimalCmsConfig;

viewComponent('head', compact('_css', '_meta', '_schema') + ['dataOverlays' => 'bottom', 'disableGAd' => true]) ?>
<body class="top-page">
    <?php viewComponent('site_header', compact('_updatedAt')) ?>

    <div class="pad-side-top-ranking body" style="overflow: hidden; padding-top: 0;">
        <section class="top-ranking top-btns">
            <a style="margin: 0;" class="top-ranking-readMore unset ranking-url white-btn" href="<?php echo url('') ?>">
                <span class="ranking-readMore">← トップページに戻る</span>
            </a>
        </section>
        <hr style="margin: 1rem 0;">
        <div class="modify-top-padding">
            <!-- AIトレンド分析セクション -->
            <article class="top-ranking">
                <header class="openchat-list-title-area unset">
                    <div class="openchat-list-date unset ranking-url">
                        <h1 class="unset">
                            <span class="openchat-list-title">AIトレンド分析</span>
                        </h1>
                        <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">
                            リアルタイムデータから見る今のトレンド
                        </span>
                    </div>
                </header>
                <div class="openchat-header unset" style="padding: 10px 0;">
                    <div class="talkroom_banner_img_area">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 7px; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                            🤖
                        </div>
                    </div>
                    
                    <div class="openchat-header-right">
                        <div class="talkroom_description_box">
                            <p class="talkroom_description">
                                <?php echo htmlspecialchars($aiAnalysis['summary']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </article>
            <hr style="margin: 1rem 0;">
        </div>

        <!-- 統計情報 -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">📊 リアルタイム統計</span>
                    </h2>
                </div>
            </header>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 1rem;">
                <div style="background: white; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e9ecef;">
                    <div style="font-size: 24px; font-weight: 700; color: #111; margin-bottom: 5px;"><?php echo number_format($overallStats['total_chats']) ?></div>
                    <div style="color: #777; font-size: 13px;">総チャット数</div>
                </div>
                <div style="background: white; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e9ecef;">
                    <div style="font-size: 24px; font-weight: 700; color: #111; margin-bottom: 5px;"><?php echo number_format($overallStats['total_members']) ?></div>
                    <div style="color: #777; font-size: 13px;">総メンバー数</div>
                </div>
                <div style="background: white; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e9ecef;">
                    <div style="font-size: 24px; font-weight: 700; color: #28a745; margin-bottom: 5px;">+<?php echo number_format($overallStats['total_growth']) ?></div>
                    <div style="color: #777; font-size: 13px;">1時間の増加数</div>
                </div>
                <div style="background: white; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e9ecef;">
                    <div style="font-size: 24px; font-weight: 700; color: #111; margin-bottom: 5px;"><?php echo number_format($overallStats['growing_chats']) ?></div>
                    <div style="color: #777; font-size: 13px;">成長中のチャット</div>
                </div>
            </div>
        </article>

        <hr style="margin: 1rem 0;">

        <!-- AIインサイト -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">💡 AIインサイト</span>
                    </h2>
                </div>
            </header>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-top: 1rem;">
                <?php foreach ($aiAnalysis['insights'] as $insight): ?>
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <div style="font-size: 24px; margin-right: 10px;"><?php echo $insight['icon'] ?></div>
                        <div style="font-size: 16px; font-weight: 600; color: #111;"><?php echo htmlspecialchars($insight['title']) ?></div>
                    </div>
                    <div style="color: #666; line-height: 1.5; font-size: 13px;">
                        <?php echo htmlspecialchars($insight['content']) ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </article>

        <hr style="margin: 1rem 0;">

        <!-- 急上昇チャット -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">🚀 急上昇中のオープンチャット</span>
                    </h2>
                </div>
            </header>
            <div style="margin-top: 1rem;">
                <?php foreach (array_slice($risingChats, 0, 5) as $index => $chat): ?>
                <div style="display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #e9ecef;">
                    <img src="<?php echo htmlspecialchars($chat['img_url']) ?>" alt="" style="width: 50px; height: 50px; border-radius: 7px; margin-right: 12px; object-fit: cover;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; color: #111; margin-bottom: 2px; font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($chat['name']) ?>
                        </div>
                        <div style="color: #777; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($chat['description']) ?>
                        </div>
                    </div>
                    <div style="text-align: right; margin-left: 10px;">
                        <div style="font-size: 18px; font-weight: 700; color: #28a745;">+<?php echo number_format($chat['diff_member']) ?></div>
                        <div style="font-size: 11px; color: #777;">1時間で増加</div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </article>

        <hr style="margin: 1rem 0;">

        <!-- カテゴリトレンド -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">📊 カテゴリ別トレンド</span>
                    </h2>
                </div>
            </header>
            <div style="margin-top: 1rem;">
                <?php foreach (array_slice($categoryTrends, 0, 5) as $trend): ?>
                <div style="display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #e9ecef;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #111; margin-bottom: 2px; font-size: 14px;">
                            <?php echo htmlspecialchars($trend['category_name']) ?>
                        </div>
                        <div style="color: #777; font-size: 12px;">
                            <?php echo number_format($trend['chat_count']) ?>個のチャット
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 18px; font-weight: 700; color: <?php echo $trend['total_growth'] > 0 ? '#28a745' : '#dc3545' ?>;">
                            <?php echo $trend['total_growth'] > 0 ? '+' : '' ?><?php echo number_format($trend['total_growth']) ?>
                        </div>
                        <div style="font-size: 11px; color: #777;">1時間の合計増減</div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </article>

        <hr style="margin: 1rem 0;">

        <!-- トレンドタグ -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">🏷️ 注目のタグ</span>
                    </h2>
                </div>
            </header>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 1rem;">
                <?php foreach (array_slice($tagTrends, 0, 20) as $tag): ?>
                <div style="background: #f8f9fa; padding: 6px 12px; border-radius: 20px; font-size: 12px; color: #495057; border: 1px solid #e9ecef;">
                    <?php echo htmlspecialchars($tag['tag']) ?> 
                    <span style="color: #28a745; font-weight: 600;">+<?php echo number_format($tag['total_1h_growth']) ?></span>
                </div>
                <?php endforeach ?>
            </div>
        </article>

        <?php if (!empty($aiAnalysis['predictions'])): ?>
        <hr style="margin: 1rem 0;">
        
        <!-- AI予測 -->
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">🔮 今後の予測</span>
                    </h2>
                </div>
            </header>
            <div style="margin-top: 1rem;">
                <?php foreach ($aiAnalysis['predictions'] as $prediction): ?>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #667eea;">
                    <div style="color: #495057; line-height: 1.5; font-size: 13px;">
                        <?php echo htmlspecialchars($prediction['content']) ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </article>
        <?php endif ?>

        <?php viewComponent('footer_inner') ?>

        <div class="refresh-time" style="width: fit-content; margin: auto; padding-bottom: 0.5rem; margin-top: -9px;">
            <div class="refresh-icon"></div><time style="font-size: 11px; color: #b7b7b7; margin-left:3px" datetime="<?php echo $_updatedAt->format(\DateTime::ATOM) ?>"><?php echo $_updatedAt->format('Y/n/j G:i') ?></time>
        </div>
    </div>
    
    <script defer src="<?php echo fileUrl("/js/site_header_footer.js", urlRoot: '') ?>"></script>
</body>

</html>