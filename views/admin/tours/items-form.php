<?php
   $tourItemCount = count( $tour->Items );
   $addItemUrl = $this->url(
      array( 'id'   => $tour->id,
             'action' => 'browseForItem' ),
      'tourAction' );
?>
<!--
<p id="save-notice">
  <em><?php echo ('Note'); ?>:</em>
  <?php echo __('Save your changes before modifying the list of items'); ?>
</p>
-->

<ul id="tourbuilder-item-list">
    <div id="tour-items-table-container">
  <?php if( $tourItemCount ): ?>
  <table id="tour-items" class="simple" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
          <th scope="col" width="20"></th>
        <th scope="col">
          <?php echo __('Title'); ?>
        </th>
        <th scope="col">
        </th>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php foreach( $tour->Items as $tourItem ):
            $alternator = (++ $key % 2 == 1) ? 'odd' : 'even';
            $itemUri = record_url( $tourItem, 'show', true );
            $itemHoist = $this->url( array( 'action' => 'hoistItem',
                                            'id'     => $tour->id,
                                            'item'   => $tourItem->id ),
                                     'tourItemAction' );
            $itemLower = $this->url( array( 'action' => 'lowerItem',
                                            'id'     => $tour->id,
                                            'item'   => $tourItem->id ),
                                     'tourItemAction' );
            $itemDelete = $this->url( array( 'action' => 'removeItem',
                                             'id'     => $tour->id,
                                             'item'   => $tourItem->id ),
                                      'tourItemAction' );
      ?>
      <tr class="orderable items" id="table-row-<?php echo $tourItem->id; ?>" item-id="<?php echo $tourItem->id; ?>" hoist="<?php echo $itemHoist; ?>" lower="<?php echo $itemLower; ?>">
          <td scope="row">
              <img src="http://cdn.zendesk.com/images/documentation/agent_guide/views_icon.png">
          </td>
          
        <td scope="row">
          <a href="<?php echo $itemUri ?>">
            <?php echo metadata( $tourItem, array( 'Dublin Core', 'Title' ) ); ?>
          </a>
        </td>

        <td scope="row" id="td-<?php echo $tourItem->id; ?>-cell">
          <a class="delete" href="<?php echo $itemDelete; ?>" onClick="jQuery.removeItem('<?php echo $itemDelete; ?>', <?php echo $tourItem->id; ?>);return false;">
            <?php echo __('Remove'); ?>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>

    </tbody>
  </table>
  <?php endif; ?>
    </div>
</ul>

<div id="tourbuilder-additem">
  <a class="submit" href="<?php echo $addItemUrl; ?>" onclick="" id="add-item-link">
    <?php echo __('Add Item'); ?>
  </a>
</div>
<div id="tourbuilder-cancelitem">
    <a class="submit" href="#" id="cancel-item-link">
        <?php echo __('Cancel'); ?>
    </a>
</div>

<div id="status-area" style="text-align: center; padding: 15px; display: none;"></div>
<input type="hidden" name="statusText" value="">

