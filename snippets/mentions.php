<div class="webmentions">

  <?php if($likes->count()): ?>
  <section class="webmentions-likes">
    <h1><?php echo $likes->count() ?> likes</h1>
    <?php foreach($likes as $like): ?>
    <?php echo $like ?>
    <?php endforeach ?>
  </section>
  <?php endif ?>

  <?php if($replies->count()): ?>
  <section class="webmentions-replies">

    <h1>Responses</h1>
    <?php foreach($replies as $reply): ?> 
    <?php echo $reply ?>
    <?php endforeach ?>

  </section>
  <?php endif ?>

  <?php if($mentions->count()): ?>
  <section class="webmentions-mentions">

    <h1>Mentions</h1>
    <?php foreach($mentions as $mention): ?> 
    <?php echo $mention ?>
    <?php endforeach ?>

  </section>
  <?php endif ?>

</div>