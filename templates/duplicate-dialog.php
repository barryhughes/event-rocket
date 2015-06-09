<div id="eventrocket_duplication_dialog" style="display: none">
	<div class="modal-form">
		<form method="post">
			<div class="title">
				<p>
					<?php _ex( 'Duplicate event', 'dialog title', 'eventrocket' ) ?>
					<span class="close-btn">&#10060;</span>
				</p>
			</div>
			<p>
				<label for="duplicate_title"> Set the new event title (or leave blank for default) </label>
				<input id="duplicate_title" name="duplicate_title" type="text">
			</p>

			<p>
				<label for="duplicate_start"> Move to the following date (leave blank to use same date) </label>
				<input id="duplicate_start" name="duplicate_start" type="text">
			</p>

			<p>
				<label for="duplicate_status"> Status for new event </label>
				<select id="duplicate_status" name="duplicate_status">
					<option value="publish"> Published </option>
					<option value="pending"> Pending review </option>
					<option value="draft">   Draft </option>
				</select>
			</p>

			<p class="action-btns">
				<button id="do_duplicate" type="submit" class="button primary">
					<?php _ex( 'Duplicate', 'button label', 'eventrocket' ) ?>
				</button>
				<button id="cancel_duplication" type="submit" class="button secondary">
					<?php _ex( 'Cancel', 'button label', 'eventrocket' ) ?>
				</button>
			</p>
		</form>
	</div>
	<div class="back-screen"></div>
</div>