<script type="text/javascript">
    var retrieving = false;
    var dotCount = 1;
    var timeoutObj = undefined;
    var timeoutCount = 1000;
    var dotMax = 5;
    var startIndex = 0;
    jQuery(document).ready(function ($) {
        
        $("#tour-items tbody tr:even").css("background-color","#f3f3e7");
        $("#tour-items tbody tr:odd").css("background-color","#fff");
        
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("#tour-items tbody").sortable({
            helper: fixHelper, 
            containment: "parent",
            start: function(evnt, ui) {
                startIndex = ui.item.index();
            },
            update: function(evnt, ui) {
                $("#tour-items tbody tr:even").css("background-color","#f3f3e7");
                $("#tour-items tbody tr:odd").css("background-color","#fff");
                alert("ID: " + ui.item.attr("item-id") + " moved from " + startIndex + " to " + ui.item.index());
                var newIndex = ui.item.index();
                if(newIndex > startIndex) {     // Lower
                    for(i = 0; i < newIndex - startIndex; i++) {
                        $.ajax({url: ui.item.attr("lower")});//.done(function() { alert("done"); });   
                    }
                } else
                if(newIndex < startIndex) {     //Hoist        
                    for(i = 0; i < startIndex - newIndex; i++) {
                        $.ajax({url: ui.item.attr("hoist")});//.done(function() { alert("done"); });   
                    }
                }
            }
        }).disableSelection();
        
        // Hide the cancel button by default
        $("#tourbuilder-cancelitem").hide();
        
        $.updateDot = function() {
            clearTimeout(timeoutObj);
            var _txt = $("input[name='statusText']").val() + '<br />';
            
            for(i = 0; i < dotCount; i++) {
                _txt = _txt + "&bull;";   
            }
            
            $("#status-area").html(_txt)
            
            dotCount += 1;
            if(dotCount > dotMax)
                dotCount = 1;
            
            timeoutObj = setTimeout("jQuery.updateDot();", timeoutCount);
        }
        
        $.setStatus = function(statusText) {
            
            //if($("input[name='statusText']").lengh == 0)
            //    $("body").append('');
            
            $("input[name='statusText']").val(statusText);
            
            $("#tourbuilder-cancelitem").hide();
            $("#tourbuilder-additem").hide();
            $("#tour-items").hide();
            $("#save-notice").hide();
            $("table#items").hide();
            $("#status-area").html(statusText).show();   
            
            dotCount = 1;
            timeoutObj = setTimeout("jQuery.updateDot();", timeoutCount);
            
        };
        
        $.hideStatus = function() {
            clearTimeout(timeoutObj);
          $("#status-area").html("").hide();  
        };
        
        $.setH2 = function(sText) {
            $("h2#action-title").html(sText);   
        }
        
        // Config the add item link
        $('#add-item-link').click(function (evt) {
            evt.preventDefault();
            if(retrieving == true) 
                return;
            retrieving = true;
            evt.preventDefault();
            $.setH2("Items &mdash; Select an item");
            $.setStatus("Retrieving items");
            $("#save").css({"position":"relative"}).append('<div style="position: absolute; width: 100%; height: 100%; z-index: 5; background: #ffffff; opacity: .7; top: 0; left: 0;" id="disable-placeholder"></div>');
            $.ajax({
                url: '<?php echo $addItemUrl; ?>'
            }).done(function ( serverResponse ) {
                //$("#add-item-link").html("Add Item").hide();
                $("#tourbuilder-item-list").append(serverResponse);
                $.hideStatus();
                $("#tourbuilder-cancelitem").show();
                retrieving = false;
                
                $("table#items a.add").click(function (evt) {
                    evt.preventDefault();
                    
                    $.setStatus("Adding item to tour");
                    
                    var submitUrl = $(this).attr("href");
                    var obj = $(this).parent();
                    
                    /*
                    $(obj).html("Adding item...");
                    $("#tourbuilder-cancelitem").hide();
                    */
                    
                    $.ajax({url: submitUrl}).done(function ( sResponse ) {
                        //$("#add-item-link").hide();
                        $.refreshItems();
                        //$("#tour-items-table-container").html("Retrieving tour items...").load($(location).attr('pathname') + " #tour-items");
                        //$('#cancel-item-link').trigger("click");
                        //$("#tourbuilder-additem").show();
                    });
                });
            });
        });
        
        // Config the cancel item link
        $('#cancel-item-link').click(function (evt) {
            evt.preventDefault();
            $("#save-notice").show();
            $("#tour-items").show();
            $("h2#action-title").html("Items");
            $("#tourbuilder-additem").show();
            $("#disable-placeholder").remove();
            $("#tourbuilder-cancelitem").hide();
            $("#primary").remove();
            $("#items").remove();
        });
        
        $.cancelItemAdd = function () {
            $("#tour-items").hide();
            $("#tourbuilder-additem").html("");
            $("#save-notice").show();  
        };
        
        $.removeItem = function(removeUrl, itemId) {
            if(confirm('<?php echo __('Are you sure you want to remove this item from this tour?'); ?>')) {
                $("#td-" + itemId + "-cell").html("<?php echo __('Removing'); ?>");
            $("#tourbuilder-additem").hide();
                $.setStatus("Removing item from tour");
                $.ajax({
                    url: removeUrl
                }).done(function ( serverResponse ) {
                    $("#table-row-" + itemId).fadeOut().remove();

                    if($("#tour-items tr").length == 1) {
                       $("#tour-items").hide();
                    } else {
                        //$("#add-item-link").hide();
                        //$.refreshItems();
                        //$.setStatus("Retrieving tour items...");
                        //$("#tour-items-table-container").load($(location).attr('pathname') + " #tour-items");
                        $.refreshItems();
                    }

                    try {
                        console.log("we hit removeItem() with the url: " + removeUrl);
                        console.log("rows left: " + $("#tour-items tr").length);
                    } catch(ex) {
                        //alert(ex.message);   
                    }
                });
            }
        };
        
        $.refreshItems = function() {
            //var wasVisible = $("#tourbuilder-additem").is(":visible");
            //$("#tourbuilder-additem").hide();
            $("h2#action-title").html("Items");
            //$("#tour-items").remove();
            $.setStatus("Refreshing tour items");
            $("#tour-items-table-container").load($(location).attr('pathname') + " #tour-items", function () { 
                $.hideStatus(); 
                $("#tourbuilder-additem").show(); 
                $("#disable-placeholder").remove();
                $.bindDownClicks();
                $.bindHoistClicks();
                
            });
            //if(wasVisible === true)
            
        };
        
        $.bindHoistClicks = function() {
            $("#tour-items a.up").click(function (evt) {
                evt.preventDefault();
               var _url = $(this).attr("href");
                $.setStatus("Hoisting");
                $.ajax({url: _url}).done(function () {
                   $.refreshItems(); 
                });
            });
        };
        
        $.bindDownClicks = function() {
            $("#tour-items a.down").click(function (evt) {
                evt.preventDefault();
                var _url = $(this).attr("href");
                $.setStatus("Lowering");
                $.ajax({url: _url}).done(function () {
                   $.refreshItems(); 
                });
            });
        };
        
        $.bindDownClicks();
        $.bindHoistClicks();
    });
</script>
