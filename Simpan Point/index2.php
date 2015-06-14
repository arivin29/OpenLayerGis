<!DOCTYPE html>
<html>
<head>
<title>EPSG:4326 example</title>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.css" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.js"></script>
<style>
#map {
  position: relative;
}
#popup {
  padding-bottom: 45px;
}
</style>
</head>
<body>
<div class="container-fluid">

<div class="row-fluid">
  <div class="span12">
    <div id="map" class="map"> <div id="popup" class="">popup</div> </div>
    <button class="btn btn-primary" id="map2" class="">asdas</button>
    <button class="btn btn-primary" id="kirim_post" class="">Kirim</button>
    <form class="form-inline">
      <label>Geometry type &nbsp;</label>
      <select id="type">
        <option value="None">None</option>
        <option value="Point">Point</option>
        <option value="LineString">LineString</option>
        <option value="Polygon">Polygon</option>
        <option value="Circle">Circle</option>
        <option value="Square">Square</option>
        <option value="Box">Box</option>
      </select>
    </form>
	<div id="hasil"></div>
  </div> 
</div>

</div>
<script>
var layers = [
  new ol.layer.Tile({
    source: new ol.source.TileWMS({
      url: 'http://demo.boundlessgeo.com/geoserver/wms',
      params: {
        'LAYERS': 'ne:NE1_HR_LC_SR_W_DR'
      }
    })
  })
];

var map = new ol.Map({
  controls: ol.control.defaults().extend([
    new ol.control.ScaleLine({
      units: 'degrees'
    })
  ]),
  layers: layers,
  target: 'map',
  view: new ol.View({
    projection: 'EPSG:4326',
    center: [0, 0],
    zoom: 2
  })
});

//layar 2
// var source = new ol.source.Vector({wrapX: false});

// var vector = new ol.layer.Vector({
  // source: source,
  // projection: 'EPSG:3857',
  // style: new ol.style.Style({
    // fill: new ol.style.Fill({
      // color: 'rgba(1, 1, 255, 0.2)'
    // }),
    // stroke: new ol.style.Stroke({
      // color: '#ffcc33',
      // width: 2
    // }),
    // image: new ol.style.Circle({
      // radius: 7,
      // fill: new ol.style.Fill({
        // color: '#ffcc33'
      // })
    // })
  // })
// });


//akir layar 2 

//layar 3 untuk ka di buek vektor

  var source= new ol.source.Vector({
         url: 'mygeojson.json',
          format: new ol.format.GeoJSON() 
      });

  var jsons = new ol.layer.Vector({
      title: 'added Layer',
	  
	  
	  style: new ol.style.Style({
		fill: new ol.style.Fill({
		  color: 'rgba(1, 1, 255, 0.2)'
		}),
		stroke: new ol.style.Stroke({
		  color: '#ffcc33',
		  width: 2
		}),
		image: new ol.style.Circle({
		  radius: 7,
		  fill: new ol.style.Fill({
			color: '#ffcc33'
		  })
		})
	  }),
	  source:source
    
  }); 
  
  map.addLayer(jsons);
  
  
  
var typeSelect = document.getElementById('type');

var id = 0;
var draw; // global so we can remove it later
function addInteraction() {
  var value = typeSelect.value;
  if (value !== 'None') {
	  
    var geometryFunction, maxPoints;
     
    draw = new ol.interaction.Draw({
      source: source,
      type: /** @type {ol.geom.GeometryType} */ (value),
      geometryFunction: geometryFunction,
      maxPoints: maxPoints
    });
	
	
	draw.on('drawend', function(e) {
	  e.feature.setProperties({
		'id': id ++,
		'name': 'yourCustomName'
	  })
	  console.log(e.feature, e.feature.getProperties());
	});

	console.log(draw);
    map.addInteraction(draw);
	 
  } 
}
 
  
  //akir layar 3 gambar
  
var element = document.getElementById('popup');

var popup = new ol.Overlay({
  element: element,
  positioning: 'bottom-center',
  stopEvent: false
});

  
  map.on('click', function(evt) {
  var feature = map.forEachFeatureAtPixel(evt.pixel,
      function(feature, layer) {
        return feature;
      });
  if (feature) { 
    var geometry = feature.getGeometry();
    var coord = geometry.getCoordinates();
    popup.setPosition(coord);
	 console.log(feature.get('name'));
    
	$(element).popover({
      'placement': 'top',
      'html': true,
      'content': feature.get('name')
    });
	
    $(element).popover('show');
  } else {
    $(element).popover('destroy');
  }
});

  
	$(document).ready(function(){
	 
		$('#map2').on('click', function() {
			var source2 = jsons.getSource();
			var geojson  = new ol.format.GeoJSON();
			var features = source.getFeatures();
			var json     = geojson.writeFeatures(features);
			console.log(json);
		});
		
		
		$('#kirim_post').click(function() {
			var source2 = jsons.getSource();
			var geojson  = new ol.format.GeoJSON();
			var features = source.getFeatures();
			var json     = geojson.writeFeatures(features);
			var json     = {a:json};
		
			$.ajax({
			  type: 'POST',
			  data: json,
			  success: function(result) {
				  
				   $('#hasil').html(result);
				  
			  },
			  error: function(){ alert('eror') },
			  url: 'getVal.php',
			  cache:false
			});
		  });
  
	});
	
	
typeSelect.onchange = function(e) {
  map.removeInteraction(draw);
  addInteraction();
};
//addInteraction();
</script>
</body>
</html>