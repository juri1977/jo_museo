$(document).ready(function(){
    init();
});

init = function() {
  var $mapcon = $('.map-container');
  
  if(typeof geojson != 'undefined' && geojson.length > 0 && $mapcon.length) {
    var $loader = $('#joAjaxloader');

    $mapcon.each(function(i,v) {
      var $that = $(this);
      var $mapdiv = $that.find('.mapdiv');
      var $container = $that.find('.ol-popup');
      var $content = $that.find('.ol-popup-content');
      var $closer = $that.find('.ol-popup-closer');

      var id = $mapdiv.data('id');

      var json = geojson[id];

      var overlay = new ol.Overlay({
        element: $container[0],
        autoPan: true,
        autoPanAnimation: {
          duration: 250
        }
      });

      $closer.click(function(e) {
        e.preventDefault();
        var view = map.getView();
          view.animate({
          zoom: 5
        });
        overlay.setPosition(undefined);
        $closer[0].blur();
        return false;
      });

      var map = new ol.Map({
        target: $mapdiv[0],
        layers: [new ol.layer.Tile({source: new ol.source.OSM()})],
        overlays: [overlay],       
        view: new ol.View({
          center: ol.proj.fromLonLat([11.100,50.500]),
          zoom: 5 
        })
      });

      /*
      map.on('moveend', function(e) {
        var newZoom = map.getView().getZoom();
      });
      */

      //for (var i = 0; i < json.length; i++) {
        /*
        var source = new ol.source.Vector({
          features: (new ol.format.GeoJSON()).readFeatures(json[i],{featureProjection: 'EPSG:3857'})
          //features: (new ol.format.GeoJSON()).readFeatures(json,{featureProjection: 'EPSG:3857'})
        });
        */
        var clusterSource = new ol.source.Cluster({
          distance: 40,
          source: new ol.source.Vector({
            features: (new ol.format.GeoJSON()).readFeatures(json[0],{featureProjection: 'EPSG:3857'})
          })
        });

        var styles = {
          'Point': new ol.style.Style({
              image: new ol.style.Icon(({
                anchor: [0.5, 1],
                src: '/typo3conf/ext/jo_museo/Resources/Public/Images/joPin.svg'
              }))
          }),
          'LineString': new ol.style.Style({
            stroke: new ol.style.Stroke({
              color: 'black',
              width: 1,
            }),
          }),
          'MultiLineString': new ol.style.Style({
            stroke: new ol.style.Stroke({
              color: 'black',
              width: 1,
            }),
          }),
        };


        function getStyle(feature, resolution) {
          var type = feature.getGeometry().getType();
          var st = styles[type];

          if (type == 'Point') {
            var text = '';
            var options = '';
            var img_name = 'joPin.svg';

            if (typeof feature.get('name') != 'undefined') {
              options = feature.get('_umap_options');
              text = feature.get('name');

              img_name = typeof feature.get('pin') != 'undefined' ? feature.get('pin') : 'joPin.svg';
            } else if (typeof feature.get('features') != 'undefined') {
              var features = feature.get('features');

              img_name = typeof features[0].get('pin') != 'undefined' ? features[0].get('pin') : 'joPin.svg';

              var textArr = {};

              for (let i = 0; i < features.length; i++) {
                if (typeof features[i].get('name') != 'undefined') {
                  if (options == '') options = features[i].get('_umap_options');

                  var name = features[i].get('name');
                  if (name in textArr) {
                    textArr[name] = textArr[name] + 1;
                  } else {
                    textArr[name] = 1;
                  }
                }
              }

              var num = Object.keys(textArr).length;

              $.each(textArr, function(i, v) {
                if (v == 1) {
                  text += i;
                } else {
                  text += i + ' (' + v + ')';
                }

                num--;
                if (num > 0) {
                  text += ', ';
                }
              });
            }

            if(text != '') {
              var offY = '18';
              var offX = '0';
              if(options != '' && typeof options != 'undefined') {
                if(options['labelDirection']) {
                  switch (options['labelDirection']) {
                    case 'right':
                      offY = -12;
                      offX = text.length * 5;
                      break;
                    case 'left':
                      offY = -12;
                      offX = text.length * 5 * -1;
                      break;
                    case 'bottom':
                      offY = '18';
                      offX = '0';
                      break;
                    case 'top':
                      offY = '-33';
                      offX = '0';
                  }
                }
              }

              text = new ol.style.Text({
                  text: text,
                  offsetY: offY,
                  offsetX: offX,
                  font: 'normal 16px Arial',
                  stroke: new ol.style.Stroke({color: 'white'}),
                  backgroundFill: new ol.style.Fill({color: 'white'}),
                  padding: [5,5,5,5],
                  fill: new ol.style.Fill({
                      color: 'black'
                  })
              });
            }

            if (map.getView().getZoom() < 5) {
              text = '';
            }

            st = new ol.style.Style({
              image: new ol.style.Icon(({
                anchor: [0.5, 1],
                src: '/typo3conf/ext/jo_museo/Resources/Public/Images/Exhibition/' + img_name
              })),
              text: text
            });
          }
          
          return st;
        }

        //var geojsonLayer = new ol.layer.Vector({
          //source: source,

        var clusterLayer = new ol.layer.AnimatedCluster({
          name: 'Cluster',
          source: clusterSource,
          animationDuration: 700,

          style: getStyle
        });

        //map.addLayer(geojsonLayer);
        map.addLayer(clusterLayer);

        map.getView().fit(clusterSource.getSource().getExtent());

        // if (i == 0) {
          // map.getView().fit(clusterSource.getExtent());
        // }
      //}

      var selectCluster = new ol.interaction.SelectCluster({
        // Point radius: to calculate distance between the features
        pointRadius: 30,
        // circleMaxObjects: 40,
        // spiral: false,
        // autoClose: false,
        animate: true,
        // Feature style when it springs apart
        featureStyle: function(f, res) {
          return getStyle(f, res);
          /*
          return [
            new ol.style.Style({
              image: new ol.style.Icon(({
                anchor: [0.5, 1],
                src: '/typo3conf/ext/jo_museo/Resources/Public/Images/joPin.svg'
              }))
            })
          ];
          */
        },
        // selectCluster: false,  // disable cluster selection
        // Style to draw cluster when selected
        style: function(f,res){
          var cluster = f.get('features');
          if (cluster.length > 1) {
            // var s = [ getStyle(f,res) ];
            return [
              new ol.style.Style({
                image: new ol.style.Circle({
                  radius: 50,
                  stroke: new ol.style.Stroke({
                    color: 'rgba(239,126,25,1)', 
                    width: 1 
                  }),
                  fill: new ol.style.Fill({
                    color: 'rgba(239,126,25,.2)'
                  })
                })
              })
            ];
          } else {
            // console.log('else');
            if (typeof f.get('selectclusterfeature') == 'undefined') return getStyle(f, res);
          }
          return null;
        }
      });
      map.addInteraction(selectCluster);

      // On selected => get feature in cluster and show info
      selectCluster.getFeatures().on(['add'], function (e) {
        var c = e.element.get('features');
        if (c.length == 1) {
          var feature = c[0];
          if (typeof feature.get('link') != 'undefined') {
            var href = feature.get('link');

            if($loader.length) $loader.show();

            $.get(href, function(data) {
              if($loader.length) $loader.hide();
              
              $that.find('.map-overlay2 .map-overlay2-con').removeClass('bkl').html(data).promise().done(function() {
                if (null != audioplayerAction) {
                  audioplayerAction();
                }
              });
              $that.find('.map-overlay2').fadeIn();
            });
          }
        } else {
          // console.log('Cluster (' + c.length + ' features)');
        }
      });
      selectCluster.getFeatures().on(['remove'], function (e) {
        // console.log('remove');
        selectCluster.clear();

        // this.getFeatures().clear();
        // this.overlayLayer_.getSource().clear();
      });

      /*
      map.on('singleclick', function(evt) {

        var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
          return feature;
        });

        if (feature) {
          cfeatures = feature.get('features');

          if (typeof cfeatures != 'undefined') {
            if (cfeatures.length == 1) {
              var geometry = feature.getGeometry();
              var coord = geometry.getCoordinates();
              
              var content_data = '<h3>' + cfeatures["0"].values_.name + '</h3>';

              $content[0].innerHTML = content_data;
              overlay.setPosition(coord);

              var view = map.getView();
              view.animate({
                center: feature.getGeometry().getCoordinates(),
                zoom: 12
              }); 
            } else {
              var view = map.getView();
              view.animate({
                center: feature.getGeometry().getCoordinates(),
                zoom: map.getView().getZoom() + 1
              });
            }
          } else {
            if (typeof feature.get('link') != 'undefined') {
              var href = feature.get('link');

              if($loader.length) $loader.show();

              $.get(href, function(data) {
                if($loader.length) $loader.hide();
                
                $that.find('.map-overlay2 .map-overlay2-con').removeClass('bkl').html(data);
                $that.find('.map-overlay2').fadeIn();
              });
            }
          }
        }
      });
      */

      map.on("pointermove", function (evt) {
        var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
            return feature;
        }); 
        if (feature) {
          map.getTargetElement().style.cursor = 'pointer';
        } else {
          map.getTargetElement().style.cursor = '';
        }
      });

      var getInput = function(layer, id) {
        var input = document.createElement('input');
        input.type = 'checkbox';
        input.checked = true;
        input.id = id;
        input.onchange = function(e) {
              layer.setVisible(e.target.checked);
          };
          return input;
      };
      
      var joLayerControl = function(opt_options) {
        var options = opt_options || {};

        var button = document.createElement('button');
        button.className = 'joLayerButton';
        button.innerHTML = 'L';

        var element = document.createElement('div');
        element.className = 'joLayerContainer ol-unselectable ol-control';
        element.appendChild(button);

        var layerContent = document.createElement('div');
        layerContent.className = 'joLayerContent';
        element.appendChild(layerContent);

        button.onclick = function() {
          layerContent.classList.toggle('active');
        };

        var ul = document.createElement('ul');
        var tmplayers = map.getLayers().getArray();
        for(var i = 1; i < tmplayers.length; i++) {
          if(tmplayers[i].get('title') && tmplayers[i].get('title') == 'polygone') {
            continue;
          }
          var id = 'Layer ' + (i);

          var li = document.createElement('li');

          var input = getInput(tmplayers[i], id);

          var label = document.createElement('label');
          label.htmlFor = id;
          label.innerHTML = tmplayers[i].get('title') ? tmplayers[i].get('title') : id;

          li.appendChild(input);
          li.appendChild(label);

          ul.appendChild(li);
        }

        layerContent.appendChild(ul);

        ol.control.Control.call(this, {
          element: element,
          target: options.target
        });
      };
      ol.inherits(joLayerControl, ol.control.Control);

      map.addControl(new joLayerControl());


      $that.find('.ol-viewport').hover(
        function() {
          currentlyZooming = true;
        }, function() {
          currentlyZooming = false;
        }
      );

      $that.find('.map-overlay').click(function() {
        $(this).fadeOut();
        $that.find('.map-closer').fadeIn();
      });

      $that.find('.map-overlay2-con').click(function(e) {
        if(e.target == e.currentTarget) {
          var $video = $that.find('.map-overlay2 .vp-vid');
          if ($video.length) $video[0].pause();

          var $audio = $that.find('.map-overlay2 .audio-btn-start.playing');
          if ($audio.length) $audio.trigger('click');

          $that.find('.map-overlay2').fadeOut();

          // selectCluster.getFeatures().clear();
          selectCluster.clear();
        };
      });

      $that.find('.map-closer').click(function() {
        if ($that.find('.map-overlay2').is(':visible')) {
          var $video = $that.find('.map-overlay2 .vp-vid');
          if ($video.length) $video[0].pause();

          var $audio = $that.find('.map-overlay2 .audio-btn-start.playing');
          if ($audio.length) $audio.trigger('click');
          
          $that.find('.map-overlay2').fadeOut();
          
          // selectCluster.getFeatures().clear();
          selectCluster.clear();
        } else {
          $(this).fadeOut();
          $that.find('.map-overlay').fadeIn();
        }
      });
    });
  
    var tmp_html = '';
    $('body').on('click', '.map-overlay2-con .bk-link', function(e) {
      e.preventDefault();
      var $that = $(this);
      var href = $that.attr('href');

      if (typeof href != 'undefined' && href != '') {
        if($loader.length) $loader.show();
        
        $.get(href, function(data) {
          if($loader.length) $loader.hide();
          
          var $moc = $that.closest('.map-overlay2-con');
          tmp_html = $moc.html();
          $moc.addClass('bkl').html(data);
        });
      }
    });

    $('body').on('click', '.map-overlay2-con .back', function(e) {
      e.preventDefault();
      var $moc = $(this).closest('.map-overlay2-con');
      $moc.removeClass('bkl').html(tmp_html);
    });
  }

}
