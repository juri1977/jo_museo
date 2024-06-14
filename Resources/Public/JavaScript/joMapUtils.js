$(document).ready(() => {
    init();
});
activeObject = null;
touch = false;
init = () => {
    if (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0)) touch = true;
    lat = 50.9295513;
    lon = 11.5898195;
    zoom = 15;
    popupsize = 10;
    projLonLat   = new OpenLayers.Projection("EPSG:4326");   // WGS 1984
    projMercator = new OpenLayers.Projection("EPSG:900913"); // Spherical Mercator
    navigationControl = new OpenLayers.Control.Navigation();
    if (touch) navigationControl = new OpenLayers.Control.TouchNavigation();
    extentMax = 20037508.34;
    map = new OpenLayers.Map({ 
        div: "mapdiv", 
        center: new OpenLayers.LonLat(0, 0),
        theme: null,
        controls: [ navigationControl,
            new OpenLayers.Control.Attribution(),   
            new OpenLayers.Control.Zoom(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.OverviewMap()
        ],
        maxExtent: new OpenLayers.Bounds(-extentMax, -extentMax, extentMax, extentMax),
        eventListeners: {
            featureover: (e) => {
                // console.log('hoverin');
            },
            featureout: (e) => {
                //console.log('hoverout');
            }
        }
    });
    map.setOptions({
        projection: "EPSG:4326"
    });
    map.displayProjection = new OpenLayers.Projection('EPSG:4326');
    
    var baselayer = new OpenLayers.Layer.XYZ("Normale Karte",
        ["http://c.tile.stamen.com/toner/${z}/${x}/${y}.png"],
        {
            sphericalMercator: true,
            attribution: "Karte von <a href='http://openstreetmap.org/'>OpenStreetMap</a>"
        }
    );

    baselayerAlternative = new OpenLayers.Layer.OSM( "Simple OSM Map");
    map.addLayer(baselayerAlternative); 
    //map.addLayer(baselayer);  
  
    var wmsLayer = new OpenLayers.Layer.WMS(
        "OpenLayers WMS", 
        "http://vmap0.tiles.osgeo.org/wms/vmap0",
        { layers: 'basic' }
    ); 

    map.setCenter(new OpenLayers.LonLat(lon, lat).transform(projLonLat, projMercator), zoom);

    /* tooltip and navigation control */
    

    $("#img-tooltip").css("position", "absolute");

    if (!touch) navigationControl.deactivate();
	map.events.register("click", map, () => {
		activateMapControl();
	});

  
    $("a[href*='#joMapAnchor']").click(() => {
         activateMapControl();
    });

    map.events.register("mousemove", map, (evt) => {
        $("#img-tooltip").css({top: evt.pageY, left: evt.pageX + 10 });
        $("#img-tooltip").tooltip("show");
    });    
    containerMouseMoveAction();

    function activateMapControl() {
        if (activeObject != null) {
			map.removePopup(activeObject);
			activeObject = null;
		}
        $("#img-tooltip").tooltip("hide");
        $("#img-tooltip").tooltip("disable");
        if (!touch) {
			navigationControl.activate();
			map.events.register("mouseout", map, mouseOutFunc);
		}
    }

    function containerMouseMoveAction() {
        $("#map-container").unbind();
        $("#map-container").mouseleave(() => {
            $("#img-tooltip").tooltip("hide");
        });
    }

    function mouseOutFunc(evt) {
        var mapdiv = $("#mapdiv");
        if (evt.pageY < mapdiv.position().top || 
            evt.pageY > mapdiv.position().top + mapdiv.height() || 
            evt.pageX < mapdiv.position().left || 
            evt.pageX > mapdiv.position().left + mapdiv.width()
        ) {
            navigationControl.deactivate();
            containerMouseMoveAction();
            $("#img-tooltip").tooltip("enable");
            map.events.unregister("mouseout", map, mouseOutFunc);
        }
    }
    //Marker SetUp
    var vectorLayer = new OpenLayers.Layer.Vector("Overlay");    
    epsg4326 =  new OpenLayers.Projection("EPSG:4326"); //WGS 1984 projection
    projectTo = map.getProjectionObject(); //The map projection (Spherical Mercator)

    if (typeof jojsonlocation != 'undefined' && jojsonlocation.length > 0) {
        jojsonlocation.forEach(function(point){
            // Define markers as "features" of the vector layer:
            var feature = new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(point.longi,point.lat).transform(epsg4326, projectTo),
            {description:'<h3>'+point.title+'</h3><p>'+point.bodytext+'</p>'+point.link},
                {externalGraphic: 'http://sammlungsportal.thulb.uni-jena.de/typo3conf/ext/jo_bkr_base/Resources/Public/Icons/joPin.svg', graphicHeight: 40, graphicWidth: 40, graphicXOffset:-17, graphicYOffset:-25  }
            );    
            vectorLayer.addFeatures(feature);
        });
        map.addLayer(vectorLayer);
    }; 

    /* Sammlungsdetail - MiniMapMarkerSetup */
    if ((typeof joMiniMapLat != 'undefined') && (typeof joMiniMapLongi != 'undefined')) {
        var detailmapfeature = new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(joMiniMapLongi,joMiniMapLat).transform(epsg4326, projectTo),
            {description:''} ,
            {externalGraphic: 'http://sammlungsportal.thulb.uni-jena.de/typo3conf/ext/jo_bkr_base/Resources/Public/Icons/joPin.svg', graphicHeight: 40, graphicWidth: 40, graphicXOffset:-17, graphicYOffset:-25  }
        );
        vectorLayer.addFeatures(detailmapfeature);
        map.addLayer(vectorLayer);
        map.setCenter(new OpenLayers.LonLat(joMiniMapLongi, joMiniMapLat).transform(projLonLat,projMercator), zoom);
    }
    
    /**
     *  Löscht das alte Popup und nutzt das neue.
     */
    function popupProcess(feature, popup) {
        if (typeof activeObject != "undefined" && activeObject != null && popup.contentDiv.id != activeObject.contentDiv.id) {
            map.removePopup(activeObject);
            activeObject.destroy();
            activeObject = null;
            activeObject = popup;
            feature.popup = popup;
            feature.popup.autoSize = true;
            map.addPopup(popup);
        } else {
            if (typeof activeObject == "undefined" || activeObject == null) {
                activeObject = popup;
                feature.popup = popup;
                feature.popup.autoSize = true;
                map.addPopup(popup);
            }
        }
    }
    //Add a selector control to the vectorLayer with popup functions
    var controls = {
      selector: new OpenLayers.Control.SelectFeature(vectorLayer)
    };
	
vectorLayer.events.on({'featureselected': createPopup, 'featureunselected': destroyPopup});
    function createPopup(ev) {
		if (activeObject != null) {
			map.removePopup(activeObject);
			activeObject = null;
		}
		var feature = ev.feature;
		var contenu = '<div class="markerContent">' + feature.attributes.description + '</div>';
		popup = new OpenLayers.Popup.Anchored(
            "pop",
            feature.geometry.getBounds().getCenterLonLat(),
            null,
            contenu,
            null, true
        );
        map.addPopup(popup);
		activeObject = popup;
    }
    function destroyPopup(ev) {
		var feature = ev.feature;
        if (feature.popup) {
            map.removePopup(feature.popup);
            feature.popup.destroy();
            delete feature.popup;
        }
    }
    map.addControl(controls['selector']);
    controls['selector'].activate();    
}