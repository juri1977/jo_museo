var currentlyZooming = false;
var audioplayerAction = null;
$(function() {

	var $loader = $('#joAjaxloader');

	var mobile = ($('#mobile-hidden').is(':visible')) ? false : true;


	var $video_slick = $('.video-slick');
	var $video_nav_slick = $('.video-nav-slick');

	if ($video_slick.length && $.fn.slick) {
		$video_slick.slick({
	  		slidesToShow: 1,
	  		slidesToScroll: 1,
	  		fade: true,
				asNavFor: $video_nav_slick[0]
		});

		$video_nav_slick.slick({
			centerMode: true,
	  		slidesToShow: 1,
			slidesToScroll: 1,
			asNavFor: $video_slick[0],
			focusOnSelect: true,
			vertical: true,
			verticalSwiping: true
		});
	}

    $('.joText-btn-icon').click(function(e) {
    	e.stopPropagation();
    	$(this).toggleClass('hide');
    	$('.sectionInfo-container').toggle();
    });

    $('.audio-intro-btn').click(function(e) {
    	e.stopPropagation();
    	$(this).closest('.desc-con-audio').find('.view2-overlay').fadeIn();
    });

    $('.img-btn').click(function(e) {
    	e.stopPropagation();
    	$(this).closest('.obj_wrap').children('.view2-overlay').fadeIn();
    });

    $('.video-btn').click(function() {
    	$(this).parent().parent().find('.view2-overlay').fadeIn();
    });

    $('.view2-overlay').click(function() {
    	$(this).fadeOut();
    });

    $('.view2-overlay-close').click(function() {
    	$(this).closest('.view2-overlay').fadeOut();
    });

    $('.view2-overlay-text').click(function(e) {
    	e.stopPropagation();
    });

    $('.cropped .show-more, .full .show-less').click(function() {
    	$parent = $(this).parent().parent().parent();
    	$parent.find('.cropped').toggle();
    	$parent.find('.full').toggle();
    });

    var ic = $('.sectionInfo-container');
    if(ic.length) {
    	$('.joText-btn-icon').fadeIn(300);
    }

    $('.section_container .section .more-arrow').click(function() {
    	var $section = $(this).closest('.section');
    	if(!$section.hasClass('up-scroll')) {
	    	var section_img = $(this).closest('.section').next().find('.section_img');
	    	$('body,html').animate({
				scrollTop: section_img.offset().top
			}, 800);
    	}
    });

    $('.video-poster-overlay').click(function() {
    	$that = $(this);
    	$that.hide();
    	$that.prev('video')[0].play();
    });

    var video = document.querySelector('.video-slick video');
    if (null != video) {
		video.addEventListener('pause', function(event) {
	  		$(this).next('.video-poster-overlay').show();
		});
    }

    var flowl = $('.frame-flyingowl');
	function flyingowl(scrlTop) {
		var addOff = (mobile) ? 300 : 400;
		$.each(flowl, function() {
			if(scrlTop+addOff > $(this).offset().top) { 
				$(this).addClass('active');
			}
		});
	}

	var joExhFlowl = $('.section .sectionInfo-c-btn');
	function joExhFlyingowl(scrlTop) {
		var addOff = $(window.top).height() / 1.5;
		var bottom = $(window).scrollTop() + $(window).height() == $(document).height();
		$.each(joExhFlowl, function() {
			if(scrlTop+addOff > $(this).offset().top || bottom) { 
				$(this).addClass('active');
			}
		});
	}

	$(window).scroll(function() {
		var scrollTop = $(window).scrollTop();
		if(flowl.length > 0) flyingowl(scrollTop);
		if(joExhFlowl.length > 0) joExhFlyingowl(scrollTop);
	});
	flyingowl($(window).scrollTop());
	joExhFlyingowl($(window).scrollTop());


	/*
	var ajaxLink = $('.controlAjaxLink');
	var aLoaded = false;
	if(ajaxLink.length) {
		$('.navbar-toggler').click(function() {
			if(aLoaded) {
				$('body').toggleClass('noscroll');
				$('.controlAjaxOut').toggleClass('active');
			} else {
				$loader.show();
				var href = ajaxLink.attr('href');
				$.get(href).success(function(data) {
					aLoaded = true;
					$('.controlAjaxOut').html(data).addClass('active');
					$('body').addClass('noscroll');
					$loader.hide();
				});
			}
		});
	}
	*/

	/* $.scrollify({
    	section : ".joObject",
		}); */
		$('.joSitemap-btn').click(function() {
			$('.joSitemap').toggleClass('active');
		});

		$('.sitemap-icon-container').click(function() {
			$('.joSitemap').toggleClass('active');
			$('.joText-btn-icon').fadeToggle();
		});

		$('.sectionInfo-c-btn').click(function() {
			window.location.href = $(this).closest('.sectionInfo-c-content').find('.sectionLink').attr('href');
		});

		$('.joTooltip').click(function() {
			$(this).closest('.content-wrapper').find('.img-description').collapse('toggle');
			/* var carousel = $(this).closest('.content-wrapper').find('.carousel');
			if($(this).hasClass('active')) {
				carousel.carousel('cycle');
			} else {
				carousel.carousel('pause');
			} */
			$(this).toggleClass('active');
		});

		/*
		$(".joObject .img-container").hover(function() {
		$(this).parent().addClass('hover');
	}, function() {
		$(this).parent().removeClass('hover');
	});

		var working = false;
	$(".joObject .img-container").click(function() {
		if(!working) {
			working = true;
			
			var $joobject = $(this).closest('.joObject');
			if($joobject.hasClass('clicked')) {
				$joobject.addClass('loading');
				setTimeout(function() {
		    		$joobject.removeClass('loading');
			  	}, 1000);
			  	$joobject.find('.section_img_info-c').removeClass('active');
			}

			$joobject.toggleClass('clicked').promise().done(function() {
				$('body').toggleClass('noscroll');
			});

			setTimeout(function() {
	    		working = false;
		  	}, 2000);
		}
	});
	*/

	if($(".joObject .img-container .section_img").length) {
		$(".joObject .img-container .section_img").each(function(i, v) {
			$that = $(this);
			$par = $that.closest('.img-container').parent();
			if ($par.hasClass('view_2_alt')) {
				this.style.top = '0';
				this.style.left = '0';
			} else {
				var img = $that.find('img');
				if (img.length) {
					img = img[0];
					if (img.complete) {
						var width = this.clientWidth;
						var height = this.clientHeight;

						this.style.top = 'calc(50% - ' + height/2 + 'px)';
						// this.style.left = 'calc(50% - ' + width/2 + 'px)';
						this.style.left = '0px)';
					} else {
						$that.on('load', function() {
			                var width = this.clientWidth;
							var height = this.clientHeight;

							this.style.top = 'calc(50% - ' + height/2 + 'px)';
							// this.style.left = 'calc(50% - ' + width/2 + 'px)';
							this.style.left = '0px)';
			            });
					}
				}
			}
		});
	}

	var dragging = false;
	var working = false;
	$(".joObject .img-container .section_img").click(function() {
		if(working || dragging) return false;

		working = true;

		var $that = $(this);

		$par = $that.closest('.img-container').parent();

		var $joobject = $that.closest('.joObject');

		$joobject.toggleClass('clicked');

		var is_activ = $that.hasClass('zoomable');

		if($that.data('ui-draggable')) {
            
        } else {
        	var __dx;
			var __dy;
			var __recoupLeft, __recoupTop;

        	$that.draggable({
        		drag: function (event, ui) {
			        __dx = ui.position.left - ui.originalPosition.left;
			        __dy = ui.position.top - ui.originalPosition.top;

			        ui.position.left = ui.originalPosition.left + (__dx);
			        ui.position.top = ui.originalPosition.top + (__dy);

			        ui.position.left += __recoupLeft;
			        ui.position.top += __recoupTop;
			    },
	      		start: function(event, ui) {
			        var left = parseInt($(this).css('left'), 10);
			        left = isNaN(left) ? 0 : left;

			        var top = parseInt($(this).css('top'), 10);
			        top = isNaN(top) ? 0 : top;

			        __recoupLeft = left - ui.position.left;
			        __recoupTop = top - ui.position.top;

	      			dragging = true;
	      			working = true;
		      	},
		      	stop: function() {
		      		setTimeout(function() {
			    		dragging = false;
			    		working = false;
				  	}, 300);
		      	}
		    });
        }

	    if(is_activ) {
	    	$that.removeClass('zoomable');

	    	var imgWidth = $that.width();
			var imgHeight = $that.height();

			if ($par.hasClass('view_2_alt')) {
				$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', '0').css('right', 'unset').css('left', '0');
			} else {

				if ($joobject.hasClass('even')) {
					$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('right', '0').css('left', 'unset');
				} else {
		    		$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('right', 'unset').css('left', '0');
				}
			}

	    	setTimeout(function() {
				imgWidth = $that.width();
				imgHeight = $that.height();

				if ($par.hasClass('view_2_alt')) {
					$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', '0').css('right', 'unset').css('left', '0');
				} else {

					if ($joobject.hasClass('even')) {
						$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('right', '0').css('left', 'unset');
					} else {
	    				$that.data('scr_pos', 1).data('scale', 1).css('transform', 'scale(1)').css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('right', 'unset').css('left', '0');
			    	}
				}
	    	}, 300);

	    	$that.draggable('disable');
	    	$('.joFixed').css('top', '0');
	    	currentlyZooming = false;
	    	$joobject.find('.section_img_info-c').show();
	    } else {
	    	$that.addClass('zoomable');
			$that.draggable('enable');
	    	$('.joFixed').css('top', '-' + $('.joFixed').outerHeight() + 'px');
	    	currentlyZooming = true;

			var $tmpimg = $that;

			if (!$that.is('img')) {
				var $tmpimg = $that.find('img');
			}

	    	var newUrl = $tmpimg.data('url');
	    	var isLoaded = $tmpimg.data('loaded');

			if(typeof newUrl != 'undefined' && newUrl != '' && typeof isLoaded == 'undefined') {
				$tmpimg.addClass('joFade').delay(550).promise().done(function() {
					$img_loader = $joobject.find('.img-load-overlay');
					$img_loader.show();
					$tmpimg.attr('src', newUrl).load(function() {
						var w = this.naturalWidth / 3;
						var h = this.naturalHeight / 3;
						$(this).css('width', w).css('height', h);

					    $that.removeClass('joFade');
					    $that.data('loaded', 'y');

					    var imgWidth = $that.width();
						var imgHeight = $that.height();

						if ($joobject.hasClass('even')) {
							if ($joobject.find('.view_4_alt').length) {
								$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', 'unset').css('right', '0px');
							} else {
								$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', 'unset').css('right', imgWidth/2 + 'px');
							}
						} else {
							if ($joobject.find('.view_4_alt').length) {
								$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', '0px');
							} else {
	    		    			$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', imgWidth/2 + 'px');
							}
						}
					    $img_loader.hide();
					});
				});
			} else {
				setTimeout(function() {
					var imgWidth = $that.width();
					var imgHeight = $that.height();

					if ($joobject.hasClass('even')) {
						if ($joobject.find('.view_4_alt').length) {
							$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', 'unset').css('right', '0px');
						} else {
							$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', 'unset').css('right', imgWidth/2 + 'px');
						}
					} else {
						if ($joobject.find('.view_4_alt').length) {
							$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', '0px');
						} else {
    		    			$that.css('top', 'calc(50% - ' + imgHeight/2 + 'px)').css('left', imgWidth/2 + 'px');
						}
					}
		    	}, 300);
			}

			$joobject.find('.section_img_info-c').hide();
	    }

		setTimeout(function() {
    		working = false;
	  	}, 500);
	});

	var zoom = 0;
	function zoomImg($img, delta, e) {
	    var deep = $img.data('scr_pos');

	    var imgWidth = $img[0].clientWidth;
		var imgHeight = $img[0].clientHeight;

	    if(typeof deep == 'undefined' || deep < 1 || deep > 22) deep = 1;

	    if (delta > 1) {
	        if(deep < 22) {
	            var scale = 1.0;

	            if(typeof $img.data('scale') != 'undefined') {
	            	scale = $img.data('scale');
	            }

	            scale += 0.1;

	            $img.css('transform', 'scale(' + scale + ')');

	            $img.data('scr_pos', deep+1);
	            $img.data('scale', scale);
	        }
	    } else {
	        if(deep > 1) {
	            var scale = 1.0;

	            if(typeof $img.data('scale') != 'undefined') {
	            	scale = $img.data('scale');
	            }

	            scale -= 0.1;
	            $img.css('transform', 'scale(' + scale + ')');

	            $img.data('scr_pos', deep-1);
	            $img.data('scale', scale);
	        }
	    }
	}

	$('.joObject_container').on('wheel', '.joObject .img-container .section_img.zoomable', function(e) {
	    e.preventDefault();

	    var delta = e.originalEvent.deltaY;

	    zoomImg($(this), delta, e);
	});

	$('.img_zoom_in').click(function(e) {
    	zoomImg($(this).closest('.joObject').find('.section_img.zoomable'), 10, e);
    });

    $('.img_zoom_out').click(function(e) {
    	zoomImg($(this).closest('.joObject').find('.section_img.zoomable'), -10, e);
    });

    $('.closescreen').click(function(e) {
    	$(this).closest('.img-container').find('.section_img').trigger('click');
    });


 	$('*[data-toggle="collapse-next"]').click(function() {
        $(this).next().collapse('toggle');
        $(this).toggleClass('active');
    })

	var working2 = false;
	$('.section_img_btn').click(function(e) {
		e.stopPropagation();

		if(!working2) {
			working2 = true;

			$(this).prev('.section_img_info-c').toggleClass('active');

			setTimeout(function() {
	    		working2 = false;
		  	}, 500);
		}

	});

		/* AOS.init({
	  duration: 1200,
	}); */

	var ic = $('.sitemap-icon-container');
    if(ic.length) {
    	$('.sitemap-icon-container').fadeIn(300);
    }

	var ic2 = $('.section.isFirst');
    if(ic2.length) {
    	ic2.delay(400).addClass('isFirstActive');
    }

    /* var ic3 = $('.section.active .carousel');
    if(ic3.length) {
    	ic3.carousel('cycle');
    } */

    
    var ic4 = $('.joTooltip');
    if(ic4.length) {
    	ic4.filter(function(i,v) { return $(this).closest('.content-wrapper').find('.img-description').length > 0; }).show();
    }


    function parralaxSlider(path) {
	    var section = $(path);
	    section.each(function(i, v) {
	    	$(this).css('z-index', (section.length + 1) - i);
	    });

	    // ------------- VARIABLES ------------- //
		var ticking = false;
		var isFirefox = (/Firefox/i.test(navigator.userAgent));
		var isIe = (/MSIE/i.test(navigator.userAgent)) || (/Trident.*rv\:11\./i.test(navigator.userAgent));
		var scrollSensitivitySetting = 30; //Increase/decrease this number to change sensitivity to trackpad gestures (up = less sensitive; down = more sensitive) 
		var slideDurationSetting = 600; //Amount of time for which slide is "locked"
		var currentSlideNumber = 0;
		var totalSlideNumber = section.length;

		// ------------- DETERMINE DELTA/SCROLL DIRECTION ------------- //
		function parallaxScroll(evt, distance = 0, swipe = false) {
			if(currentlyZooming || currentlyFullscreen) return false;

			setTimeout(function() {
			  if (isFirefox) {
			    //Set delta for Firefox
			    delta = evt.detail * (-120);
			  } else if (isIe) {
			    //Set delta for IE
			    delta = -evt.deltaY;
			  } else if(swipe) {
			  	delta = distance;
			  } else {
			    //Set delta for all other browsers
			    delta = evt.wheelDelta;
			  }

			  if (ticking != true) {
			    if (delta <= -scrollSensitivitySetting) {
			      //Down scroll
			      ticking = true;
			      if (currentSlideNumber !== totalSlideNumber - 1) {
			        currentSlideNumber++;
			        nextItem();
			      }
			      slideDurationTimeout(slideDurationSetting);
			    }
			    if (delta >= scrollSensitivitySetting) {
			      //Up scroll
			      ticking = true;
			      if (currentSlideNumber !== 0) {
			        currentSlideNumber--;
			      }
			      previousItem();
			      slideDurationTimeout(slideDurationSetting);
			    }

			    var id = $(path).eq(currentSlideNumber).attr('id');
			    $('.sitemap-slick-active').removeClass('sitemap-slick-active');
  				$('.slick-slider [data-target="#' + id + '"]').addClass('sitemap-slick-active');

			    // carouselCycle();
			  }
			}, 60);
		}

		// ------------- SET TIMEOUT TO TEMPORARILY "LOCK" SLIDES ------------- //
		function slideDurationTimeout(slideDuration) {
		  	setTimeout(function() {
	    		ticking = false;
		  	}, slideDuration);
		}

		// ------------- ADD EVENT LISTENER ------------- //
		var mousewheelEvent = isFirefox ? "DOMMouseScroll" : "wheel";
		window.addEventListener(mousewheelEvent, parallaxScroll, false);
		//$(parentPath).on(mousewheelEvent, parallaxScroll);
		
		$('body').swipe( {
		    swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
		    	var swipe = true;
		    	if (direction == 'up') {
		      		parallaxScroll(event, -distance, swipe);
		    	}
		    	if (direction == 'down') {
		      		parallaxScroll(event, distance, swipe);
		    	}
		    }
	  	});

  		$(document).on('keydown', function(e) {
  			var swipe = true;
		    // oben 
		    if (e.keyCode == 38 || e.keyCode == 87) {
		    	parallaxScroll(event, 100, swipe);
		    }

		    // unten
		    if (e.keyCode == 40 || e.keyCode == 83) {
		    	parallaxScroll(event, -100, swipe);
		    }
	  	});

		// ------------- SLIDE MOTION ------------- //
		function nextItem() {
	  		var $previousSlide = $(path).eq(currentSlideNumber - 1);
	  		if($previousSlide.length) {
			  	$previousSlide.removeClass("up-scroll").addClass("down-scroll");
			  	$(path + '.active').removeClass('active'); //find('.carousel').carousel('pause');
			  	$(path).eq(currentSlideNumber).addClass('active'); //find('.carousel').carousel('cycle');
			  	// hide header
			  	$('.joFixed').css('top', '-' + $('.joFixed').outerHeight() + 'px');
	  		}
		}

		function previousItem() {
		  	var $currentSlide = $(path).eq(currentSlideNumber);
		  	if($currentSlide.length) {
			  	$currentSlide.removeClass("down-scroll").addClass("up-scroll");
			  	$(path + '.active').removeClass('active'); //find('.carousel').carousel('pause');
			  	$(path).eq(currentSlideNumber).addClass('active'); //find('.carousel').carousel('cycle');
			  	// show header
			  	$('.joFixed').css('top', '0');
			  }
		}

		var carouselInterval;
	    function carouselCycle() {
	    	clearInterval(carouselInterval);
	    	$('.carousel .carousel-indicators').removeClass('active');
	    	var currentSlide = $(path).eq(currentSlideNumber);
	    	var carousel = currentSlide.find('.carousel');
	    	if(carousel.length) {
	    		carousel.find('.carousel-indicators').addClass('active');
	    		/* carouselInterval = setInterval(function() {
	    			var width = carousel.find('.carousel-indicators li.active::after').attr('width');
	    			console.log(width);
	    		}, 100); */
	    		carouselInterval = setInterval(function() {
	    			carousel.carousel('next');
	    		}, 5000);
	    	}
	    }

	    $('.section_container .section .more-arrow').click(function() {
	    	var $section = $(this).closest('.section');
	    	if($section.hasClass('up-scroll')) {
	    		if (currentSlideNumber !== totalSlideNumber - 1) {
			        currentSlideNumber++;
			        nextItem();
		      	}
		      	slideDurationTimeout(slideDurationSetting);
	    	}
	    });

	    $('.joObject_container .joObject .more-arrow').click(function() {
	    	if (currentSlideNumber !== totalSlideNumber - 1) {
		        currentSlideNumber++;
		        nextItem();
      		}
	      	slideDurationTimeout(slideDurationSetting);
	    });
		
		$('.joSitemap-item, .sitemap-slick-item').click(function() {
			var $that = $(this);
  			var parent = $that.closest('.joSitemap-container');
  			if (parent.hasClass('section-sitemap')) {
  				// var index = $that.data('index');
  				var $item = $($that.data('target'));

  				if($item.length) {
	  				var index = $item.index();

	  				if (index > currentSlideNumber) {
	  					$item.prevAll().each(function(i,v) {
		  					var $target = $(v);
				  			$target.removeClass('up-scroll').addClass('down-scroll');
		  				});

			  			// hide header
			  			$('.joFixed').css('top', '-' + $('.joFixed').outerHeight() + 'px');
	  				}

	  				if (index < currentSlideNumber) {
	  					$item.nextAll().andSelf().each(function(i,v) {
		  					var $target = $(v);
				  			$target.removeClass('down-scroll').addClass('up-scroll');
		  				});

						// show header
			  			$('.joFixed').css('top', '0');
	  				}
  					
  					currentSlideNumber = index;
  				}

  				$('.sitemap-slick-active').removeClass('sitemap-slick-active');
  				$that.addClass('sitemap-slick-active');


  				$(path + '.active').removeClass('active');
		  		$(path).eq(currentSlideNumber).addClass('active');
  			} else {
	  			var item = $($(this).data('target'));
	  			if (item.length) {
		  			$('body,html').animate({
						scrollTop: item.offset().top
					}, 800);
	  			}
  			}
  		});

  		if(window.location.hash) {
  			var id = window.location.hash;
  			if(typeof id != 'undefined' && id != '') {
  				var $hashItem = $('.sitemap-slick-item[data-target="' + id + '"]');
  				if($hashItem.length) {
	  				var $item = $($hashItem.data('target'));
	  				
	  				if ($item.length) {
	  					var index = $item.index();

	  					$item.prevAll().each(function(i,v) {
		  					var $target = $(v);
				  			$target.removeClass('up-scroll').addClass('down-scroll');
		  				});
	  					
	  					currentSlideNumber = index;
	  				}

	  				$('.sitemap-slick-active').removeClass('sitemap-slick-active');
	  				$hashItem.addClass('sitemap-slick-active');


		    		$(path + '.active').removeClass('active');
		  			$(path).eq(currentSlideNumber).addClass('active');
  				}
  			}
  		}
    }

    $('.joSitemap-item, .sitemap-slick-item').click(function() {
		var $that = $(this);
		var parent = $that.closest('.joSitemap-container');
		var item = $($(this).data('target'));
		if (item.length) {
			$('body,html').animate({
				scrollTop: item.offset().top
			}, 800);
		}
	});

    /*
    if($('.section_container .section').length && !mobile) {
    	parralaxSlider('.section_container .section');
    	$('.back-to-top').hide();
    	$('body').css('overflow', 'hidden');
    }

	    if($('.joObject_container .joObject').length  && !mobile) {
    	parralaxSlider('.joObject_container .joObject');
    	$('.back-to-top').hide();
    	$('body').css('overflow', 'hidden');
    }
    */

	$('.exhibition.showexhibition .item.teasertext .more_link').click(function(e) {
		e.preventDefault();
		var item = $(this).closest('.item.teasertext').next();
		if(item.length) {
			$('body,html').animate({
			scrollTop: item.offset().top
		}, 800);
		}
	});


	$('.back').click(function() {
		window.history.back();
	});

	$('.summaryArrow').click(function() {
		var item = $(this).closest('.container').next();
		if(item.length) {
  			$('body,html').animate({
				scrollTop: item.offset().top
			}, 800);
		}
	});

	$('.learn_more').click(function() {
		var $cn_wrapper = $(this).closest('.content-wrapper');
		if ($cn_wrapper.hasClass('sl-effect-2')) {
			$cn_wrapper.find('.obj_txt').slideToggle();
		}
		$cn_wrapper.toggleClass('show_text');

		$cn_wrapper.removeClass('audio_pl').removeClass('video_pl');

		if ($(this).find('.audio_trigger').length) $cn_wrapper.addClass('audio_pl');
		if ($(this).find('.video_trigger').length) {
			$cn_wrapper.addClass('video_pl');

			setTimeout(function() {
	    		$cn_wrapper.find('.video-slick, .video-nav-slick').slick('refresh');
		  	}, 500);
		}

		if ($cn_wrapper.hasClass('show_text')) {
			$('html,body').animate({
		        scrollTop: $cn_wrapper.offset().top - 150
		    }, 600);
		}

		currentlyZooming = currentlyZooming ? false : true;
	});

	$('.obj_txt .close, .content-wrapper > .closer').click(function() {
		$(this).closest('.content-wrapper').removeClass('show_text').removeClass('audio_pl').removeClass('video_pl');
		currentlyZooming = false;
	});


	var currentlyFullscreen = false;

		/* Fullscreen open */
    function enterFullscreen(element) {
		if(element.requestFullscreen) {
			element.requestFullscreen();
		} else if(element.msRequestFullscreen) {  /* Safari */
			element.msRequestFullscreen();
		} else if(element.webkitRequestFullscreen) { /* IE11 */
			element.webkitRequestFullscreen();
		}
    }

    /* Fullscreen close */
    function closeFullscreen() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.webkitExitFullscreen) { /* Safari */
			document.webkitExitFullscreen();
		} else if (document.msExitFullscreen) { /* IE11 */
			document.msExitFullscreen();
		}
    }

    $('.togglefullscreen_ex').click(function(e) {
        $that = $(this);

        $main_content = $that.closest('.joObject');

        if($that.hasClass('in_fullscreen')) {
            closeFullscreen();
        } else {
            enterFullscreen($main_content[0]);
        }
    });

    // ESC close fullscreen
    document.addEventListener('fullscreenchange', changeHandler);
	document.addEventListener('webkitfullscreenchange', changeHandler);
	document.addEventListener('mozfullscreenchange', changeHandler);
	document.addEventListener('MSFullscreenChange', changeHandler);

	function changeHandler() {
		if ($('.joObject_container').length) {
			if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
	     		currentlyFullscreen = false;
		    } else {
		    	currentlyFullscreen = true;
		    }

			$main_content.toggleClass('obj_fullscreen')
	        $that.toggleClass('in_fullscreen');
	        $('.joTop').fadeToggle();
		}
	}

	$('.all_images .small_img').click(function(e) {
		var $that = $(this);

		if($that.hasClass('active')) return false;

		var $img_container = $that.closest('.img-container');

		var $target = $img_container.find('.section_img');
		var $target_img = $img_container.find('.section_img img');
		var newUrl = $that.data('url');

		var info = $that.data('info');

		if($target.length && typeof newUrl != 'undefined' && newUrl != '') {
			$asset = $img_container.prev('.view2-overlay').find('.asset_desc');
			if ($asset.length) {
				$asset.html(info);
			}

			$target.addClass('joFade').delay(550).promise().done(function() {
				$img_loader = $that.closest('.img-container').find('.img-load-overlay');
				$img_loader.show();

				$target_img.attr('src', newUrl).load(function() {
					var w = this.naturalWidth / 3;
					var h = this.naturalHeight / 3;
					$target_img.data('url', newUrl).css('width', w).css('height', h);
					$target.removeClass('joFade');
				    $img_loader.hide();
				});

				$that.parent().find('.active').not(this).removeClass('active');
				$that.addClass('active');
			});
		}
	});

	$('.overview-link').click(function(e) {
		e.preventDefault();
		var $that = $(this);
		var href = $that.attr('href');

		if (typeof href == 'undefined' || href == '') {
			return false;
		}

		var $container = $('#overview-container');
		if ($container.length) {
			if (typeof $container.data('loaded') != 'undefined') {
				if($container.hasClass('active')) {
					$container.removeClass('active').fadeOut();
					currentlyFullscreen = false;
					$('body').removeClass('overflow-hidden');
				} else {
					$container.addClass('active').fadeIn();
					currentlyFullscreen = true;
					$('.joFixed').css('top', '-' + $('.joFixed').height() + 'px');
					$('body').addClass('overflow-hidden');
				}

				return false;
			}

			$loader.show();
			$.get(href, function(data) {
				$container.html(data).promise().done(function() {
					$loader.hide();
					$container.data('loaded', 'y').addClass('active').fadeIn();
					$('.joFixed').css('top', '-' + $('.joFixed').height() + 'px');
					currentlyFullscreen = true;
					$('body').addClass('overflow-hidden');
				});
			}).fail(function() {
				$loader.hide();
			});
		}
	});

	$('body').on('click', '.overview-closer', function() {
		var $container = $('#overview-container');
		$container.removeClass('active').fadeOut();
		currentlyFullscreen = false;
		$('body').removeClass('overflow-hidden');
	});

	audioplayerAction = function() {
		var $audioParent = $('.audio-item');

		if ($audioParent.length) {
			$audioParent.each(function(i, v) {
				var $that = $(this);

				if ($that.hasClass('has-loaded')) return true;

				var $audioBack = $that.find('.audio-btn-back');
				var $audioStart = $that.find('.audio-btn-start');
				var $audioForw = $that.find('.audio-btn-forw');
				var $audioPlayer = $that.find('.audio-player');
				var $progress = $that.find('.progress');
				var $progressbar = $that.find('.progress-bar');
				var $progressText = $that.find('.progress-text');
				
				if ($audioBack.length && $audioStart.length && $audioForw.length && $audioPlayer.length) {
					var player = $audioPlayer[0];

					$audioBack.click(function() {
						var currTime = player.currentTime;

						currTime -= 10;
						if (currTime < 0) {
							currTime = 0;
						}

						player.currentTime = currTime;
					});
					
					$audioForw.click(function() {
						var currTime = player.currentTime;

						currTime += 10;
						if (currTime > player.duration) {
							currTime = player.duration;
						}

						player.currentTime = currTime;
					});

					$audioStart.click(function() {
						if (player.paused) {
							$audioParent.not($that).each(function() {
								var $tmpStart = $(this).find('.audio-btn-start');
								if ($tmpStart.length && $tmpStart.hasClass('playing')) $tmpStart.trigger('click');
							});
							player.play();
							$audioStart.addClass('playing');
							$that.addClass('playing');
							$that.parent().addClass('plr');
						} else {
							player.pause();
							$audioStart.removeClass('playing');
							$that.removeClass('playing');
							$that.parent().removeClass('plr');
						}
					});

					/*
					 *	calc seconds to video time output hh:mm:ss 
					 *  mostly mm:ss
					 */
					function secondsToTime(seconds) {
						var date = new Date(seconds * 1000);
						var hours = date.getHours() - 1;

						if(hours <= 0) hours = '';
						else hours += ':';

						var min = date.getMinutes();
						var sec = date.getSeconds();

						min = min < 10 ? '0' + min : min;
						sec = sec < 10 ? '0' + sec : sec;

						return hours + '' + min + ':' + sec;
					}

					player.addEventListener('timeupdate', function(e) {
						var percent = (player.currentTime / player.duration) * 100;
						$progressbar.css('width', percent + '%');

						var curDate = secondsToTime(player.currentTime);
						var maxDate = secondsToTime(player.duration);

						$progressText.html(curDate + ' / ' + maxDate);
					});

					$progress.click(function(e) {
						var currTime = (e.offsetX / $progress.width()) * player.duration;

						if (currTime < 0) {
							currTime = 0;
						}

						if (currTime > player.duration) {
							currTime = player.duration;
						}

						player.currentTime = currTime;

						if (player.paused) player.play();
					});

					$that.addClass('has-loaded');
				}
			});
		}
	}

	audioplayerAction();

	//var TheBook = (function() {
	function TheBook(config) {
		/*
		var config = {
			$bookBlock : $block,
			$navNext : $out.find('.bb-nav-next'),
			$navPrev : $out.find('.bb-nav-prev'),
			$navFirst : $out.find('.bb-nav-first'),
			$navLast : $out.find('.bb-nav-last')
		};
		*/
		this.config = config;
		this.init = function() {
			var $slides = this.config.$bookBlock.children();
			var max = $slides.length;

			this.config.$bookBlock.bookblock({
				speed : 1000,
				shadowSides : 0.8,
				shadowFlip : 0.4,
				onEndFlip : function(old, page, isLimit) {
					if (isLimit && page == 0) {
						config.$navPrev.addClass('inactiv');
						config.$navFirst.addClass('inactiv');
					} else {
						config.$navPrev.removeClass('inactiv');
						config.$navFirst.removeClass('inactiv');
					}

					if (isLimit && page != 0) {
						config.$navNext.addClass('inactiv');
						config.$navLast.addClass('inactiv');
					} else {
						config.$navNext.removeClass('inactiv');
						config.$navLast.removeClass('inactiv');
					}

					var tmppage = 0;
					
					if (page <= 0 || page - 5 < 0) tmppage = 0;
					else tmppage = page - 5;

					for (var i = tmppage; i < tmppage + 10; i++) {
						if (i <= max) {
							var $item = $($slides[i]);
							$item.children().each(function() {
								var $slide = $(this);
								if (!$slide.hasClass('loaded')) {
									var href = $slide.data('href');
									$slide.addClass('loaded').children('a').append('<img class="bookimg" src="' + href + '" />');
								}
							});
						}
					}

					return false;
				},
				onBeforeFlip : function(page) {
					/*
					console.log('flip');
					console.log(page);
					if (page <= 0 || page - 3 < 0) page = 0;
					else page -= 5;

					console.log(page);

					for (var i = page; i < page + 8; i++) {
						var $item = $($slides[i]);
						$item.children().each(function() {
							var $slide = $(this);
							if (!$slide.hasClass('loaded')) {
								var href = $slide.data('href');
								$slide.children('a').append('<img class="bookimg" src="' + href + '" />');
							}
						});
					}
					*/

					return false;
				}
			});
			this.initEvents();
		};
		this.initEvents = function() {
			
			var $slides = this.config.$bookBlock.children();

			// add navigation events
			this.config.$navNext.on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('inactiv')) config.$bookBlock.bookblock('next');
				return false;
			});

			this.config.$navPrev.on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('inactiv')) config.$bookBlock.bookblock('prev');
				return false;
			});

			this.config.$navFirst.on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('inactiv')) config.$bookBlock.bookblock('first');
				return false;
			});

			this.config.$navLast.on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('inactiv')) config.$bookBlock.bookblock('last');
				return false;
			});

			this.config.$bookBlock.find('.bb-item .bb-custom-side:first-child').on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('first')) config.$bookBlock.bookblock('prev');
				return false;
			});

			this.config.$bookBlock.find('.bb-item .bb-custom-side:last-child').on('click touchstart', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('last')) config.$bookBlock.bookblock('next');
				return false;
			});
			
			// add swipe events
			/*
			$slides.on({
				'swipeleft' : function( event ) {
					console.log('swipeleft');
					config.$bookBlock.bookblock('next');
					return false;
				},
				'swiperight' : function( event ) {
					console.log('swiperight');
					config.$bookBlock.bookblock('prev');
					return false;
				}
			});
			*/

			// add keyboard events
			$(document).keydown(function(e) {
				var keyCode = e.keyCode || e.which,
					arrow = {
						left : 37,
						up : 38,
						right : 39,
						down : 40
					};

				switch (keyCode) {
					case arrow.left:
						config.$bookBlock.bookblock('prev');
						break;
					case arrow.right:
						config.$bookBlock.bookblock('next');
						break;
				}
			} );
		};
		this.init();
		//return { init : init };
	}		

	//})();

	if ($('.bb-bookblock').length) {
		// TheBook.init();
	}

	var books = [];
	$('body').on('click', '.book-link', function(e) {
		e.preventDefault();
		var $that = $(this);

		var $wrapper = $that.closest('.content-wrapper');
		var $out = $wrapper.find('.book-out');

		if ($out.hasClass('loaded')) {
			$out.addClass('active');
			$wrapper.find('.book-link-con').hide().parent().addClass('ovl');
		}

		var href = $that.attr('href');

		$loader.show();

		$.get(href, function(data) {
			$loader.hide();

			$wrapper.find('.book-link-con').hide().parent().addClass('ovl');

			$out.html(data).promise().done(function() {
				$loader.hide();

				var $block = $out.find('.bb-bookblock');

				if($block.length) {
					$out.addClass('active loaded');
					var config = {
						$bookBlock : $block,
						$navNext : $out.find('.bb-nav-next'),
						$navPrev : $out.find('.bb-nav-prev'),
						$navFirst : $out.find('.bb-nav-first'),
						$navLast : $out.find('.bb-nav-last')
					};
					var book = new TheBook(config);
					//book.init(config);
					books.push(book);
				}

				$map_cl = $('.map-closer');
				if ($map_cl.length) $map_cl.hide();
			});
		});

		return true;
	});

	$('body').on('click', '.book-closer', function(e) {
		$out = $(this).closest('.book-out');
		if ($out.hasClass('loaded') && $out.hasClass('active')) {
			$out.removeClass('active');
			$out.prev('.book-link-con').show().parent().removeClass('ovl');
		}
		$map_cl = $('.map-closer');
		if ($map_cl.length) $map_cl.show();
	});


	// aus project_base
	var $loader = $('#joAjaxloader');

	var prevScrollpos = window.pageYOffset;
	$(window).scroll(function() {

	  	var currentScrollPos = window.pageYOffset;

	  	if (prevScrollpos > currentScrollPos) {
	  		$('.joFixed').css('top', '0');
	  	} else {
	  		$('.joFixed').css('top', '-' + $('.joFixed').outerHeight() + 'px');
	  	}

	  	prevScrollpos = currentScrollPos;

	  	var windowBottom = $(this).scrollTop() + $(this).innerHeight();
		$(".jofade").each(function() {
		  	/* Check the location of each desired element */
	  		var objectBottom = $(this).offset().top + $(this).outerHeight() / 4;

	  		/* If the element is completely within bounds of the window, fade it in */
	  		if (objectBottom < windowBottom) { //object comes into view (scrolling down)
		    	if ($(this).css("opacity")==0) {$(this).fadeTo(500,1);}
  			} else { //object goes out of view (scrolling up)
		    	if ($(this).css("opacity")==1) {$(this).fadeTo(500,0);}
	  		}
		});
	});

	var $carousel = $('.carousel-sitemap-slick');
	if ($carousel.length) {
		/*
		$carousel.on('init', function(event, slick) {
			if(slick.currentSlide == 0) {
				$('.carousel-sitemap-slick .slick-prev').hide();
			} else {
				$('.carousel-sitemap-slick .slick-prev').show();
			}
		});

		$carousel.on('afterChange', function(event, slick) {
			var slidesToShow = slick.options.slidesToShow;

			if (slick.activeBreakpoint != null) {
				slidesToShow = slick.options.responsive[0].settings.slidesToShow;
			}

			if (slick.currentSlide == 0) {
				$('.carousel-sitemap-slick .slick-prev').hide();
			} else {
				$('.carousel-sitemap-slick .slick-prev').show();
			}

			if (slick.currentSlide + slidesToShow < slick.slideCount) {
				$('.carousel-sitemap-slick .slick-next').show();
			} else {
				$('.carousel-sitemap-slick .slick-next').hide();
			}
		});
		*/

		$carousel.slick({
	  		infinite: false,
	  		slidesToShow: 5,
	  		slidesToScroll: 5,
	  		responsive: [
			    {
		      		breakpoint: 500,
			      	settings: {
			        	slidesToShow: 1,
			        	slidesToScroll: 1
			      	}
			    },
			    {
		      		breakpoint: 768,
			      	settings: {
			        	slidesToShow: 2,
			        	slidesToScroll: 2
			      	}
			    },
			    {
		      		breakpoint: 992,
			      	settings: {
			        	slidesToShow: 3,
			        	slidesToScroll: 3
			      	}
			    }
	  		]
		});
	}

	$('.exhibition.showexhibition .item.teasertext .joOpener').click(function(e) {
		e.preventDefault();
		var item = $(this).closest('.item.teasertext').next();
		if(item.length) {
			$('body,html').animate({
				scrollTop: item.offset().top
			}, 800);
		}
	});

	$('body').on('click', '.showindex .section_li a', function() {
		var href = $(this).attr('href');

		if (href.includes('#')) {
			var pathname = window.location.pathname;
			var search = window.location.search;
			var href2 = href.split('#')[0];

			if ((pathname + search) == href2) {
				$('.showindex .overview-closer').trigger('click');
			}
		}
	});

});
