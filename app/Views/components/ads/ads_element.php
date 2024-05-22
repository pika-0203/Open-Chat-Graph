<ins class="kokoku-element">
    <article class="kokoku-box">
        <header class="kokoku-header">
            <a class="kokoku-info-button" aria-label="広告について" href="<?php echo url('policy/ads') ?>">
                <span class="kokoku-info-button-info-svg"></span>
            </a>
            <a class="kokoku-anchor-box ads_href" href="<?php /** @var App\Views\Dto\AdsDto $dto */ echo $dto->ads_href ?>" rel="nofollow">
                <div class="kokoku-img-box">
                    <img loading="lazy" class="kokoku-img ads_img_url" src="<?php echo $dto->ads_img_url ?>" loading="lazy" alt="<?php echo $dto->ads_sponsor_name ?>" />
                </div>
                <div class="kokoku-title-box">
                    <h3 class="kokoku-title ads_title"><?php echo $dto->ads_title ?></h3>
                    <span class="kokoku-title-button ads_title_button"><?php echo $dto->ads_title_button ?></span>
                </div>
            </a>
        </header>
        <p class="kokoku-paragraph ads_paragraph">
            <?php echo $dto->ads_paragraph ?>
        </p>
        <footer class="kokoku-box-footer">
            <span class="kokoku-pr-icon">広告</span>
            <span class="kokoku-sponsor-name ads_sponsor_name"> <?php echo $dto->ads_sponsor_name ?></span>
        </footer>
    </article>
    <?php if ($dto->ads_tracking_url) : ?>
        <img class="kokoku-tracking-img" src="<?php echo $dto->ads_tracking_url ?>" alt="">
    <?php endif ?>
</ins>