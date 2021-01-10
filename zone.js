var triangleCoords = [<?php
    $pid = 71;
    $s = "SELECT count(*) FROM `missing_pets_posts` where MP_ID='{$pid}'";
    $res = mysqli_query($conn,$s);
    $count = mysqli_fetch_array($res)[0];
    $sql = "SELECT * FROM `missing_pets_posts` where MP_ID='$pid' order by `lat`, `long` asc";
    $results = mysqli_query($conn,$sql);
    if(mysqli_num_rows($results)){
             while($row=mysqli_fetch_assoc($results)){
               echo ' {lat: '.$row["lat"].',lng:'.$row["long"].'}';
               $count--;

               if($count>0){
                    echo ',';

               }

}
}
   ?>];

// Construct the polygon.
var bermudaTriangle = new google.maps.Circle({
    paths: triangleCoords,
    strokeColor: '#FF0000',
    strokeOpacity: 0.8,
    strokeWeight: 3,
    fillColor: '#FF0000',
    fillOpacity: 0.01
});

bermudaTriangle.setMap(map);


//orginal
//function to get json file with the markers
function makeRequest(url, callback) {
    var request;
    if (window.XMLHttpRequest) {
        request = new XMLHttpRequest(); // IE7+, Firefox, Chrome, Opera, Safari
    } else {
        request = new ActiveXObject("Microsoft.XMLHTTP"); // IE6, IE5
    }
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            callback(request);
        }
    }
    request.open("GET", url, true);
    request.send();
}
//variable to set up map and display
var map;

//positions map to the center of T&T
var center = new google.maps.LatLng(10.777730, -61.176800);

var geocoder = new google.maps.Geocoder();
var infowindow = new google.maps.InfoWindow();
//function to create and set up map
function init() {

    var mapOptions = {
            zoom: 9,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        //prepares map to be displayed in map_canvas divider
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    //gets json data
    makeRequest('getlocations.php', function(data) {
        //stores json objects in data variable
        var data = JSON.parse(data.responseText);
        //passes each marker stored in json object to be displayed on map
        for (var i = 0; i < data.length; i++) {
            displayLocation(data[i]);
        }
    });
}







//display markers on map using info window
function displayLocation(location) {
    console.log(location)
        //display marker info
    var content = '<div class="infoWindow"><strong>' + location.description + '</strong>' +
        '<br/>' + location.post_time +
        '<br/>' + location.lat + '<br/>' + location.long + '</div>';

    if (parseInt(location.Long_tude) == 0) {
        geocoder.geocode({ 'Disaster Type': location.Disaster_Type }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    title: location.Disaster_Type
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
                });
            }
        });
    } else {
        var position = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.long));
        var marker = new google.maps.Marker({
            map: map,
            position: position,
            title: location.name
        });
        //show information attach to each marker when clicked on
        // Define the LatLng coordinates for the polygon.
        var triangleCoords = [<?php
       $pid = $_SESSION['pet_id_update'];
       $s = "SELECT count(*) FROM `missing_pets_posts` where MP_ID='{$pid}'";
       $res = mysqli_query($conn,$s);
       $count = mysqli_fetch_array($res)[0];
       $sql = "SELECT * FROM `missing_pets_posts` where MP_ID='$pid' order by `lat`, `long` asc";
       $results = mysqli_query($conn,$sql);
       if(mysqli_num_rows($results)){
                while($row=mysqli_fetch_assoc($results)){
                  echo ' {lat: '.$row["lat"].',lng:'.$row["long"].'}';
                  $count--;
   
                  if($count>0){
                       echo ',';
   
                  }
   
   }
   }
      ?>];
        // Construct the polygon.
        var bermudaTriangle = new google.maps.Polygon({
            paths: triangleCoords,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 3,
            fillColor: '#FF0000',
            fillOpacity: 0.35
        });
        bermudaTriangle.setMap(map);
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(content);
            infowindow.open(map, marker);
        });
    }
}