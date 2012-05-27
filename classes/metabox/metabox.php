<?php
	if($enabled && !empty($gc_event)):
?>

<input type="hidden" name="gc_update" value="Y" />
<input type="hidden" name="gc_prev_id" value="<?php  echo $event_meta['cal_id']; ?>" />
<input type="hidden" name="event_prev_id" value="<?php echo $gc_event['id']; ?>" />

<?php endif; ?>


<div class="wrap">
	<h2>Insert an Event to the Google calender </h2>
	<p> Enable G calender event <input id="enable_calender_event" type="checkbox" name="gc_enabled" value="1" <?php checked('1', $enabled); ?> /></p>
	<table  class="form-table" id="Gcalender_form" style="display: none;">
		<tr>
			<td>Select a calender</td>
			<td colspan="2">
				<select name="gc_id">
					<?php
						if(empty($calList)){
							echo "<option>No calender found!</option>";
						}
						else{
							foreach($calList['items'] as $item){
								echo '<option value="'.$item['id'].'" ' . selected($item['id'], $event_meta['cal_id']) . ' >'.$item['summary'].'</option>';
							}
						}
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>Event Title</td>
			<td colspan="2"><input size="60" name="gc-event-title" type="text" value="<?php echo $gc_event['summary']; ?>" /> </td>
		</tr>
		
		<tr>
			<td>Event Description</td>
			<td colspan="2"><textarea rows="3" cols="60"  name="gc-event-description"><?php echo $gc_event['description']; ?></textarea> </td>
		</tr>	
		
		<tr>
			<td>Event Start Date/Time</td>
			<td><input class="gc_date_picker" size="20" name="gc-event-date_start" type="text" value="<?php echo self::get_normalized_date($gc_event['start']['dateTime']); ?>" /> </td>
			<td><input class="gc_time_picker" size="20" name="gc-event-time_start" type="text" value="<?php echo self::get_normalized_time($gc_event['start']['dateTime']); ?>" /> </td>
			
		</tr>
		
		<tr>
			<td>Event End Date/Time</td>
			<td><input class="gc_date_picker" size="20" name="gc-event-date_end" type="text" value="<?php echo self::get_normalized_date($gc_event['end']['dateTime']); ?>" /> </td>
			<td><input class="gc_time_picker" size="20" name="gc-event-time_end" type="text" value="<?php echo self::get_normalized_time($gc_event['end']['dateTime']); ?>" /> </td>
			
		</tr>
		
	</table>
		
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('.gc_time_picker').timepicker({
                   showNowButton: true,
                    showDeselectButton: true,
                    showPeriod: true,  // removes the highlighted time for when the input is empty.
                    showCloseButton: true,
		    showLeadingZero: true
               })
		
		$('.gc_date_picker').datepicker();
		
		if($('#enable_calender_event').attr('checked') == 'checked'){
			$('#Gcalender_form').show();
		}		
		
		$('#enable_calender_event').bind('click', function(){
			if($(this).attr('checked') == 'checked'){
				$('#Gcalender_form').show();
			}
			else{
				$('#Gcalender_form').hide();
			}
						
		});
		
	});
</script>