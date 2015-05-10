<article class="webmention webmention-reply">
  <?php echo $author ?>
  <?php if($mention->text()->isNotEmpty()): ?>
  <div class="webmention-text">
    <p><?php echo $mention->text()->excerpt(200) ?></p>
    <a class="webmention-continue" href="<?php echo $mention->url() ?>">Continue reading</a>
  </div>
  <?php endif ?>
</article>