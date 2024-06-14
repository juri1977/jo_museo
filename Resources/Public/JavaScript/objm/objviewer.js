window.onload = initObjectViewer;

function initObjectViewer() {
  var windowWidth = 900;
  var windowHeight = 600;
  //var imageWidth = 1800;
  var imageWidth = 800;
  var imageHeight = 1200;
  var zoomWidth = 3840;
  var zoomHeight = 2560;
  var rotationspeed = 300;
  var mouseRotationspeed = 10;
  var defaultImageMap = new Map();
  var zoomImageMap = new Map();
  var currentIndex = 0;
  var maxIndex = imageList.length - 1;
  var zoom = 0;
  var mousedown = false;
  var mouseposx = 0;
  var mouseposy = 0;
  var imageposx = 0;
  var imageposy = 0;
  var defaultMarginLeft = (windowWidth - $(".objmviewer").innerWidth()) / 2;
  var defaultMarginRight = (windowHeight - $(".objmviewer").innerHeight()) / 2;
  var autoplay;

  $(".objmviewer-img").css("margin-left", "-" + defaultMarginLeft + "px");
  $(".objmviewer-img").css("margin-top",  "-" + defaultMarginRight + "px");

  /* 
  // funciton to load next image only after the current is finished
  var list_length = imageList.length;
  var list_i = 0;
  var is_first = 1;

  function load_next_item() {
    if(list_i == list_length) return;

    let image = new Image(imageWidth, imageHeight);
    image.src = iiifImageApi.replace("/digicult/", "/").replace("/rsc/iiif/image", "/api/iiif/image/v2")  + imageList[list_i].replace("/", "%2F") + "/full/" + imageWidth + ",/0/default.png";
    image.onload = function(e) {
      defaultImageMap.set(list_i, image);
      if(is_first) {
        $(".objmviewer-img").attr("src", defaultImageMap.get(currentIndex).src);
        is_first = 0;
      }
      list_i += 1;
      load_next_item();
    };
    image.ererror = function(e) {
      list_i += 1;
      load_next_item();
    };
  }

  load_next_item(); */

  imageList.forEach(function(item, index, array){
    let image = new Image(imageWidth, imageHeight);
    image.src = iiifImageApi.replace("/digicult/", "/").replace("/rsc/iiif/image", "/api/iiif/image/v2")  + item.replace("/", "%2F") + "/full/" + imageWidth + ",/0/default.png";

    defaultImageMap.set(index, image);
  });

  $(".objmviewer-img").attr("src", defaultImageMap.get(currentIndex).src);

  $( "body" ).keydown(function( event ) {
    //console.log(event.which);
    if ( event.which == 39 ) { // right
     event.preventDefault();
     nextImage();
    }

    if ( event.which == 37 ) { // left
     event.preventDefault();
     prevImage();
    }

    if ( event.which == 90 ) { // Z
     event.preventDefault();
     if (zoom) {
       unzoomImage();
     } else {
       zoomImage();
     }
    }

    if ( event.which == 80 ) { // P
      //console.log(autoplay);
      event.preventDefault();
      if (autoplay) {
        stopautorotate();
      }
      else {
        startautorotate();
      }
    }
  });

  $('body').on('click', ".objmviewer-left", function (event) {
    prevImage();
  });

  $('body').on('click', ".objmviewer-right", function (event) {
    nextImage();
  });

  $('body').on('click', ".objmviewer-zoom", function (event) {
    zoomImage();
  });

  $('body').on('click', ".objmviewer-unzoom", function (event) {
    unzoomImage();
  });

  $('body').on('click', ".objmviewer-play", function (event) {
    startautorotate();
  });

  $('body').on('click', ".objmviewer-stop", function (event) {
    stopautorotate();
  });

  $('body').on('mousedown', ".objmviewer-img", function (event) {
    mousedown = true;
    mouseposx = event.pageX;
    mouseposy = event.pageY;
    imageposx = parseInt($(".objmviewer-img").css("margin-left"));
    imageposy = parseInt($(".objmviewer-img").css("margin-top"));
  });

  $('body').on('mouseup', function (event) {
    mousedown = false;
    mouseposx = 0;
    mouseposy = 0;
    imageposx = 0;
    imageposy = 0;
  });

  $('body').on('mousemove', function (event) {
    if(mousedown && zoom == 1){
      let marginleft = imageposx  - ((mouseposx - event.pageX));
      if (marginleft > 0) {
        marginleft = 0;
      }
      if (marginleft < (zoomWidth - $(".objmviewer").innerWidth()) * -1) {
        marginleft = (zoomWidth - $(".objmviewer").innerWidth()) * -1;
      }
      let margintop = imageposy  - ((mouseposy - event.pageY));
      if (margintop > 0) {
        margintop = 0;
      }
      if (margintop < (zoomHeight - $(".objmviewer").innerHeight()) * -1) {
        margintop = (zoomHeight - $(".objmviewer").innerHeight()) * -1;
      }
      $(".objmviewer-img").css("margin-left", marginleft + "px");
      $(".objmviewer-img").css("margin-top",  margintop + "px");
    }
    if(mousedown && zoom == 0){
      let diff = mouseposx - event.pageX;
      if (diff < (mouseRotationspeed * -1) ) {
        nextImage();
        mouseposx = event.pageX;
      }
      if (diff > mouseRotationspeed) {
        prevImage();
        mouseposx = event.pageX;
      }
      // if (marginleft > 0) {
      //   marginleft = 0;
      // }
      // if (marginleft < (zoomWidth - $(".objmviewer").innerWidth()) * -1) {
      //   marginleft = (zoomWidth - $(".objmviewer").innerWidth()) * -1;
      // }
      // let margintop = imageposy  - ((mouseposy - event.pageY));
      // if (margintop > 0) {
      //   margintop = 0;
      // }
      // if (margintop < (zoomHeight - $(".objmviewer").innerHeight()) * -1) {
      //   margintop = (zoomHeight - $(".objmviewer").innerHeight()) * -1;
      // }
      // $(".objmviewer-img").css("margin-left", marginleft + "px");
      // $(".objmviewer-img").css("margin-top",  margintop + "px");
    }
  });

  function loadZoomImage(index) {
    // console.log(index);
    if(zoomImageMap.has(index)) {
      $(".objmviewer-img").attr("src", zoomImageMap.get(index).src);
    }
    else {
      let image = new Image(zoomWidth, zoomHeight);
      image.src = iiifImageApi.replace("/digicult/", "/").replace("/rsc/iiif/image", "/api/iiif/image/v2") +  imageList[index].replace("/", "%2F") + "/full/" + zoomWidth + ",/0/default.png";
      zoomImageMap.set(index, image);
      $(".objmviewer-img").attr("src", zoomImageMap.get(index).src);
    }
  }

  function setImage(index) {
    if (zoom == 0) {
      $(".objmviewer-img").attr("src", defaultImageMap.get(index).src);
    }
    else {
      loadZoomImage(index);
    }
  }

  function nextImage() {
    if (currentIndex < maxIndex) {
      setImage(++currentIndex)
    }
    else {
      currentIndex = 0;
      setImage(currentIndex);
    }
  }

  function prevImage() {
    if (currentIndex > 0) {
      setImage(--currentIndex);
    }
    else {
      currentIndex = maxIndex;
      setImage(currentIndex);
    }
  }

  function zoomImage() {
    loadZoomImage(currentIndex);
    $(".objmviewer-img").addClass("zoom");
    $(".objmviewer-img").css("margin-left", "-" + (zoomWidth - $(".objmviewer").innerWidth()) / 2 + "px");
    $(".objmviewer-img").css("margin-top",  "-" + (zoomHeight - $(".objmviewer").innerHeight()) / 2 + "px");
     zoom = 1;
     $(".objmviewer-zoom").addClass("hidden");
     $(".objmviewer-unzoom").removeClass("hidden");
  }

  function unzoomImage() {
    zoom = 0
    setImage(currentIndex);
    $(".objmviewer-img").removeClass("zoom");
    $(".objmviewer-img").css("margin-left", "-" + defaultMarginLeft + "px");
    $(".objmviewer-img").css("margin-top",  "-" + defaultMarginRight + "px");
    $(".objmviewer-unzoom").addClass("hidden");
    $(".objmviewer-zoom").removeClass("hidden");
  }

  function startautorotate() {
    autoplay = window.setInterval(function(){
      nextImage();
    }, rotationspeed);
    $(".objmviewer-play").addClass("hidden");
    $(".objmviewer-stop").removeClass("hidden");
  }

  function stopautorotate() {
    clearInterval(autoplay);
    autoplay = undefined;
    $(".objmviewer-stop").addClass("hidden");
    $(".objmviewer-play").removeClass("hidden");
  }

};

window.onresize = function () {
  var windowWidth = 900;
  var windowHeight = 600;
  var defaultMarginLeft = (windowWidth - $(".objmviewer").innerWidth()) / 2;
  var defaultMarginRight = (windowHeight - $(".objmviewer").innerHeight()) / 2;
  $(".objmviewer-img").css("margin-left", "-" + defaultMarginLeft + "px");
  $(".objmviewer-img").css("margin-top",  "-" + defaultMarginRight + "px");
}
