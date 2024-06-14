function setFontSize() {
    var wW = parseInt(window.innerWidth * 10 / 1920);
	var minFontSize = 4;
	if (wW >= minFontSize) {
		$( 'html' ).css( 'font-size', wW + 'px');
		$( 'body' ).css( 'font-size', wW + 'px');
	} else {
		$( 'html' ).css( 'font-size', minFontSize + 'px');
		$( 'body' ).css( 'font-size', minFontSize + 'px');
	}
}
$( window ).resize(() => {
   setFontSize();
});
$(document).ready(() => {
	setFontSize();
});