<?php
$tabs = array();
$theTabs= (!$tour->id) ? array( 'Tour Info' ) : array( 'Tour Info','Items' );
foreach( $theTabs as $tabName )
{
   ob_start();
   switch( $tabName )
   {
   case 'Tour Info':
      require 'metadata-form.php';
      break;
   case 'Items':
      require 'items-form.php';
      break;
   }
   $tabs[$tabName] = ob_get_contents();
   ob_end_clean();
}
?>

<!-- Create the sections for the various element sets -->
<ul id="section-nav" class="navigation tabs">
  <?php
    foreach ($tabs as $tabName => $tabContent)
    {
       // Hide tabs with no content
       if (!empty($tabContent))
       {
          $tabId = html_escape( text_to_id( $tabName ) . '-metadata' );
  ?>
  <li>
    <a href="#<?php echo $tabId; ?>">
      <?php echo html_escape($tabName); ?>
    </a>
  </li>
  <?php
       }
   }
  ?>
</ul>
