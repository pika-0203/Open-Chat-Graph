<aside style="font-size: 13px; display: block; margin:0;" class="unset">
    <details style="margin:0 0 0 0; width:100%;">
        <summary class="news-summary">
            <span>アップデート情報</span>
            <span style="color: #777; font-weight:normal; font-size:13px"><?php /** @var \App\Views\Content\UpdateNews[] $_news */ echo timeElapsedString($_news[0]->date->format('Y-m-d H:i:s')) ?></span>
        </summary>
        <div style="position:relative;">
            <div style="margin: .5rem 0 .5rem 0; max-height: 20rem; overflow-y: auto;">
                <div style="margin-bottom: 2rem;">
                    <?php foreach ($_news as $el) : ?>
                        <div style="margin-bottom: 1rem; border-bottom: 1px solid #efefef; width: 100%;">
                            <span style="color: #111; font-size: 13px; font-weight: bold"><?php echo $el->title ?></span>
                            <span style="color: #555; margin-left: 4px"><?php echo $el->date->format('Y/n/j G:i') ?></span>
                            <?php foreach ($el->body as $body) : ?>
                                <?php if (is_array($body)) : ?>
                                    <ul style="padding-left: 1rem;">
                                        <?php foreach ($body as $li) : ?>
                                            <li style="white-space: pre-line;"><?php echo $li ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                <?php else : ?>
                                    <p style="white-space: pre-line;"><?php echo $body ?></p>
                                <?php endif ?>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="gradient-bottom"></div>
        </div>
    </details>
</aside>