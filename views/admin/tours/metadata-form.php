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


   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'credits', __('Credits') ); ?>
 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formText( 'credits', $tour->credits ); ?>
 	  <p class="explanation"><?php echo __('OPTIONAL: The name of the person(s) or organization responsible for the content of the tour.');?></p>
 	</div>
   </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'description', __('Description') ); ?>

 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formTextarea( 'description', $tour->description,array( 'rows' => 12, 'cols' => '40' ) ); ?>
 		<p class="explanation"><?php echo __('The main text of the tour.');?></p>
 	</div>
   </div>

   <div class="field">
 	<div class="two columns alpha">
 	  <?php echo $this->formLabel( 'postscript_text', __('Postscript Text') ); ?>
 	</div>
 	<div class="five columns omega inputs">
 	  <?php echo $this->formTextarea( 'postscript_text', $tour->postscript_text,array( 'rows' => 3, 'cols' => '40' )  ); ?>
 	  <p class="explanation"><?php echo __('OPTIONAL: Add postscript text to the end of the tour, for example, to thank a sponsor or add directional information.');?></p>
 	</div>
   </div>

   
 </fieldset>

</div>
