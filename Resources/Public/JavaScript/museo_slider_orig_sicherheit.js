jQuery(document).ready(() => {
    
	/* joSlider
	* 
	*/
	(function ($) {
		var pluginName = 'joSlider';
		var defaults = {
		  'lSlSel' : '.joStartSlider',
		  'rSlSel' : '.joEndSlider',
		  'bgSlSel' : '.joBgSlider',
		  'lSl' : null,
		  'rSl' : null,
		  'bgSl' : null,
		  'complete': function() {},
          'step' : function() {},
          'element' : this,
          'moutElement' : null
		}
		var internals = {
			'lSlClass' : 'joStartSlider',
			'rSlClass' : 'joEndSlider',
			'activeElement': null,
			'inactiveElement': null,
			'activeDirection': null,
			'initialX': null,
			'touchDirection': null
		}

		function joSlider (element, options) {
			var _this = this;
			this.element = $(element);
			/* settings by defaults and options */
			this.settings = $.extend({}, defaults, options, internals);
			// initialize funtions
			this._init = function(e) {
				_this.buildSlider();
			}
			this.buildSlider = function() {
				_this.settings.lSl = _this.element.find(_this.settings.lSlSel);
				_this.settings.rSl = _this.element.find(_this.settings.rSlSel);
				_this.settings.lSlClass = _this.settings.lSlSel.split('.').pop();
				_this.settings.lSlClass = _this.settings.lSlClass.split('#').pop();
				_this.settings.rSlClass = _this.settings.rSlSel.split('.').pop();
				_this.settings.rSlClass = _this.settings.rSlClass.split('#').pop();
				_this.settings.bgSl = _this.element.find(_this.settings.bgSlSel);
				_this.settings.lSl.unbind('mousedown', _this.slideStartByMouse);
				_this.settings.lSl.bind('mousedown', _this.slideStartByMouse);							
				_this.settings.rSl.unbind('mousedown', _this.slideStartByMouse);
				_this.settings.rSl.bind('mousedown', _this.slideStartByMouse);

				if (_this.settings.moutElement == null) _this.settings.moutElement = _this.element;

				$(_this.settings.moutElement).unbind('mouseleave', _this.stopByMouse);
				$(_this.settings.moutElement).bind('mouseleave', _this.stopByMouse);
                
				$(window).unbind('click', _this.stopByMouse);
				$(window).bind('click', _this.stopByMouse);
				$(window).unbind('mouseup', _this.stopByMouse);
				$(window).bind('mouseup', _this.stopByMouse);

				/* mobile support */
				jQuery.support.touch = 'ontouchend' in document;
				if (jQuery.support.touch) {
					_this.settings.lSl.addClass('joMobile');
					_this.settings.rSl.addClass('joMobile');
				}	
				_this.settings.lSl.unbind('touchstart', _this.slideStartByTouch);
				_this.settings.lSl.bind('touchstart', _this.slideStartByTouch);
				_this.settings.rSl.unbind('touchstart', _this.slideStartByTouch);
				_this.settings.rSl.bind('touchstart', _this.slideStartByTouch);

              	_this.settings.lSl.unbind('touchend touchcancel', _this.slideEndByTouch);
              	_this.settings.lSl.bind('touchend touchcancel', _this.slideEndByTouch);
              	_this.settings.rSl.unbind('touchend touchcancel', _this.slideEndByTouch);
              	_this.settings.rSl.bind('touchend touchcancel', _this.slideEndByTouch);
	            
	            _this.settings.lSl.bind('touchmove', (e) => {
	              _this.evtByTouch(e);
	            });
	            _this.settings.rSl.bind('touchmove', (e) => {
	              _this.evtByTouch(e);
	            });
	            /* mobile support end */
			}

			this.slideStartByTouch = function(e) {
				_this.settings.initialX = e.originalEvent.changedTouches[0].pageX;
                $(this).trigger( "mousedown" );
                 _this.settings.initialXSlider = parseInt(_this.settings.activeElement.css('left'));
			}
			this.slideEndByTouch = function(e) {
				_this.settings.initialX = null;
                _this.settings.touchDirection = null;
                $(this).trigger('mouseup')
                $(this).trigger('mouseup');
			}
			this.evtByTouch = function(e) {
                if (_this.settings.initialX != null) {
                	var posAct = e.originalEvent.changedTouches[0].pageX - _this.settings.initialX;
					var wBox = _this.element.width();
					var posCur = _this.settings.initialXSlider;
					var perc = (posAct + posCur) / wBox * 100;
					// nicht weiter als 0 oder 100%
					if (perc < 0) perc = 0;
					if (perc > 100) perc = 100;
					_this.settings.activeElement.css('left', perc + '%');
					var bgWidth = _this.element.width();
					// nicht weiter als anderer Slider
					if (_this.settings.inactiveElement != null) {
						if (_this.settings.activeDirection == 'left') {
							if (parseInt(_this.settings.inactiveElement.css('left'), 10) < parseInt(_this.settings.activeElement.css('left'), 10)) {
								_this.settings.activeElement.css('left', (parseInt(_this.settings.inactiveElement.css('left'), 10) - _this.settings.activeElement.width()));
							}
							bgWidth = parseInt(_this.settings.inactiveElement.css('left'),10) - parseInt(_this.settings.activeElement.css('left'), 10);
						} else {
							if (parseInt(_this.settings.inactiveElement.css('left'), 10) > parseInt(_this.settings.activeElement.css('left'), 10)){
								_this.settings.activeElement.css('left', (parseInt(_this.settings.inactiveElement.css('left'), 10) + _this.settings.activeElement.width()));
							}
							bgWidth = parseInt(_this.settings.activeElement.css('left'),10) - parseInt(_this.settings.inactiveElement.css('left'), 10);
						}
					}
					// bg setzen
					if (_this.settings.bgSl != null) {
						_this.settings.bgSl.width(bgWidth);
						if (_this.settings.lSl != null) _this.settings.bgSl.css('left', _this.settings.lSl.css('left'));
					}
                    
                    _this.settings.step(_this);
                }
			}
			this.slideStartByMouse = function(e) {
				if (_this.settings.activeElement == null) {
					if ($(this).hasClass(_this.settings.lSlClass)) {
						_this.settings.activeDirection = 'left';
						_this.settings.inactiveElement = _this.settings.rSl;
					}
					if ($(this).hasClass(_this.settings.rSlClass)) {
						_this.settings.activeDirection = 'right';
						_this.settings.inactiveElement = _this.settings.lSl;
					}
					_this.settings.activeElement = $(this);
					$(window).unbind('mousemove', _this.evtByMouse);
					$(window).bind('mousemove', _this.evtByMouse);
				}
			}
			this.evtByMouse = (e) => {
                if (_this.settings.activeElement != null) {
					// position box ermitteln
					var offL = _this.getboxPosition();
					var posAct = e.pageX - offL;
					var wBox = _this.element.width();
					var perc = posAct / wBox * 100;
					// nicht weiter als 0 oder 100%
					if (perc < 0) perc = 0;
					if (perc > 100) perc = 100;
					_this.settings.activeElement.css('left', perc + '%');
					var bgWidth = _this.element.width();
					// nicht weiter als anderer Slider
					if (_this.settings.inactiveElement != null) {
						if (_this.settings.activeDirection == 'left') {
							if (parseInt(_this.settings.inactiveElement.css('left'), 10) < parseInt(_this.settings.activeElement.css('left'), 10)) {
								_this.settings.activeElement.css('left', (parseInt(_this.settings.inactiveElement.css('left'), 10) - _this.settings.activeElement.width()));
							}
							bgWidth = parseInt(_this.settings.inactiveElement.css('left'), 10) - parseInt(_this.settings.activeElement.css('left'), 10);
						} else {
							if (parseInt(_this.settings.inactiveElement.css('left'), 10) > parseInt(_this.settings.activeElement.css('left'), 10)){
								_this.settings.activeElement.css('left', (parseInt(_this.settings.inactiveElement.css('left'), 10) + _this.settings.activeElement.width()));
							}
							bgWidth = parseInt(_this.settings.activeElement.css('left'), 10) - parseInt(_this.settings.inactiveElement.css('left'), 10);
						}
					}
					// bg setzen
					if (_this.settings.bgSl != null) {
						_this.settings.bgSl.width(bgWidth);
						if (_this.settings.lSl != null) _this.settings.bgSl.css('left', _this.settings.lSl.css('left'));
					}
                    _this.settings.step(_this);
				}
			}
			this.stopByMouse = (e) => {
				if (_this.settings.activeElement != null) {
					_this.settings.activeElement = null;
					_this.settings.inactiveElement = null;
					_this.settings.activeDirection = null;
					$(window).unbind('mousemove', _this.evtByMouse);
					_this.settings.complete();
				}
			}
			this.getboxPosition = () => {
                return _this.element.offset().left;
			}
			getStartEnd = () => {
				var posStart = 0;
				var posEnd = 0;
				var boxWidth = _this.element.width();
				if (_this.settings.lSl != null) {
					posStart = parseInt(_this.settings.lSl.css('left'), 10);
					posStart = posStart / boxWidth * 100;
				}
				if (_this.settings.rSl != null) {
					posEnd = parseInt(_this.settings.rSl.css('left'), 10);
					posEnd = posEnd / boxWidth * 100;
				}
				return [posStart,posEnd];
			}
			this._init();
		};
		// init plugin
		$.fn[pluginName] = function (options) {
			return this.each(function () {
				if (!$.data(this, 'plugin_' + pluginName)) {
					$.data(this, 'plugin_' + pluginName, 
					new joSlider( this, options ));
				}
			});
		}
	}(jQuery));

	var fn = () => {
	}

	$('ul.joObjekteTimeline').joSlider({'bgSlSel': '.joBgSliderFocus', 'moutElement': $('.joPageheadWrapper'), 'complete': fn, 'step': function(currentThis) {

        // left-Werte aus dem Jahr-Filter an GET Ã¼bergeben
        var startSliderLeft = parseFloat($('.joPageTimeline .joStartSlider').css('left'));
        var endSliderLeft = parseFloat($('.joPageTimeline .joEndSlider').css('left'));
        var fullSliderLength = parseFloat($('.joObjekteTimeline').width())
        startSliderLeft = startSliderLeft / fullSliderLength;
        endSliderLeft = endSliderLeft / fullSliderLength;
        var slider = 'end';
        var slideValue = endSliderLeft * parseFloat(timeline.headerdata['absoluterangeinyears']) + parseFloat(timeline.headerdata['startpointinyears']);
        if (currentThis.settings.activeElement.hasClass('joStartSlider')) {
            slider = 'start';
            slideValue = startSliderLeft * parseFloat(timeline.headerdata['absoluterangeinyears']) + parseFloat(timeline.headerdata['startpointinyears']);
        }
        
		/**
		 *	mod CR -> Positionen in Zeiteinheiten umrechnen
		 *	Referenz auf JSON Objekt timeline.headerdata, das im Controller gerendert wird
		 */
        $('.joSlideableYearContainer .content', currentThis.element).text(parseInt(slideValue));
    }});
	
	loadingLayer = (option) => {
		$('#joAjaxloader').show();
	}
	if (typeof(timeline) != 'undefined' && timeline != null && typeof(timeline.color) != 'undefined' && timeline.color != null) {
		var strColorfillcg = timeline.color.fillcg; // cg = colorgraph
		var strColorlinecg = timeline.color.linecg;
	}

    // Seitenreload für die Auswahl eines Filters
    setFilter = (e, option) => {        
        // left-Werte aus dem Jahr-Filter an GET Ã¼bergeben
        var startSliderLeft = parseFloat($('.joPageTimeline .joStartSlider').css('left'));
        var endSliderLeft = parseFloat($('.joPageTimeline .joEndSlider').css('left'));
        var fullSliderLength = parseFloat($('.joObjekteTimeline').width());
        
        startSliderLeft = startSliderLeft / fullSliderLength;
        endSliderLeft = endSliderLeft / fullSliderLength;
		// falschen Offset des rechten Sliders korrigieren nach Auswahl
		var offset = 0;
		if (endSliderLeft >= 1) offset = -1;
		/*
		console.log(endSliderLeft);
		console.log(startSliderLeft);
		console.log(fullSliderLength);
	*/
		/**
		 *	mod CR -> Positionen in Zeiteinheiten umrechnen
		 *	Referenz auf JSON Objekt timeline.headerdata, das im Controller gerendert wird
		 */
		startSliderLeft = startSliderLeft * parseFloat(timeline.headerdata['absoluterangeinyears']) + parseFloat(timeline.headerdata['startpointinyears']);
		endSliderLeft = endSliderLeft * parseFloat(timeline.headerdata['absoluterangeinyears']) + parseFloat(timeline.headerdata['startpointinyears']) + offset;

        var strNewUrl = extbase_config.baseurl;
        strNewUrl += "?id=" + extbase_config.currentPageId;
		if (extbase_config.additional_hash) strNewUrl += "&" + extbase_config.additional_hash;
        strNewUrl += "&" + extbase_config.action;
        strNewUrl += "&" + extbase_config.controller_name;
        strNewUrl += "&no_cache=1";
        if (extbase_config.ce_uid) {
        	strNewUrl += "&ceid=" + extbase_config.ce_uid;
        }
        if (extbase_config.pagetype > 0) {
         	strNewUrl += "&type=" + extbase_config.pagetype;
        }
        if (option != "notimeline") {
            strNewUrl += "&" + extbase_config.starttime + parseInt(startSliderLeft);
            strNewUrl += "&" + extbase_config.endtime + parseInt(endSliderLeft);
        }
        loadingLayer({'show': true});

        if ($('.joPageTimeline').hasClass('t200')) {
        	$('#joAjaxloader').show();
            $.get(strNewUrl).done(function(data) {
                $('.joPageTimeline').closest('.frame').html($(data)).promise().done(function() {
                    if (typeof initMapFuncIt == 'function')  initMapFuncIt();
                    if (typeof fc_drp_init == 'function') fc_drp_init();
                });
                $('#joAjaxloader').hide();
            });
        } else {
			location.href = strNewUrl;
        }
    }
	/**
	 *	Hintergrundgrafik für die Timeline
	 */
	var myChart = '';
	if (typeof(timeline) != 'undefined' && timeline != null) {
	  	if (timeline.items.value.length > 1) {
	  		var points = (timeline.items.value.length < 30)? 5 : 0;
		  	$('#chart_div').append('<canvas id="myChart"></canvas>');
		  	var myContext = $("#myChart");
		  	var myChartConfig = {
		      type: 'line',
		      data: {
				labels: timeline.items.label,
				datasets: [
					{
						fill: true,
						pointRadius: points,
						pointHitRadius: 5,
						data: timeline.items.value,
						backgroundColor: strColorfillcg,
						borderColor: strColorlinecg,
						borderWidth: 1.5
					}
				]
		      },
		      options: {
		        responsive: true,
		        maintainAspectRatio: false,
		        legend: {
		            display: false
		        },
		        scales: {
		            yAxes: [{
		                display: false
		            }],
		            xAxes: [{
		                display: false
		            }]
		        },
				tooltips: {
					displayColors: false
				}
		      }
		    }
		    myChart = new Chart(myContext, myChartConfig);
	  	}
	}

	showYear = function(e) {
        $('.joSlideableYearContainer').remove();
        var yearContainer = $('<div>', {'class': 'joSlideableYearContainer'});
        yearContainer.append($('<div>', {'class': 'ecke'}));
        yearContainer.append($('<div>', {'class': 'content'}));
        $(this).append(yearContainer);
        var currentYear = $('.joObjekteTimelineStartJahr').text();
		if ($(this).hasClass('joEndSlider')) currentYear = $('.joObjekteTimelineEndJahr').text();
        $('.content', yearContainer).text(currentYear);
    }
	$('.joStartSlider').off('mouseup', setFilter);
    $('.joStartSlider').on('mouseup', setFilter);

    $('.joEndSlider').off('mouseup', setFilter);
    $('.joEndSlider').on('mouseup', setFilter);

    $('.joStartSlider').off('mousedown', showYear);
    $('.joStartSlider').on('mousedown', showYear);

    $('.joEndSlider').off('mousedown', showYear);
    $('.joEndSlider').on('mousedown', showYear);
});