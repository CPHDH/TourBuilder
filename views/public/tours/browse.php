<?php
$pageTitle = __('Browse Tours');
echo head( array( 'title' => $pageTitle, 'bodyclass' => 'tours browse' ) );
?>

<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', $total_results); ?></h1>

<nav class="tours-nav navigation secondary-nav">
  <?php echo public_nav_tours(); ?>
</nav>

<?php echo pagination_links(); ?>

<?php if( $total_results > 0 ): ?>

<?php foreach( $tours as $tour ): ?>
<?php set_current_record( 'tour', $tour ); ?>
<div class="tour hentry">
  <h2><?php echo link_to_tour(); ?></h2>

  <?php if( $description = metadata( 'tour', 'description', array( 'snippet' => 250 ) ) ): ?>
  <div class="tour-description">
    <?php echo $description; ?>
  </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php echo pagination_links(); ?>
<?php echo foot(); ?>
