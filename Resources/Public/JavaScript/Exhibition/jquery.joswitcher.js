/* joSwitcher
 * slides element dependent on given parameter
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
    var pluginName = 'joSwitcher';
    /* defaults */
    var defaults = {
		'selItems':	null
    }
    var internals = {
      'window' : $(window),
      'document' : $(document),
	  'itemsWrap' : null,
	  'items' : null
    }
    function joSwitcher ( element, options ) {
        var _this = this;
        this.element = $(element);
        this.element.uniqueId();

        /* sets by defaults and options */
        this.sets = $.extend({}, defaults, options, internals);
        this.init = function(){
          if(_this.element.find(_this.sets.selItems).length>0){
            _this.sets.items = $(_this.sets.selItems);
			_this.sets.itemsWrap = _this.sets.items.closest('div');
			_this.buildTouchEvents();
		  }
        }

        this.buildTouchEvents = function(){
          _this.element.bind('touchstart', function(e) {
             _this.sets.initialX = e.originalEvent.changedTouches[0].pageX;
          });

          _this.element.bind('touchend touchcancel', function(e) {
              if(_this.sets.touchDirection>0){
                  _this.setupAnimation(1);
              }
              if(_this.sets.touchDirection<0){
                  _this.setupAnimation(-1);
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
        }
		this.reindexItems = function(){
			var anz = _this.sets.items.length;
			var act = Math.ceil(anz/2) - 1;
			ind = 0;
			$.each(_this.sets.items, function(i,n){
				(i<=act) ? ind++ : ind--;
				$(n).css('z-index',ind);
			});
			_this.sets.items.eq(act).css('z-index',100);
			_this.sets.items.eq(act).addClass('active');
		}

        this.setupAnimations = function(direction){
			_this.sets.items.removeClass('active');
        }
		
		this.setupAnimation = function(direction){
			_this.sets.items.removeClass('active');
			if(direction>0){
				_this.sets.items.first().before(_this.sets.items.last());
			}else{
				_this.sets.items.last().after(_this.sets.items.first());
			}
			_this.sets.items = _this.element.find(_this.sets.selItems);
			_this.reindexItems();
        }

        this.windowResize = function(){
          _this.sliderUpdate();
        }

        this.sliderUpdate = function(fn){

        }

        this.init();
    };

    // init plugin
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                new joSwitcher( this, options ));
            }
        });
    }
}));