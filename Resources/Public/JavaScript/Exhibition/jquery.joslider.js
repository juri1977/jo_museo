/* joSlider
 * slides element dependent on given parameter
 * right-to-left or left-to-right or center
 * element will duplicated to prevent boxmodelcollapsing
 * build Slider from construct
 * <div class="joSlider"><ul><li></li>...<li></li></ul></div>
 * you can define own viewport
 * <div class="joSlider"><div class="viewport ownviewport"><ul><li></li>...<li></li></ul></div></div>
 */
(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		// AMD. Register as an anonymous module.
		define( [ 'jquery' ], factory );
	} else if ( typeof module === "object" && module.exports ) {
		// Node/CommonJS
		module.exports = factory(require("jquery"));
	} else {
		// Browser globals
		factory( jQuery );
	}
}(function ( $ ) {
    /* plugin name */
    var pluginName = 'joSlider';
    /* defaults */
    var defaults = {
	  'useRealWidth' : true,
      'liWidth' : null,
      'liWidthSel' : null,
      'speed' : 1000,
      'selVp' : '.viewport',
	  'offsetVp' : 0,
      'selUl' : 'ul',
      'selLi' : 'li',
      'selBl' : false,
	  'selBlUl' : 'ul.slideshow.bullets',
      'selBtnP' : '.joSliderButtons.prev',
      'selBtnN' : '.joSliderButtons.next',
	  'changeHeightArrows' : 0,
      'clearfix' : 'joClearfix',
      'ulPos' : null,
      'intervalTime' : 8000,
      'btnsBl' : 'li a',
      'fading' : false,
      'iScroll' : null,
      'autoSlider' : true,
      'type' : null,
      'setLowerHeightForMobile' : 0,
      'offMobileHeight' : 0,
      'offMobileSel' : null,
      'fnStart' : null,
      'fnComplete': null,
	  'lastIsFirst': true,
	  'fnInit' : null
    }
    var internals = {
      'window' : $(window),
      'document' : $(document),
      'activeLi' : 0,
      'cntElements' : 0,
      'isAnimated' : false,
      'thisTimer' : null,
      'initialX' : null,
      'touchDirection' : 0,
      'isSliding' : false,
      'elVp' : null,
      'elUl' : null,
      'elLi' : null,
      'elBlUl' : null,
      'outerMobileHeight' : 0,
      'outerMobileWidth' : 0,
	  'direction': 0,
	  'slideFn' : {
		'before' : function(){},
		'after' : function(){},		
	  }
    }
    function joSlider ( element, options ) {
        var _this = this;
        this.element = $(element);
        this.element.uniqueId();

        /* sets by defaults and options */
        this.sets = $.extend({}, defaults, options, internals);

        this.init = function(){
          if($(_this.sets.selLi).length>0 && _this.element.find(_this.sets.selLi).length>1){
            // build slider btns, viewports etc
            _this.buildSlider();
            _this.setLowerHeightForMobile();
            _this.buildMirrored();
            _this.lookForActive();

            _this.imgLoad(_this.element.find('img'),_this.sliderUpdate);

            if(_this.sets.elBlUl != null && _this.sets.elBlUl.length>0){
                _this.sets.elBlUl.find(_this.sets.btnsBl).unbind('click',_this.slideToPrStage);
                _this.sets.elBlUl.find(_this.sets.btnsBl).bind('click',_this.slideToPrStage);
            }
            _this.sets.ulPos = parseInt(_this.sets.elUl.css('left'));
            // start slider
            if(_this.sets.autoSlider)_this.startAutoSlider();
            _this.element.find('.joSliderButtons').unbind('click',_this.slidePrStage);
            _this.element.find('.joSliderButtons').bind('click',_this.slidePrStage);

            _this.sets.window.bind('DOMMouseScroll',_this.isScrolling);
            _this.sets.window.scroll(_this.isScrolling);

            _this.sets.window.unbind('resize',_this.windowResize);
            _this.sets.window.bind('resize',_this.windowResize);
          }
		  if(typeof _this.sets.fnInit == "function") _this.sets.fnInit();
        }

        this.buildSlider = function(){
		  _this.element.css('position','relative');
          if(_this.element.find(_this.sets.selUl).length>0){
            _this.sets.elUl = _this.element.find(_this.sets.selUl);
          }
          if($(_this.sets.selLi).length>0){
            _this.sets.elLi = _this.element.find(_this.sets.selLi);
          }
          /* viewport */
          if(_this.element.find(_this.sets.selVp).length>0){
            _this.sets.elVp = _this.element.find(_this.sets.selVp);
          }else{
            _this.sets.selVp = '.viewport';
            _this.sets.elUl.wrap( "<div class='viewport'></div>" );
            _this.sets.elVp = _this.element.find(_this.sets.selVp);
          }
		  _this.sets.elVp.after( '<div class="joSliderButtonsWrap"></div>' );
          /* button prev */
          if(_this.element.find(_this.sets.selBtnP).length>0){
            _this.sets.elBtnP = _this.element.find(_this.sets.selBtnP);
          }else{
            _this.sets.selBtnP = '.joSliderButtons.prev';
            _this.element.find('.joSliderButtonsWrap').append( '<a href="#" class="joSliderButtons prev"></a>' );
            _this.sets.elBtnP = _this.element.find(_this.sets.selBtnP);
          }
          /* button next */
          if(_this.element.find(_this.sets.selBtnN).length>0){
            _this.sets.elBtnN = _this.element.find(_this.sets.selBtnN);
          }else{
            _this.sets.selBtnN = '.joSliderButtons.next';
            _this.element.find('.joSliderButtonsWrap').append( '<a href="#" class="joSliderButtons next"></a>' );
            _this.sets.elBtnN = _this.element.find(_this.sets.selBtnN);
          }
          /* sliderbullets */
		  if(_this.sets.selBl){
			  if($(_this.sets.selBlUl).length>0){
				_this.sets.elBlUl = $(_this.sets.selBlUl);
			  }else{
				_this.sets.selBlUl = 'ul.slideshow.bullets';
				_this.sets.elBtnN.after( '<div class="bullets-wrap"><ul class="slideshow bullets joClearfix"></ul></div>' );
				_this.sets.elBlUl = _this.element.find(_this.sets.selBlUl);
				$.each(_this.sets.elLi,function(i,n){
				  _this.sets.elBlUl.append('<li><a href="#" class="bullet" data-slide="' + i + '"></a></li>');
				});
				_this.sets.elBlUl.find( "li" ).first().find('a').addClass('active');
			  }
		  }
        }
        this.setLowerHeightForMobile = function(){
          if(_this.sets.setLowerHeightForMobile != 0){
          	var vPH = _this.sets.elVp.innerHeight();
          	var wH = _this.sets.window.innerHeight();
            var wOuterH = (typeof window.screen != 'undefined' && typeof window.screen.height != 'undefined')? Math.max(window.screen.height, _this.sets.window.height()) : _this.sets.window.height();
            var wOuterW = _this.sets.window.outerWidth();
          	var img = _this.sets.elUl.find('li img');
          	if(_this.sets.offMobileSel != null && $(_this.sets.offMobileSel).length > 0){
      			 _this.sets.offMobileHeight = $(_this.sets.offMobileSel).innerHeight();
      		  }
          	var relHeight = wH-_this.sets.offMobileHeight;
            if(_this.sets.outerMobileHeight != wOuterH || _this.sets.outerMobileWidth != wOuterW){
              _this.sets.outerMobileHeight = wOuterH;
              _this.sets.outerMobileWidth = wOuterW;
              if(vPH >= relHeight && $('.bullets-wrap').is(':hidden')){            
                img.css({
            			'width' : 'auto',
            			'height' : relHeight + 'px',
        					'margin': '0 auto',
        					'float': 'none'
            		});
            	}else{
              	if(typeof(img.attr('style')) != 'undefined' && img.attr('style') != '')img.attr('style','');
            	}
            }
          }
        }
        this.setCssClasses = function(){
            _this.sets.elVp.css({
              'overflow' : 'hidden',
              'position' : 'relative',
              'width' : _this.element.innerWidth(),
              'height' : (_this.sets.elLi.innerHeight() < 5) ? _this.element.closest('.csc-default').innerHeight() + _this.sets.offsetVp : _this.sets.elLi.innerHeight() + _this.sets.offsetVp,
            });
            /*
            _this.sets.elBlUl.closest('.bullets-wrap').css({
              'width' : _this.element.innerWidth(),
            });
            */
            if(_this.sets.fading){
              _this.sets.elLi.css({
                'float' : 'none',
                'position' : 'absolute',
                'top' : 0,
                'left' : 0
              });
            }else{
				if(!_this.sets.useRealWidth){
					_this.sets.elUl.find('li').css({
					  'width' : _this.sets.elVp.css('width')
					});
				}
                _this.sets.liWidth = _this.sets.elLi.innerWidth();
                _this.sets.elUl.addClass(_this.sets.clearfix);
                _this.sets.elUl.css({
                  'width' : _this.sets.elUl.find('li').length * (_this.sets.elLi.innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left')))
                });
            }
        }

        this.buildMirrored = function(){
          _this.sets.cntElements = _this.sets.elUl.find('li').length;
          if(!_this.sets.fading){
            if(_this.sets.cntElements>1){
              if(!_this.sets.lastIsFirst){
				var firstLi = _this.sets.elUl.find('li').first().clone();
				var lastLi = _this.sets.elUl.find('li').last().clone();
				firstLi.addClass('mirrored first');
				firstLi.attr('id','joSlideM-'+_this.sets.cntElements);
				lastLi.addClass('mirrored last');
				lastLi.attr('id','joSlideM-0');
			  }
              if(_this.sets.liWidth == null)_this.sets.liWidth = _this.sets.elUl.find('li').innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left'));
              if(_this.sets.liWidthSel != null){
                _this.sets.liWidth = _this.sets.elUl.find('li').parents(_this.sets.liWidthSel).innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left'));
              }
              if(!_this.sets.lastIsFirst)_this.sets.elUl.prepend(lastLi);
              _this.sets.elUl.css({ 'left': -_this.sets.liWidth });
              if(!_this.sets.lastIsFirst)_this.sets.elUl.append(firstLi);
            }
          }else{
            _this.sets.elUl.css({
              'white-space':'normal',
              'position':'auto'
            });
            _this.sets.elUl.find('li').hide();
            _this.sets.elUl.find('li').first().show();
          }
          _this.sets.elUl.find('li a').first().addClass('active');
          _this.setCssClasses();

          _this.element.bind('touchstart', function(e) {
             _this.sets.initialX = e.originalEvent.changedTouches[0].pageX;
          });

          _this.element.bind('touchend touchcancel', function(e) {
              if(_this.sets.touchDirection>0){
                  _this.element.find(".prev").trigger( "click" );
              }
              if(_this.sets.touchDirection<0){
                  _this.element.find(".next").trigger( "click" );
              }
              _this.sets.initialX = null;
              _this.sets.touchDirection = null;
          });

          _this.element.bind('touchmove', function(e) {
              if(_this.sets.initialX!=null){
                  _this.sets.touchDirection = e.originalEvent.changedTouches[0].pageX - _this.sets.initialX;
                  var abs = Math.abs(_this.sets.touchDirection);
                  if (abs>80) {
                      e.preventDefault();
                  }else{
                      _this.sets.touchDirection = 0;
                  }
              }
          });
		  if(_this.sets.changeHeightArrows != 0){
			  var hgt = parseInt(_this.sets.elLi.height()) + _this.sets.changeHeightArrows;
			  _this.sets.elBtnP.height(hgt);
			  _this.sets.elBtnN.height(hgt);
		  }
        }
        this.setLastIsFirst = function(direction){
			if(direction > 0){
				_this.sets.slideFn.before = function(){
					_this.sets.ulPos = - _this.sets.liWidth;
					_this.sets.slideFn.before = function(){};
				}
				_this.sets.slideFn.after = function(){
					_this.sets.elUl.find('li').last().after(_this.sets.elUl.find('li').first());
					_this.sets.elUl.css('left', parseInt(_this.sets.elUl.css('left')) + _this.sets.liWidth );
					_this.sets.ulPos = 0;
					_this.sets.activeLi = -1;
					_this.setElementActive(_this.sets.activeLi+1);
					_this.sets.slideFn.after = function(){};
				}
			}
			if(direction < 0){
				_this.sets.slideFn.before = function(){
					_this.sets.elUl.find('li').first().before(_this.sets.elUl.find('li').last());
					_this.sets.elUl.css('left', -_this.sets.liWidth );
					_this.sets.ulPos = parseInt(_this.sets.elUl.css('left')) + _this.sets.liWidth;
					_this.sets.slideFn.before = function(){};
				}
				_this.sets.slideFn.after = function(){
					_this.sets.activeLi = 0;
					_this.setElementActive(_this.sets.activeLi);
					_this.sets.slideFn.after = function(){};
				}
			}
        }
		this.lookForActive = function(){
            _this.sets.activeLi = _this.sets.elUl.find('li.active').index();
            if(_this.sets.activeLi < 0 ){
              _this.sets.activeLi = 0;
            }
            _this.setElementActive(_this.sets.activeLi);
            _this.sets.ulPos =-(_this.sets.activeLi)*_this.sets.liWidth;
            _this.sets.elUl.css({ 'left': _this.sets.ulPos });
            _this.sets.activeLi--;
        }
        this.slidePrStage = function(e){
            e.preventDefault();
            _this.stopAutoSlider();
            if(!_this.sets.isAnimated){
                _this.sets.isAnimated = true;
                _this.sets.liWidth = _this.sets.elUl.find('li').first().innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left'));
                var direction = 1;
                if(_this.sets.elUl.length>0){
                  if($(this).hasClass('prev')) direction = -1;
                  _this.sets.activeLi += direction;
                  _this.setElementActive(_this.sets.activeLi+1);
                  if(_this.sets.fading){
                    _this.fadeAnimation();
                  }else{
                    _this.sets.ulPos =-(_this.sets.activeLi+1)*_this.sets.liWidth;
					if(_this.sets.lastIsFirst){
						_this.setLastIsFirst(direction);
					}
                    _this.slideAnimation();
                  }
                }
            }
        }
        this.slideToPrStage = function(e){
            e.preventDefault();
            _this.stopAutoSlider();
            var thisLi = $(this).parents('li');
            if(!_this.sets.isAnimated){
                _this.sets.isAnimated = true;
                _this.sets.liWidth = _this.sets.elUl.find('li').first().innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left'));
                if(_this.sets.elUl.length>0){
                  _this.sets.activeLi = _this.sets.elBlUl.find('li').index(thisLi);
                  _this.setElementActive(_this.sets.activeLi+1);
                  if(_this.sets.fading){
                    _this.fadeAnimation();
                  }else{
                    _this.sets.ulPos =-(_this.sets.activeLi+1)*_this.sets.liWidth;
                    _this.slideAnimation();
                  }
                }
            }
        }
        this.slideByTimer = function(){
            if(!_this.sets.isAnimated){
                _this.sets.isAnimated = true;
                if(_this.sets.elUl.length>0){
                    _this.sets.activeLi++;
                    if(_this.sets.activeLi>_this.sets.cntElements){
                        _this.sets.activeLi = 0;
                    }
                    _this.setElementActive(_this.sets.activeLi+1);
                    if(_this.sets.fading){
                      _this.fadeAnimation();
                    }else{
                      _this.sets.liWidth = _this.sets.elUl.find('li').first().innerWidth() + parseInt(_this.sets.elUl.find('li').css('margin-right')) + parseInt(_this.sets.elUl.find('li').css('margin-left'));
                      _this.sets.ulPos =-(_this.sets.activeLi+1)*_this.sets.liWidth;
                      _this.slideAnimation();
                    }
                }
            }
        }
        this.slideAnimation = function(){
            _this.sets.slideFn.before();
			_this.sets.elUl.animate({ 'left': _this.sets.ulPos },{
                 'duration' : _this.sets.speed,
                 'start' : function(){
                    if(typeof (_this.sets.fnStart) == 'function')_this.sets.fnStart();
                 },
                 'step' : function(){},
                 'complete' : function(){
                    _this.sets.isAnimated = false;
					_this.sets.slideFn.after();
					if(!_this.sets.lastIsFirst && _this.sets.activeLi<0){
                        _this.sets.elUl.css({ 'left': -_this.sets.liWidth*_this.sets.cntElements });
                        _this.sets.activeLi = _this.sets.cntElements-1;
                        _this.setElementActive(_this.sets.activeLi+1);
                    }
                    if(!_this.sets.lastIsFirst && _this.sets.activeLi>=_this.sets.cntElements){
                        _this.sets.elUl.css({ 'left': -_this.sets.liWidth });
                        _this.sets.activeLi = 0;
                        _this.setElementActive(_this.sets.activeLi+1);
                    }
                    if(_this.sets.elBlUl != null)_this.setBullet(_this.sets.activeLi);
                    if(_this.sets.iScroll != null){
                        _this.sets.iScroll.refresh();
                    }
					
                    if(typeof (_this.sets.fnComplete) == 'function')_this.sets.fnComplete();
                    _this.startAutoSlider();
                }
            });
        }
        this.fadeAnimation = function(){
          var  nextLi = _this.sets.activeLi;
          if(nextLi>=_this.sets.cntElements){ nextLi=0; }
          _this.sets.elUl.find('li').hide();
          _this.sets.elUl.find('li').eq(nextLi).fadeIn({
             'duration' : _this.sets.speed,
             'start' : function(){
                    if(typeof (_this.sets.fnStart) == 'function')_this.sets.fnStart();
                 },
             'step' : function(){},
             'complete' : function(){
                _this.sets.isAnimated = false;
                if(_this.sets.activeLi<0){
                    _this.sets.activeLi = _this.sets.cntElements-1;
                }
                if(_this.sets.activeLi>=_this.sets.cntElements){
                    _this.sets.activeLi = 0;
                }
                _this.setElementActive(_this.sets.activeLi);
                if(_this.sets.elBlUl != null)_this.setBullet(_this.sets.activeLi);
                if(typeof (_this.sets.fnComplete) == 'function')_this.sets.fnComplete();
                _this.startAutoSlider();
                if(_this.sets.iScroll != null){
                    _this.sets.iScroll.refresh();
                }
             }
          });
        }
        this.setBullet = function(ind){
            _this.sets.elBlUl.find('li a.active').removeClass('active');
            $(_this.sets.elBlUl.find('li').get(ind)).find('a').addClass('active');
        }
        this.setElementActive = function(ind){
            _this.sets.elUl.find('li').removeClass('active');
            $(_this.sets.elUl.find('li')[ind]).addClass('active');
        }
        this.startAutoSlider = function(){
          if(_this.sets.autoSlider){
            if(_this.sets.thisTimer == null){
              _this.sets.thisTimer = setInterval(function(){
                _this.slideByTimer();
             }, _this.sets.intervalTime);
            }
          }
        }
        this.stopAutoSlider = function(){
            clearInterval(_this.sets.thisTimer);
            _this.sets.thisTimer = null;
        }

        this.isScrolling = function(e){
          var left = false, right = false, up = false, down = false;
          var frameSelector = _this.sets.window;
          // prüfen ob DIV1 rechts außerhalb der Seite ist
          if (parseInt(_this.element.css("left")) > (frameSelector.width() + _this.sets.document.scrollLeft())) {
              right = false;
          } else {
              right = true;
          }
          // prüfen ob DIV1 links außerhalb der Seite ist
          if ((parseInt(_this.element.css("left")) + parseInt(_this.element.css("width"))) <= _this.sets.document.scrollLeft()) {
              left = false;
          } else {
              left = true;
          }
          if(typeof(_this.sets.glblsets)!='undefined' && typeof(_this.sets.glblsets.isMobile)!='undefined'&&_this.sets.glblsets.isMobile){
            if ((parseInt(_this.element.position().top) + parseInt(_this.element.innerHeight())) <= -parseInt($('#scroller').offset().top)) {
                up = false;
            } else {
                up = true;
            }
            // prüfen ob DIV1 unten außerhalb der Seite ist
            if (parseInt(_this.element.position().top) > (_this.sets.window.height() + (-parseInt($('#scroller').offset().top)))) {
                down = false;
            } else {
                down = true;
            }
          }else{
            if ((parseInt(_this.element.offset().top) + parseInt(_this.element.innerHeight())) <= _this.sets.document.scrollTop()) {
                up = false;
            } else {
                up = true;
            }
            // prüfen ob DIV1 unten außerhalb der Seite ist
            if (parseInt(_this.element.offset().top) > (_this.sets.window.height() + _this.sets.document.scrollTop())) {
                down = false;
            } else {
                down = true;
            }
          }
          if (left == true && right == true && up == true && down == true) {
              if(_this.sets.thisTimer==null){
                _this.sliderUpdate();
              }
          } else {
              if(_this.sets.thisTimer!=null){
                _this.stopAutoSlider();
              }
          }
        }

        this.windowResize = function(){
          _this.setLowerHeightForMobile();
          _this.sliderUpdate();
        }

        this.sliderUpdate = function(fn){
          _this.stopAutoSlider();
          if(!_this.sets.fading){
            if(_this.sets.cntElements>1){
              _this.setCssClasses();
              _this.lookForActive();
            }
          }
          if(typeof(fn)=='function'){
            fn();
          }
          _this.startAutoSlider();
          if(_this.sets.iScroll != null){
              _this.sets.iScroll.refresh();
          }
        }

        this.imgLoad = function(img, callback) {
          return img.each(function() {
              if (callback) {
                  if (this.complete || /*for IE 10-*/ $(this).height() > 0) {
                      callback();
                  }
                  else {
                      $(this).on('load', function(){
                          callback();
                      });
                  }
              }
          });
        };

        this.init();
    };

    // init plugin
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                new joSlider( this, options ));
            }
        });
    }
}));