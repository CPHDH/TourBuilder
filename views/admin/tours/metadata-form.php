<div class="seven columns alpha" id="form-data">

 <fieldset>
   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'title', __('Title') ); ?>
 	</div>
 	<div class="five columns omega">
 	  <?php echo $this->formText( 'title', $tour->title ); ?>
 	  <p class="explanation"><?php echo __('A title for the tour.');?></p>
 	</div>
   </div>

   <div class="field hidden">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'slug', __('Slug') ); ?>
 	</div>
 	<div class="five columns omega">
 	  <?php echo $this->formText( 'slug', $tour->slug ); ?>
 	  <p class="explanation"><?php echo __('A short string of alphanumeric characters (no punctuation) used to identify the tour.');?></p>
 	</div>
   </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'credits', __('Credits') ); ?>
 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formText( 'credits', $tour->credits ); ?>
 	  <p class="explanation"><?php echo __('The name of the person(s) or organization responsible for the content of the tour.');?></p>
 	</div>
   </div>

     <div class="field">
         <div class="two columns alpha">
             <?php echo $this->formLabel('image',__('Image'));?>
         </div>
        <div class="five columns omega inputs">
            <?php echo $this->formFile('image'); ?>

             <p class="explanation">
	             <?php echo __('A single image file used to represent the tour in mobile apps.');?>
	         </p>

            <?php
if($tour->hasImage()) {

	echo '<div id="admin-tour-image">'.$tour->image().'</div>';
	echo '<span class="file-helper">'.__('The tour image will only be overwritten if you select a new file.').'</span>';

}
?>
        </div>
    </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'description', __('Description') ); ?>

 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formTextarea( 'description', $tour->description,
	array( 'rows' => 8, 'cols' => '40' ) ); ?>
 		                              <p class="explanation"><?php echo __('The main text of the tour.');?></p>
 	</div>
   </div>
 </fieldset>

</div>
