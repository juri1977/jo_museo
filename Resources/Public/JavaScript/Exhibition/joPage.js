(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		// AMD. Register as an anonymous module.
		define( [ 'jquery', 'jqueryui', 'swipe', 'bootstrap', 'jquery.nicescroll.min', 'jquery.joslider', 'jquery.joswitcher', 'zoom', 'lightbox' ], factory );
	} else if ( typeof module === "object" && module.exports ) {
		// Node/CommonJS
		module.exports = factory(require("jquery"));
	} else {
		// Browser globals
		factory( jQuery );
	}
}(function( $ ) {
    var mobile = ($('.hidden-xs').is(':visible')) ? false : true;
	
	/* Navigation drehung end */
	function initNiceScroll(){
		if(jQuery().niceScroll) {
			if(!mobile){
				$("html").niceScroll({horizrailenabled:false});
			}else{
				$("html").getNiceScroll().remove();
			}
		}
	}
	initNiceScroll();
	
	/* slideshow */
	var joSliderEl = $('.jo-slider');
	initJoSlider = function(){
		var slidesImg = joSliderEl.find('.slides li.news-item img');
		if(mobile){
			slidesImg.css('width',$('#stck-ftr-page').width());
		}else{
			slidesImg.css('width','100%');
		}
	}
	initJoSlider();
	joSliderEl.joSlider({
		speed : 800,
		autoSlider: false,
		changeHeightArrows: -20,
		offsetVp: 10,
		fnInit: function(){ $('.viewport').css('opacity','1'); },
		fnComplete: function(){ 
			if(mobile){
				var _par = this.elUl.closest('.news_wrap');
				var _ind = this.elUl.find('li.active').data('uid');
					
				if(_par.find('.jo-detail').is(':hidden')){
					_par.find('.jo-detail').show();
				}
				_par.find('.jo-detail li').hide();
				_par.find('.jo-detail li[data-uid="' + _ind + '"]').fadeIn();
				this.elUl.find('li').removeClass('jo-open');
				this.elUl.find('li.active').addClass('jo-open');
			}		
		}
	});	
	
	var newsItem = $('.jo-slider .news-item');
	var itemMargin = (mobile) ? 15 : 30;
	function checkActiveSlider(){
		var _this = $(this);
		var _par = _this.closest('.news_wrap');
		var _ind = _this.data('uid');
		if(mobile){
			if(!_this.hasClass('jo-open')){
				_par.find('.jo-detail li').hide();
				_par.find('.jo-detail li[data-uid="' + _ind + '"]').fadeIn();
			}
		}else{
			_par.find('.jo-detail li').hide();
			_par.find('.jo-detail li[data-uid="' + _ind + '"]').fadeIn();
		}
		
		if(_this.hasClass('jo-open') && $('.jo-detail').is(':visible')){
			if(!mobile){
				$('.jo-detail').slideUp(function(){ if(jQuery().niceScroll) { $("html").getNiceScroll().resize(); } );
				newsItem.removeClass('jo-open');
			}
		}else{
			newsItem.removeClass('jo-open');
			_this.addClass('jo-open');
			$('.jo-detail').slideDown(function(){ if(jQuery().niceScroll) { $("html").getNiceScroll().resize(); } });
		}		
	}
	
	function closeActiveSlider(){
		var _this = $(this).closest('.jo-detail');
		if(_this.is(':visible')){
			$('.jo-detail').slideUp(function(){ if(jQuery().niceScroll) { $("html").getNiceScroll().resize(); } });
			newsItem.removeClass('jo-open');
		}
	}
	
	newsItem.unbind('click',checkActiveSlider);
	newsItem.bind('click',checkActiveSlider);
	
	newsItem.unbind('touchstart',checkActiveSlider);
	newsItem.bind('touchstart',checkActiveSlider);
	
	$('.jo-detail .close').unbind('touchstart',closeActiveSlider);
	$('.jo-detail .close').bind('touchstart',closeActiveSlider);
	
	/* jo ce images start */
	var ceimages = $('.jo-ceimage-item');
	
	function setWidthForCeImageCenter(){
		var ceimagesWrap = $('.jo-ceimage.layout-1 .jo-center');
		ceimagesWrap.removeAttr('style');
		if(mobile){
			var w = 0;
			$.each(ceimagesWrap.find('.jo-ceimage-item'), function(){
				w += parseInt($(this).width()) + parseInt($(this).css('margin-left')) + parseInt($(this).css('margin-right'));
			});
			//Bildschirmbreite abziehen
			var ori = window.orientation;
			var screenwidth = (ori==90 || ori==-90) ? window.innerHeight : window.innerWidth;
			w -= screenwidth;
			w = parseInt(w/2) * -1;
			ceimagesWrap.css('margin-left', w);
		}
	}
	function activateCeImage(){
		if(!mobile){
			var _this = $(this);
			_this.closest('.jo-ceimage').find('.jo-ceimage-item').removeClass('active');
			_this.addClass('active');
		}
	}
	// activate preselect Images if Mobile
	function preselectCeImage(){
		if(mobile){
			$('.jo-ceimage-item.preselect').addClass('active');
		}
	}
	setWidthForCeImageCenter();
	preselectCeImage();
	
	ceimages.unbind('click',activateCeImage);
	ceimages.bind('click',activateCeImage);
	
	$('.jo-ce.layout-1').joSwitcher({
		'selItems' : '.jo-ceimage-item'
	});
	
	/* jo ce images end */
	
	/* resize functions */
	$( window ).resize(function() {		
		mobile = ($('.hidden-xs').is(':visible')) ? false : true;
		
		/* ceImages */
		setWidthForCeImageCenter();
		
		/* joslider */
		initJoSlider();
		
		/* nicescroll */
		initNiceScroll();
		
		if(jQuery().niceScroll) {
			$("html").getNiceScroll().resize();
		}
	});
	
	/* orientation change */
	$( window ).on( "orientationchange", function( event ) {
	  setWidthForCeImageCenter();
	});
	/* orientation change end*/
	
	/* scroll functions */
	
	/* scroll top button */
	var _btnTop = $('<a href="#top" class="back-to-top"><div class="vp jo-sprite jo-top"></div></a>');
	$("body").append(_btnTop);
	function scrollTopBtn (scrlTop) {
		if (scrlTop > 100) {
			if(!_btnTop.hasClass('active'))_btnTop.addClass('active');
		} else {
			if(_btnTop.hasClass('active'))_btnTop.removeClass('active');
		}
	};
	_btnTop.click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	
	/* flying owl imiges slide from side into screen */
	var flowl = $('.frame-flyingowl');
	function flyingowl(scrlTop){
		var addOff = (mobile)? 300 : 400;
		$.each(flowl, function(){
			if ( scrlTop+addOff > $(this).offset().top ) { 
				$(this).addClass('active');
			}
		});
	}

	var joExhFlowl = $(".joFlyingowl");
	function joExhFlyingowl(scrlTop){
		var addOff = $(window.top).height() / 1.5;
		var bottom = $(window).scrollTop() + $(window).height() == $(document).height();
		$.each(joExhFlowl, function(){
			if ( scrlTop+addOff > $(this).offset().top || bottom) { 
				$(this).addClass('active');
			}
		});
	}
	
	/* Tooltip */
	$('[data-toggle="tooltip"]').tooltip({ position: { my: "right center", at: "left center" } }); 

	var navi_el = $(".joExhibition_navi");
	var navi_top = $(".jo-navbar");
	function exhNavi(scrlTop) {
		if ($('.joExhibition_content').length && scrlTop <= $('.joExhibition_content').offset().top) {
			if(navi_el.hasClass("active"))navi_el.removeClass("active");
			if(navi_top.hasClass("active"))navi_top.removeClass("active");
		} else {
			if(!navi_el.hasClass("active"))navi_el.addClass("active");
			if(!navi_top.hasClass("active"))navi_top.addClass("active");
		}
	};
	
	var isExhibition = $('.joExhibition_container').length;
	/* scroll events */
	$(window).scroll(function() {
	  var scrollTop = $(window).scrollTop();
	  if(flowl.length>0) flyingowl(scrollTop);
	  if(joExhFlowl.length>0) joExhFlyingowl(scrollTop);
	  scrollTopBtn(scrollTop);
	  if(isExhibition) {
	  	exhNavi(scrollTop);
	  }
	});
	flyingowl($(window).scrollTop());
	joExhFlyingowl($(window).scrollTop());

	var sectionContainer = $(".sectionenContainer");
	var sectionOption_1 = $("#joOption_1");
	var sectionOption_2 = $("#joOption_2");

	sectionOption_1.click(function() {
		if(!$(this).hasClass("joActive")) {
			sectionContainer.removeClass("showTitle");
			toggleActive($(this));
			toggleActive(sectionOption_2);
		}
	});

	sectionOption_2.click(function() {
		if(!$(this).hasClass("joActive")) {
			sectionContainer.addClass("showTitle");
			toggleActive($(this));
			toggleActive(sectionOption_1);
		}
	});

	function toggleActive(el) {
		if(el.hasClass("joActive")) {
			el.removeClass("joActive");
		} else {
			el.addClass("joActive");
		}
	}

	var starter = $(".joExhibition_starter");
	starter.click(function () {
		$('body,html').animate({
			scrollTop: $(".joExhibition_content").offset().top
		}, 800);
		return false;
	});

	var leftNaviItem = $("#joExhibition_leftNavi li a, .lastrow figure");
	leftNaviItem.click(function () {
		var imgOff = parseInt($("#jo-main-navbar").css("height"));
		var href = $(this).attr("href");
		if(href) {
			$('body,html').animate({
				scrollTop: $(href).offset().top - imgOff
			}, 800);
		}
		return false;
	});


	function initShow() {
		setInterval(function() {
	        // if last element, then start from the beginning else go next element
	        var activeImg = $("#joShow > .img-item.active");
	        if(activeImg.get(0) == $("#joShow > div:last-child").get(0)) {
	            $("#joShow > div:last-child").removeClass('active');
	            $("#joShow > div:first-child").addClass('active');
	        } else {
	        	activeImg.removeClass('active');
	            activeImg.next().addClass('active');
	        }
	    }, 3000);
	}

	function startClick() {
		$(this).removeClass('active');
    	if($('.joVideo-container').length) {
    		$('.joOverlay').removeClass('active');
    		$('#joVideoPlayer')[0].play();
    		$(this).addClass('jo-pfeil');
    		$(this).css('color', 'transparent');
    		$(this).off('click', startClick);
    		$(this).on('click', function() {
    			$('body,html').animate({
					scrollTop: $(".joExhibition_choosing").offset().top
				}, 800);
    		});
    	} else {
    		initShow();
    	}
	}

    $('.joVideo-btn').on('click', startClick);

    function joCollapse(el, time) {
    	var that = $(el);
		var text = that.parent().prev();
		text.toggleClass('inactive');
		if(text.hasClass('joGradientBottom')) {
			setTimeout(function() {
				text.removeClass('joGradientBottom');
			},time);
			that.text('- Weniger ...');
		} else {
			text.addClass('joGradientBottom');
			that.html('<span class="plus">+</span> Mehr ...');
		}
    }

	$('#summary_more').click(function() {
		joCollapse($(this), 800);
	});

	$('body').on('click', '.showMore', function() {
		var that = $(this);
		var el = that.parent().prev('.collapse');
		el.toggle({
			duration: 300,
			step: function() {
				resizeContainer();
			}
		});
		var text = that.text();
		if(text.indexOf('Mehr') >= 0) {
			that.text('- Weniger');
		} else {
			that.html('<span class="plus">+</span> Mehr');
		}
	});

	$('.dataInfo').ready(function() {
		var that = $(this);
		that.prev().data('zoom-image', that.text());
	});

	function toNext(ev) {
		var that = $(event.target);
		var current = $('.pagination .page-item.active');

		if(current.next(':not(.joNext)').length) {
			current.removeClass('active');
			current = current.next(':not(.joNext)').addClass('active');
			hideActive('left');
			addNewActive(current.data('id'), 'right');
			showLoadMore(current.data('id'));
			if(current.next(':not(.joNext)').length == 0) {
				$('.joObject_main.active .joNext').hide();
			}
			$('.joObject_main .joPrev').show();
			$('.joObject_extra').empty();
			niceScrollReloade();
			resizeContainer();
		} else {
			if(!that.hasClass('disabled')) {
				$(that).addClass('disabled');
			}
		}
	}

	function toPrev(ev) {
		var that = $(event.target);
		var current = $('.pagination .page-item.active');

		if(current.prev(':not(.joPrev)').length) {
			current.removeClass('active');
			current = current.prev(':not(.joPrev)').addClass('active');
			hideActive('right');
			addNewActive(current.data('id'), 'left');
			showLoadMore(current.data('id'));
			if(current.prev(':not(.joPrev)').length == 0) {
				$('.joObject_main.active .joPrev').hide();
			}
			$('.joObject_main .joNext').show();
			$('.joObject_extra').empty();
			niceScrollReloade();
			resizeContainer();
		} else {
			if(!that.hasClass('disabled')) {
				$(that).addClass('disabled');
			}
		}
	}

	function resizeContainer() {
		$('.joObject_main_container').css('min-height', $('.joObject_main.active').css('height'));
	}

	function hideActive(direction) {
		$('.joObject_main.active').hide('slide', {direction: direction }, 'slow');
		$('.joObject_main.active').removeClass('active');
	}

	function addNewActive(id, direction) {
		$('#' + id).show('slide', {direction: direction}, 'slow');
		$('#' + id).addClass('active');
	}

	function showLoadMore(id) {
		if($('#' + id + ' .ajaxLink').length) {
			$('.loadMore-container').show();
		} else {
			$('.loadMore-container').hide();
		}
	}

	$('body').on('click', '.joObject_container .pagination .page-item:not(.joPrev, .joNext, .active)', function() {
		var that = $(this);
		var indexOld = $('.pagination .page-item.active').index();
		var indexNew = that.index();
		$('.pagination .page-item.active').removeClass('active');
		var dHide = indexOld > indexNew ? 'right' : 'left';
		var dShow = indexOld > indexNew ? 'left' : 'right';
		hideActive(dHide);
		addNewActive(that.data('id'), dShow);
		that.addClass('active');
		$('.joObject_extra').empty();
		showLoadMore(that.data('id'));
		niceScrollReloade();
		resizeContainer();

		if(that.prev('.joPrev').length) {
			$('.joObject_main.active .joPrev').hide();
		}
		if(that.next('.joNext').length) {
			$('.joObject_main.active .joNext').hide();
		}
	});

	$('.joObject_container .pagination .joPrev, .joObject_main .joPrev').on('click', toPrev);
	$('.joObject_container .pagination .joNext, .joObject_main .joNext').on('click', toNext);

	$('.joObject_container .pagination').swipe( {
        swipeLeft:toPrev,
        swipeRight:toNext
    });

	$('.loadMore').click(function(ev) {
		ev.preventDefault();
		var href = $('.joObject_main.active .ajaxLink').val();
		var loader = $('.joLoader-container').parent();
		$.when(getContent(href, loader)).done(function(data) {
			$(loader).hide();
			$('.joObject_extra').html(data);
			niceScrollReloade();

			$('body,html').animate({
				scrollTop: $(".joObject_extra").offset().top
			}, 800);
		});
		$(this).parent().hide();
	});

	function getContent(url, loader) {
		$(loader).show();
    	return $.ajax({
			'type': "GET",
			'url': url,
			'success': function(data){
				return data;
			}
		});
    }

    function niceScrollReloade() {
    	if(jQuery().niceScroll) {
			$("html").getNiceScroll().remove();
		}
    	initNiceScroll();
    }
}));