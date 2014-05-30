<div class="seven columns alpha" id="form-data">

 <fieldset>
   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'title', __('Title') ); ?>
 	</div>
 	<div class="five columns omega">
 	  <?php echo $this->formText( 'title', $tour->title ); ?>
 	</div>
   </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'slug', __('Slug') ); ?>
 	</div>
 	<div class="five columns omega">
 	  <?php echo $this->formText( 'slug', $tour->slug ); ?>
 	</div>
   </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'credits', __('Credits') ); ?>
 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formText( 'credits', $tour->credits ); ?>
 	</div>
   </div>
     
     <!--
     <div class="field">
         <div class="two columns alpha">
             <?php echo $this->formLabel('image',__('Image'));?>
         </div>
        <div class="five columns omega inputs">
            <?php echo $this->formFile('image',$tour->image); ?>
        </div>
    </div>
-->

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'description', __('Description') ); ?>
 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formTextarea( 'description', $tour->description,
 		                              array( 'rows' => 8, 'cols' => '40' ) ); ?>
 	</div>
   </div>
 </fieldset>

</div>
