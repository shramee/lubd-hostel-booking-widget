/**
 * Travelanium Booking Plugin
 * Version: 1.3.3
 * Author: Travelanium
 * Author URI: http://www.travelanium.com
 * Support: support@travelanium.com
 *
 */

(
	function ( $ ) {
		"use strict";

		$.fn.booking = function ( options ) {
			var settings = $.extend( {
				checkInSelector: '[name="checkin"]',
				checkOutSelector: '[name="checkout"]',
				adultSelector: '[name="numofadult"]',
				childSelector: '[name="numofchild"]',
				roomSelector: '[name="numofroom"]',
				codeSelector: '[name="accesscode"]',
				builtinDatepicker: true,
				dateFormat: 'dd M yy',
				secretCode: null,
				submitSelector: '.btn-search',
				propertyId: '[name="propertyId"]',
				onlineId: 4,
				language: null,
				beforeSubmit: function () {
				},
				afterSubmit: function () {
				},
				debug: false,
			}, options );

			return this.each( function ( index, element ) {
				var $this = $( element ),
					$checkIn = $this.find( settings.checkInSelector ),
					$checkOut = $this.find( settings.checkOutSelector ),
					$submit = $this.find( settings.submitSelector );

				var checkInValue, checkOutValue;

				if ( settings.builtinDatepicker ) {
					/**
					 * Insert hidden fields
					 */
					var alt_checkin = $( '<input>' ).attr( {
						'type': 'hidden',
						'id': 'lubd_alt_checkin_' + index,
					} ), alt_checkout = $( '<input>' ).attr( {
						'type': 'hidden',
						'id': 'lubd_alt_checkout_' + index,
					} );

					$checkIn.after( alt_checkin );
					$checkOut.after( alt_checkout );

					var today = new Date(),
						tomorrow = nextday( today, 1 );

					$checkIn.datepicker( {
						minDate: today,
						changeMonth: false,
						changeYear: false,
						dateFormat: settings.dateFormat,
						altFormat: 'yy-mm-dd',
						altField: '#lubd_alt_checkin_' + index,
						numberOfMonths: 1,
						onSelect: function ( dateFormat, inst ) {
							$checkOut.datepicker( 'option', 'minDate', nextday( dateFormat, 1 ) );
							setTimeout( function () {
								$checkOut.datepicker( 'show' );
							}, 350 );
						}
					} );

					$checkOut.datepicker( {
						minDate: tomorrow,
						changeMonth: false,
						changeYear: false,
						dateFormat: settings.dateFormat,
						altFormat: 'yy-mm-dd',
						altField: '#lubd_alt_checkout_' + index,
						numberOfMonths: 1
					} );

					/**
					 * Set default date
					 */
					$checkIn.datepicker( 'setDate', today );
					$checkOut.datepicker( 'setDate', tomorrow );

					checkInValue = alt_checkin.val();
					checkOutValue = alt_checkout.val();
				} else {
					checkInValue = $checkIn.val();
					checkOutValue = $checkOut.val();
				}

				$this.on( 'submit', function ( event ) {
					event.preventDefault();
					runBookingScript();
				} );

				$submit.on( 'click', function ( event ) {
					event.preventDefault();
					runBookingScript();
				} );

				function runBookingScript() {
					var baseurl = 'https://reservation.travelanium.net/propertyibe2/?';
					var propertyIdParam = 'propertyId=';
					var onlineIdParam = 'onlineId=';
					var checkInParam = 'checkin=';
					var checkOutParam = 'checkout=';
					var adultParam = 'numofadult=';
					var childParam = 'numofchild=';
					var roomParam = 'numofroom=';
					var accesscodeParam = 'accesscode=';
					var propertyIdValue;
					if ( isFinite( settings.propertyId ) ) {
						propertyIdValue = settings.propertyId;
					} else {
						propertyIdValue = $this.find( settings.propertyId ).val();
					}

					if ( propertyIdValue.indexOf( 'http' ) > -1 ) {
						baseurl = propertyIdValue;
						propertyIdValue = '';
					}

					var onlineIdValue = settings.onlineId;

					var languageParam = 'lang=';
					var language = settings.language;

					var checkInValue = alt_checkin.val();
					var checkOutValue = alt_checkout.val();
					var adultValue = $this.find( settings.adultSelector ).val();
					var childValue = $this.find( settings.childSelector ).val();
					var roomValue = $this.find( settings.roomSelector ).val();
					var accesscode = $this.find( settings.codeSelector ).val();
					var secretcode = settings.secretCode;
					var accesscodeValue = (
						accesscode
					) ? accesscode : secretcode;
					var redirectUrl = baseurl + propertyIdParam + propertyIdValue + "&" + onlineIdParam + onlineIdValue;

					redirectUrl += "&" + checkInParam + checkInValue;
					redirectUrl += "&" + checkOutParam + checkOutValue;

					if ( 'undefined' !== typeof language && language ) {
						redirectUrl += '&' + languageParam + language;
					}

					if ( 'undefined' !== typeof roomValue && roomValue ) {
						redirectUrl += "&" + roomParam + roomValue;
					}

					if ( 'undefined' !== typeof adultValue && adultValue ) {
						redirectUrl += "&" + adultParam + adultValue;
					}

					if ( 'undefined' !== typeof childValue && childValue ) {
						redirectUrl += "&" + childParam + childValue;
					}

					if (
						accesscodeValue
						&& accesscodeValue !== null
						&& accesscodeValue !== undefined
					) {
						redirectUrl += "&" + accesscodeParam + accesscodeValue;
					}

					$.beforeSubmit = settings.beforeSubmit;
					$.beforeSubmit();

					gotoURL( redirectUrl );

					$.afterSubmit = settings.afterSubmit;
					$.afterSubmit();
				}

				/**
				 * Calcurate date
				 */
				function nextday( date, plus ) {
					var cur = new Date( date ),
						nxt = new Date( cur.getTime() + (
							plus * 24 * 60 * 60 * 1000
						) );
					return nxt;
				}

				function gotoURL( url ) {
					if ( settings.debug === true ) {
						console.log( decorateGACrossDomainTracking( url ) );
					} else {
						window.open( decorateGACrossDomainTracking( url ) );
					}
				}

				/**
				 * Generate Google tracking script
				 */
				function decorateGACrossDomainTracking( url ) {
					var output = url;
					try {
						if ( ! ga ) {
							return output;
						}
						ga( function ( tracker ) {
							if ( tracker == undefined ) {
								tracker = ga.getAll()[0];
							}
							if ( ! tracker ) {
								return output;
							}
							if ( ! tracker.get( 'linkerParam' ) ) {
								return output;
							}
							var linker = new window.gaplugins.Linker( tracker );
							output = linker.decorate( url );
						} );
					} catch ( e ) {
						if ( settings.debug === true ) {
							console.log( e.message );
						}
					}
					return output;
				}
			} );
		};

	}
)( jQuery );
