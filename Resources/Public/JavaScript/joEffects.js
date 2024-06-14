$(document).ready(() => {
	// window.localStorage.setItem('window_id','test');
	
	toggleNav = () => {
		var toggle = $('.joNaviTrigger');
		toggle.click(() => {
			toggle.parent().toggleClass('show');
		});
	}
	toggleNav();

	// $('.joMaskMove').joMouseScroll({'speed':30,'movepx': 4});

	function addScrollClass() {
		$('.scrollCss').removeClass('scrollCss');
		var className = $(this).data('scrolltrigger');
		$('[data-scrolltarget="' + className + '"').addClass('scrollCss');
	}

	$('[data-scrolltrigger]').unbind('click', addScrollClass);
	$('[data-scrolltrigger]').bind('click', addScrollClass);

	function scrollToAnker(ev) {
		if (!$(this).hasClass('joNoPrevent')) {
			ev.preventDefault();
			var linkId = $(this).attr('href').split('#').pop();
			$('html, body').stop().animate({
	        	scrollTop: $('#' + linkId).offset().top
		    }, 1500);
	    }
	}
/*
	$('a[href*="#"]').unbind('click',scrollToAnker);
	$('a[href*="#"]').bind('click',scrollToAnker);
*/
	setScroll();

	$(window).resize(() => {
		setScroll();
	});
	$('div.joNewsBackLink').click(() => {
		window.history.back();
	})

	function setScroll() {
		if ($(window).width() < 768) {
			$('.joLeftColHigher, .joRightColNavi, .joHeaderWrap').trigger("detach.ScrollToFixed");
			$('.joLeftColHigher').scrollToFixed({
	        	marginTop: 25
	    	});
	    	$('.joRightColNavi').scrollToFixed({
	        	marginTop: 65
	    	});
		} else {
			$('.joLeftColHigher, .joRightColNavi, .joHeaderWrap').trigger("detach.ScrollToFixed");
			$('.joHeaderWrap').scrollToFixed({
		        marginTop: -180
		    });
		}
	}

	$('.hover-effekt > li:not(.no-img)').hoverdir({hoverElem: '.hover-effekt-text'});

	$('.tile_view_controls #joOption_1').click(function() {
		$(this).toggleClass('joActive');
		$(this).next().toggleClass('joActive');
		$('.hover-effekt > li:not(.no-img)').hoverdir('rebuild');
		$('.hover-effekt').toggleClass('fixed');
	});

	$('.tile_view_controls #joOption_2').click(function() {
		$(this).toggleClass('joActive');
		$(this).prev().toggleClass('joActive');
		$('.hover-effekt > li:not(.no-img)').hoverdir('destroy');
		$('.hover-effekt').toggleClass('fixed');
	});
});

