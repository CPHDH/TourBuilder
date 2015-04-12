<?php
   $tourItemCount = count( $tour->Items );
   $addItemUrl = $this->url(
      array( 'id'   => $tour->id,
             'action' => 'browseForItem' ),
      'tourAction' );
?>
<p id="save-notice">
  <em><?php echo ('Note'); ?>:</em>
  <?php echo __('No changes will be applied unless you save them.'); ?>
</p>

<ul id="tourbuilder-item-list">
    <div id="tour-items-table-container">
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
  <?php if( $tourItemCount ): ?>
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
          <a class="delete" href="<?php echo $itemDelete; ?>" onClick="jQuery.removeItem(<?php echo $tourItem->id; ?>);return false;">
            <?php echo __('Remove'); ?>
          </a>
        </td>
      </tr>


      <?php endforeach; ?>

  <?php endif; ?>
    </tbody>
  </table>
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
    var itemsInTour = <?php echo json_encode($tour->Items); ?>;
    var retrieving = false;
    var dotCount = 1;
    var timeoutObj = undefined;
    var timeoutCount = 1000;
    var dotMax = 5;
    var startIndex = 0;

    jQuery(document).ready(function ($) {
        
        $.allTourItems = undefined;
        $.formCanSubmit = false;

        $("#add-item-link").hide();
        $.ajax({
            url: '<?php echo $this->url( array('action' => 'getItems', 'id' => $tour->id), 'tourAction'); ?>',
            dataType: 'json'
        }).done(function( serverResponse ) {
            $.allTourItems = serverResponse;
            $("#add-item-link").show();
        });

        $("#tour-form").submit(function() {
            if($.formCanSubmit === true) {
                return true;
            }

            var subURL = '<?php echo $this->url( array( 'action' => 'saveTourItems',
                                            'id'     => $tour->id),
                                            'tourAction' ); ?>';
            var objsArray = [];
            $("tr.orderable").each(function( index ) {
                objsArray.push($(this).attr("item-id"));
            });
            var _json = JSON.stringify(objsArray);
            //alert(_json);
            $("#form-data").hide();
            $.setH2("Saving Tour");
            $.setStatus("Please wait while the tour is being saved");
            $.ajax({
                url: subURL,
                data: {"saveOrder":_json},
                type: "POST"
            }).done(function ( serverResponse ) {
                $.formCanSubmit = true;
                setTimeout("jQuery('#save-changes').trigger('click');",1000);
                return false;
            });

            return false;
        });

        $.styleTRs = function() {
            $("#tour-items tbody tr:even").css("background-color","#f3f3e7");
            $("#tour-items tbody tr:odd").css("background-color","#fff");
        };
        $.styleTRs();
        
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $.bindSortable = function() {

            $("#tour-items tbody").sortable({
                helper: fixHelper,
                containment: "parent",
                start: function(evnt, ui) {
                },
                update: function(evnt, ui) {
                    $.styleTRs();
                }
            }).disableSelection();
        };

        $.bindSortable();
        
        // Hide the cancel button by default
        $("#tourbuilder-cancelitem").hide();
        
        // Function that displays the dot in an
        // incrementing fashion
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
        
        // Function to set the status text above the dot
        $.setStatus = function(statusText) {
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
//            if(retrieving == true)
//                return;
//            retrieving = true;


            $("#tour-items").hide();
            $("#tourbuilder-cancelitem").show();
            $(this).hide();

            $.setH2("Items &mdash; Select an item");
            //$.setStatus("Retrieving items");
            $("#save").css({"position":"relative"}).append('<div style="position: absolute; width: 100%; height: 100%; z-index: 5; background: #ffffff; opacity: .7; top: 0; left: 0;" id="disable-placeholder"></div>');

            // Check if we have the new table in the DOM
            if($("table#items").length == 0) {
                var newTable = document.createElement("table");
                var thead = document.createElement("thead");
                var tr = document.createElement("tr");


                $(newTable).attr("id","items").attr("cellspacing","0").attr("cellpadding","0");

                var t1 = document.createElement("th");
                $(t1).attr("scope","col").attr("width","20").html("");
                tr.appendChild(t1);

                t1 = document.createElement("th");
                $(t1).attr("scope","col").html("Title   ");
                tr.appendChild(t1);

                t1 = document.createElement("th");
                $(t1).attr("scope","col").html("");
                tr.appendChild(t1);

                // Add the tr to the thead
                thead.appendChild(tr);

                // Add the thead to the table
                newTable.appendChild(thead);

                

                var tbody = document.createElement("tbody");
                newTable.appendChild(tbody);

                $("#tourbuilder-item-list").append(newTable);
                $(newTable).addClass("simple");
            }


            // Now add all of the available items to the table
            var key = 0;
            for(i = 0; i < $.allTourItems.length; i++) {
                var tbody = $("table#items tbody");
                //debugger;
                if($.allTourItems[i].in_tour == 0) {
                    var tr = document.createElement("tr");
                    var td = document.createElement("td");
                    
                    $(td).attr("scope","row").html($.allTourItems[i].id);
                    tr.appendChild(td);
                    
                    td = document.createElement("td");
                    $(td).attr("scope","row").html($.allTourItems[i].name);
                    tr.appendChild(td);

                    td = document.createElement("td");
                    $(td).attr("scope","row").html('<a href="" onClick="jQuery.addItem(' + $.allTourItems[i].id + ');return false;"><?php echo __('Add'); ?></a>');
                    tr.appendChild(td);

                    $(tr).addClass("items").attr("add-row-id",$.allTourItems[i].id);

                    $("table#items tbody").append(tr);
                }
            }
            $("#disable-placeholder").remove();
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
            $("table#items").remove();
            $("#add-item-link").show();
            $("#tourbuilder-cancelitem a").html("Cancel");
        });
        
        $.cancelItemAdd = function () {
            $("#tour-items").hide();
            $("#tourbuilder-additem").html("");
            $("#save-notice").show();  
        };

        $.addItem = function(itemId) {
            for(i = 0; i < $.allTourItems.length; i++) {
                if($.allTourItems[i].id == itemId) {
                    // We found it, now insert it
                    var tr = document.createElement("tr");

                    $(tr).addClass("orderable items").attr("id","table-row-" + itemId).attr("item-id",itemId);

                    var td = document.createElement("td");
                    $(td).attr("scope","row").html('<img src="http://cdn.zendesk.com/images/documentation/agent_guide/views_icon.png">');
                    tr.appendChild(td);

                    td = document.createElement("td");
                    $(td).attr("scope","row").html('<a href="' + $.allTourItems[i].uri + '">' + $.allTourItems[i].name + '</a>');
                    tr.appendChild(td);

                    td = document.createElement("td");
                    $(td).attr("scope","row").html('<a class="delete" onClick="jQuery.removeItem(' + itemId + ');"><?php echo __('Remove'); ?></a>');
                    tr.appendChild(td);

                    $("table#tour-items tbody").append(tr);

                    // Now set the json object to actually be
                    // in the tour
                    $.allTourItems[i].in_tour = 1;
                    $("table#items tr[add-row-id='" + itemId + "']").fadeOut();
                    $("#tourbuilder-cancelitem a").html("Done");
                }
            }
            $.styleTRs();
        }

        $.removeItem = function(itemId) {
            $("tr#table-row-" + itemId).remove();
            $.styleTRs();
            for(i = 0; i < $.allTourItems.length; i++) {
                if($.allTourItems[i].id == itemId) {
                    $.allTourItems[i].in_tour = 0;
                }
            }
        };
        
        $.refreshItems = function() {
            $("h2#action-title").html("Items");
            $.setStatus("Refreshing tour items");
            $("#tour-items-table-container").load($(location).attr('pathname') + " #tour-items", function () { 
                $.hideStatus(); 
                $("#tourbuilder-additem").show(); 
                $("#disable-placeholder").remove();
                $.bindDownClicks();
                $.bindHoistClicks();
                $.bindSortable();
                $.styleTRs();
            });
        };
    });
</script>
