<figure class="webmention-author">
  <a title="<?php echo $author->name() ?>" class="webmention-author-photo" href="<?php echo $author->url() ?>">
    <?php if($author->photo()->exists()): ?>
    <img src="<?php echo $author->photo()->url() ?>" alt="<?php echo $author->name() ?>">
    <?php endif ?>
  </a>
  <figcaption class="webmention-author-meta">
    <a href="<?php echo $author->url() ?>" class="webmention-author-name"><?php echo $author->name() ?></a>
    <time class="webmention-author-date" datetime="<?php echo $mention->date('c') ?>">
      <a href="<?php echo $mention->url() ?>"><?php echo $mention->date('d M Y - h:i') ?></a>
    </time>
  </figcaption>
</figure>