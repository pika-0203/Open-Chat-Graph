<!-- @param array $openChatList -->
<!-- @param bool $isHourly -->
<ol class="openchat-item-list unset">
  <?php foreach ($openChatList as $oc) : ?>
    <li class="openchat-item unset">
      <a class="link-overlay unset" href="<?php echo  $oc['id'] ? url('/oc/' . $oc['id']) : url('policy#comments') ?>" tabindex="-1" aria-hidden="true">
        <span class="visually-hidden"><?php echo $oc['name'] ?></span>
      </a>
      <img alt="<?php echo $oc['name'] ?>" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['id'], $oc['img_url']) ?>">
      <h3 class="unset">
        <a class="openchat-item-title unset" href="<?php echo  $oc['id'] ? url('/oc/' . $oc['id']) : url('policy#comments') ?>">
          <div class="comment-user">
            <span><?php echo $oc['user'] ?></span>
          </div>
          <div class="comment-name">
            <span aria-hidden="true">@</span><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><span><?php echo $oc['name'] ?></span>
          </div>
          <div class="comment-time">
            <span><?php echo timeElapsedString($oc['time']) ?></span>
          </div>
        </a>
      </h3>
      <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
    </li>
  <?php endforeach ?>
</ol>