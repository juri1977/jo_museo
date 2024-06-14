;(function ( $ ) {

    if (typeof($) != 'function') {
        console.log('jQuery required');
        return false;
    }
    // Create the defaults once
    var pluginName = 'joMouseScroll',
        defaults = {
            speed: 50,
            movepx: 2,
            moveElementClass: '.joMoveElement',
            moveElementSelector: null,
            offsetY: null
        };
        intern = {
            direction: -1,
            timer: null
        }

    function Plugin(element, options) {
        _this = this;
        _this.element = $(element);
        _this.options = $.extend( {}, defaults, options, intern) ;
        _this._defaults = defaults;
        _this.init();
    }

    Plugin.prototype.move = function () {
        var elementHeight = _this.options.moveElementSelector.height();
        var viewPortHeight = _this.element.height();
        var scrolldiv = viewPortHeight-elementHeight;
        var tempTop = parseInt(_this.options.moveElementSelector.css('top'));
        if (isNaN(tempTop)) {
            tempTop = 0;
        }
        tempTop += _this.options.movepx * _this.options.direction;
        if (tempTop < scrolldiv) tempTop = scrolldiv;
        if (tempTop > 0) tempTop = 0;
        _this.options.moveElementSelector.css('position', 'relative');
        _this.options.moveElementSelector.css('top', tempTop + 'px');

    }

    Plugin.prototype.startMove = function () {
        _this.options.timer = setInterval( _this.move , _this.options.speed);
    }

    Plugin.prototype.stopMove = function () {
        clearInterval(_this.options.timer);
    }

    Plugin.prototype.setDirection = function (e) {
       var offY = e.pageY;
       var docOffY = _this.element.offset().top;
       var changeDirectionOffY = _this.element.height() / 2 + docOffY;
       if (_this.options.offsetY != null) {
            changeDirectionOffY = parseInt(_this.options.offsetY) + docOffY;
       }
       if (offY > changeDirectionOffY) _this.options.direction = 1
       else _this.options.direction = -1; 
    }

    Plugin.prototype.init = function () {
        _this.options.moveElementSelector = _this.element.find(_this.options.moveElementClass);
        _this.element.unbind('mouseenter', _this.startMove);
        _this.element.bind('mouseenter', _this.startMove);
        _this.element.unbind('mouseleave', _this.stopMove);
        _this.element.bind('mouseleave', _this.stopMove);
        _this.element.unbind('mousemove', _this.setDirection);
        _this.element.bind('mousemove', _this.setDirection);
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, 
                new Plugin(this, options));
            }
        });
    }
})( jQuery );