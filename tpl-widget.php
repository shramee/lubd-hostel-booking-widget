<?php
wp_enqueue_script( 'lubd-booking' );
wp_enqueue_style( 'lubd-booking' );
?>

<section id="lubd_booking_widget">
	<h1>Booking with Label</h1>
	<div id="lubd_booking_form">
		<form id="booking_form" class="form form-horizon form-with-label">
			<?php if ( ! empty( $instance['preset_location'] ) ) { ?>
				<input type="hidden" id="lubd_property" value="<?php echo $instance['preset_location'] ?>">
			<?php } else { ?>
				<div class="form-group">
					<span class="field select-icon">
						<label class="label" for="lubd_child">Location</label>
						<select id="lubd_property" class="input-select">
							<option value="" selected>Please choose</option>
							<option value="419">Bangkok Silom</option>
							<option value="420">Bangok Siam</option>
							<option value="421">Phuket Patong</option>
							<option value="504">Cambodia Siem Reap</option>
							<option value="https://hotels.cloudbeds.com/reservation/ZH7GQ6#">Philippines Makati</option>
						</select>
					</span>
				</div>
			<?php } ?>

			<div class="form-group">
      <span class="field datepicker-icon">
          <label class="label" for="lubd_checkin_date">Check-in</label>
          <input id="lubd_checkin_date" class="input-text" placeholder="Check-in" type="text" readonly/>
      </span>
			</div>

			<div class="form-group">
      <span class="field datepicker-icon">
          <label class="label" for="lubd_checkout_date">Check-out</label>
          <input id="lubd_checkout_date" class="input-text" placeholder="Check-out" type="text" readonly/>
      </span>
			</div>

			<div class="form-group">
      <span class="field">
          <label class="label" for="lubd_access_code">Promo Code</label>
          <input id="lubd_access_code" class="input-text" placeholder="Promo Code" type="text"/>
      </span>
			</div>

			<div class="form-group">
      <span class="field">
          <button id="lubd_submit" class="button" type="submit">BOOK NOW</button>
      </span>
			</div>
		</form>
	</div>

	<script>
		jQuery( function ( $ ) {
			$( '#lubd_booking_form' ).booking( {
				checkInSelector: '#lubd_checkin_date',
				checkOutSelector: '#lubd_checkout_date',
//				adultSelector: '#lubd_adult',
//				childSelector: '#lubd_child',
//				roomSelector: '#lubd_room',
				codeSelector: '#lubd_access_code',
				submitSelector: '#lubd_submit',
				propertyId: '#lubd_property',
			} );
		} );
	</script>
</section>