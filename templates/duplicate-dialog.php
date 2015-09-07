<div id="eventrocket_duplication_dialog" style="display: none">
	<div class="modal-form">
		<form method="post">
			<div class="title">
				<p>
					<?php _ex( 'Duplicate event', 'dialog title', 'event-rocket' ) ?>
					<span class="close-btn">&#10060;</span>
				</p>
			</div>
			<p>
				<label for="duplicate_title"> <?php _e( 'Set the new event title (or leave blank for default)', 'event-rocket' ) ?> </label>
				<input id="duplicate_title" name="duplicate_title" type="text">
			</p>

			<p>
				<label for="duplicate_datetimepicker"> <?php _e( 'Move to the following date (leave blank to use same date)', 'event-rocket' ) ?> </label>
				<input id="duplicate_datetimepicker" name="duplicate_datetimepicker" type="text">
				<input id="duplicate_datetime" name="duplicate_datetime" type="hidden">
			</p>

			<p>
				<label for="duplicate_status"> <?php _e( 'Status for new event', 'event-rocket' ) ?> </label>
				<select id="duplicate_status" name="duplicate_status">
					<option value="publish"> <?php _ex( 'Published', 'duplicate post status', 'event-rocket' ) ?> </option>
					<option value="pending"> <?php _ex( 'Pending review', 'duplicate post status', 'event-rocket' ) ?> </option>
					<option value="draft"> <?php _ex( 'Draft', 'duplicate post status', 'event-rocket' ) ?> </option>
				</select>
			</p>

			<p class="action-btns">
				<button id="do_duplicate" type="submit" class="button primary">
					<?php _ex( 'Duplicate', 'button label', 'event-rocket' ) ?>
				</button>
				<button id="cancel_duplication" type="submit" class="button secondary">
					<?php _ex( 'Cancel', 'button label', 'event-rocket' ) ?>
				</button>
			</p>
		</form>
	</div>
	<div class="back-screen"></div>
</div>