requirejs.config({
    baseUrl: '/typo3conf/ext/jo_exhibition/Resources/Public/JavaScript',
    urlArgs: "bust=" +  (new Date()).getTime(),
    waitSeconds: 200,
	paths: {
        jquery: "jquery.min",
		jqueryui: "jquery-ui.min",
		swipe: "jquery.touchSwipe.min",
		bootstrap :  "bootstrap.min",
		flexslider: "/typo3conf/ext/ws_flexslider/Resources/Public/JavaScript/jquery.flexslider-min",
		zoom: "jquery.elevateZoom-3.0.8.min",
		lightbox: "lightbox"
    },
	shim: {
        'swipe': { deps : ["jquery"] },
        'bootstrap': { deps : ["jquery"] },
		'flexslider': { deps : ["jquery"], exports: 'flexslider' },
		'zoom': { deps : ["jquery"] },
		'lightbox': { deps : ["jquery"] },
    }
});
requirejs(["joPage"]);