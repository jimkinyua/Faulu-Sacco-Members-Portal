	// Will wait for everything on the page to load.
	$(window).bind('load', function() {
	// console.log('loading.....')
	});

	$('form').find("input[type=tel]").each(function(ev){
		if(!$(this).val()) { 
			$(this).attr("placeholder", "07XX XXX XXX");
		}
	});

	let timerOn = true;

	function timer(remaining) {
		var m = Math.floor(remaining / 60);
		var s = remaining % 60;
		
		m = m < 10 ? '0' + m : m;
		s = s < 10 ? '0' + s : s;
		document.getElementById('timer').innerHTML = m + ':' + s;
		// document.getElementById('ResendToken').style.display = "inline"
		document.getElementById('VerifyOTP').style.display = "inline"

		remaining -= 1;
		
		if(remaining >= 0 && timerOn) {
			setTimeout(function() {
				timer(remaining);
			}, 1000);
			document.getElementById('ResendToken').style.display = "none"
			return;
		}

		if(!timerOn) {
			// Do validate stuff here
			return;
		}
		
		// Do timeout stuff here
		// alert('Timeout for otp');
	}
