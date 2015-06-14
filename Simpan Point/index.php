<!DOCTYPE html>
<html>
<head>
<title>Draw features example</title>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.css" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.js"></script>

</head>
<body>
<div class="container-fluid">

<div class="row-fluid">
  <div class="span12">
    <div height="400px" id="map" class="map"></div>
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
  </div>
</div>

</div>
<script>
var raster = new ol.layer.Tile({
  source: new ol.source.MapQuest({layer: 'sat'})
});

var source = new ol.source.Vector({wrapX: false});

var vector = new ol.layer.Vector({
  source: source,
  projection: 'EPSG:3857',
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
  })
});


var map = new ol.Map({
  layers: [raster,vector],
  target: 'map',
  view: new ol.View({
    center: [-11000000, 4600000],
    zoom: 4 
	//center: ol.proj.transform([0, 0], 'EPSG:4326', 'EPSG:3857')
  })
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
      source: new ol.source.Vector({
         url: 'mygeojson.json',
         //format: new ol.format.GeoJSON()
		 
		  format: new ol.format.GeoJSON({ 
			 //projection: map.getView().getProjection()
			projection: 'EPSG:900913'
		  })
	  
      })
  });

  
  
  map.addLayer(jsons); 

var typeSelect = document.getElementById('type');

var draw; // global so we can remove it later
function addInteraction() {
  var value = typeSelect.value;
  if (value !== 'None') {
    var geometryFunction, maxPoints;
    if (value === 'Square') {
      value = 'Circle';
      geometryFunction = ol.interaction.Draw.createRegularPolygon(4);
	   
    } else if (value === 'Box') {
      value = 'LineString';
      maxPoints = 2;
      geometryFunction = function(coordinates, geometry) {
        if (!geometry) {
          geometry = new ol.geom.Polygon(null);
        }
        var start = coordinates[0];
        var end = coordinates[1];
        geometry.setCoordinates([
          [start, [start[0], end[1]], end, [end[0], start[1]], start]
        ]);
        return geometry;
      };
    }
    draw = new ol.interaction.Draw({
      source: source,
      type: /** @type {ol.geom.GeometryType} */ (value),
      geometryFunction: geometryFunction,
      maxPoints: maxPoints
    });
	console.log(draw);
    map.addInteraction(draw);
	 
  }
  
  
  var source2 = jsons.getSource();
	var geojson  = new ol.format.GeoJSON({projection: 'EPSG:900913'});
    var features = source.getFeatures();
    var json     = geojson.writeFeatures(features);
    console.log(json);
	
}

	


/**
 * Let user change the geometry type.
 * @param {Event} e Change event.
 */
typeSelect.onchange = function(e) {
  map.removeInteraction(draw);
  addInteraction();
};

addInteraction();

</script>
</body>
</html>