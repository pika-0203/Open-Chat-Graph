<ins class="ads-element">
    <article class="ads-box">
        <header class="ads-header">
            <a class="ads-info-button" aria-label="広告について" href="<?php echo url('policy/ads') ?>">
                <span class="ads-info-button-info-svg"></span>
            </a>
            <a class="ads-anchor-box" href="<?php /** @var App\Views\Dto\AdsDto $dto */ echo $dto->ads_href ?>">
                <div class="ads-img-box">
                    <img class="ads-img" src="<?php echo $dto->ads_img_url ?>" loading="lazy" />
                </div>
                <div class="ads-title-box">
                    <h3 class="ads-title"><?php echo $dto->ads_title ?></h3>
                    <span class="ads-title-button"><?php echo $dto->ads_title_button ?></span>
                </div>
            </a>
        </header>
        <p class="ads-paragraph">
            <?php echo $dto->ads_paragraph ?>
        </p>
        <footer class="ads-box-footer">
            <span class="ads-pr-icon">広告</span>
            <span class="ads-sponsor-name"> <?php echo $dto->ads_sponsor_name ?></span>
        </footer>
    </article>
</ins>