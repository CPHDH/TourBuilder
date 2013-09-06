<?php
head( array( 'title' => 'Browse Tours', 'content_class' => 'horizontal-nav',
   'bodyclass' => 'tours primary browse-tours' ) );
?>
<h1>Browse Tours (<?php echo $total_results; ?> total)</h1>

<?php if( is_allowed( 'TourBuilder_Tours', 'add' ) ): ?>
<p id="add-tour" class="add-button">
   <a class="add"
      href="<?php echo $this->url( array( 'action' => 'add' ) ); ?>">Add a Tour</a>
</p>
<?php endif; ?>

<div id="primary">
   <?php
      echo flash();
      if( $total_results > 0 ):
   ?>
   <div class="pagination"><?php echo pagination_links(); ?></div>
   <table id="tours" class="simple" cellspacing="0" cellpadding="0">
     <thead>
       <tr>
         <th scope="col">ID</th>
         <th scope="col">Title</th>
         <?php if( is_allowed( 'TourBuilder_Tours', 'edit' ) ): ?>
         <th scope="col">Edit?</th>
         <?php endif; ?>
       </tr>
     </thead>
     <tbody>
       <?php $key = 0; ?>
       <?php foreach( $tours as $key => $tour ):
		 $even_odd = ((++ $key % 2) == 1) ? 'odd' : 'even';
		 $show_url = $this->url( array( 'action' => 'show',
		                                'id' => $tour->id ) );
		 $edit_url = $this->url( array( 'action' => 'edit',
		                                'id' => $tour->id ) );

	   ?>
       <tr class="tours <?php echo $even_odd; ?>">
       <td scope="row"><?php echo $tour->id; ?></td>
       <td scope="row">
		 <a href="<?php echo $show_url ?>"><?php echo $tour->title; ?></a>
	   </td>
       <?php if( is_allowed( 'TourBuilder_Tours', 'edit' ) ): ?>
       <td>
		 <a class="edit" href="<?php echo $edit_url; ?>"><?php echo __('Edit'); ?></a>
	   </td>
       <?php endif; ?>
     </tr>
     <?php endforeach; ?>
   </tbody>
 </table>
 <?php endif; ?>
</div>

<?php foot();
