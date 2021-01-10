<?php
require_once 'dbh.php';

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
<script
 src=" https://maps.googleapis.com/maps/api/js?key=key goes here"></script>



    <style>
    




html, body {
  overflow-x:hidden;
  height:100%;
}
#main { padding-right: 15px; }
            .infoWindow { width: 220px; }
 </style>

 
 <script>
 function arrIndex(arr, a, b, index){
    for (var i = 0 ; i<arr.length ; i++){
       if(arr[i].lat==a && arr[i].long==b){
          index = i;
          break;
       }
    }
    return index;
 }
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
 var map;
 var infowindow;
 var params;
 function init(val) {
        var center = new google.maps.LatLng(10.777730, -61.176800);
        infowindow = new google.maps.InfoWindow();
        var mapOptions = {
 zoom: 9,
 center: center,
 mapTypeId: google.maps.MapTypeId.ROADMAP
 }
         map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
         if(val==2){
          makeRequest('getlocations.php?val='+val, function(data) {
 //stores json objects in data variable
 var data = JSON.parse(data.responseText);
 var dist = [];
 for (var i = 0; i < data.length; i++) {
      var check = dist.some(d => d.lat== parseFloat(data[i].lat).toFixed(2) && d.long ==parseFloat(data[i].long).toFixed(2));
       
     if(check){
       var a = parseFloat(data[i].lat);
       var b = parseFloat(data[i].long);
       a = a.toFixed(2);
       b = b.toFixed(2);
       var idx = arrIndex(dist, a, b, -1);
     // indexOf("Array")
       console.log('idx: '+idx);
        dist[idx].count = parseInt(dist[idx].count)+1;
      
     
      //console.log(dist[2].lat +" "+ i)
       //dist[pos].count++;
     }
     else{
         var ress = data[i].translated_address;
         var lat = parseFloat(data[i].lat);
         var lng = parseFloat(data[i].long);
         dist[dist.length]={"lat": lat.toFixed(2), "long": lng.toFixed(2), "count":1, "ta":ress};
         console.log(ress)
     }
 }
 //console.log(data.length)
 //passes each marker stored in json object to be displayed on map
 for (var i = 0; i < dist.length; i++) {
   console.log(dist.length);
     displayLocation(dist[i], data.length);
 }
 });
 
         }
         else if(val==1 || val==3){
          makeRequest('getlocations.php', function(data) {
 //stores json objects in data variable
 var data = JSON.parse(data.responseText);
 var polyCoords = [];
 //passes each marker stored in json object to be displayed on map
 for (var i = 0; i < data.length; i++) {
     displayLocation2(data[i]);
     if(val==3){
       polyCoords.push({lat: parseFloat(data[i].lat), lng: parseFloat(data[i].long)});
     }
 }
   if(val==3){
     console.log(polyCoords);
     polyCoords.sort(function(a,b) {
  var x =  parseFloat(a.lat) - parseFloat(b.lat);
  return x == 0? parseFloat(a.lng) - parseFloat(b.lng) : x;
});
     console.log(polyCoords);
     var megaZone = new google.maps.Polygon({
    paths:  polyCoords,
    strokeColor: '#FF0000',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0.35
  });
  megaZone.setMap(map);

   }
 });
         }
         else{
             makeRequest('getlocations.php?val=4&'+val,function(data){
              var data = JSON.parse(data.responseText);
              for (var i = 0; i < data.length; i++) {
     displayLocation2(data[i]);
 }
             });
         }
         


      }
      
      function displayLocation(location, len) {
  // console.log(location)
 //display marker info
 var addr = location.ta;
//console.log(addr)
 var content =   '<div class="infoWindow"><strong>'  + Math.ceil(((location.count/len)*100)) + '% </strong>'
             + '<br/>'     + addr
             + '<br/>'     + location.lat + '<br/>'+ location.long + '</div>';

   var position = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.long));
 var marker = new google.maps.Marker({
     map: map,
     position: position,
     title: location.name
 });
 //show information attach to each marker when clicked on
 // Define the LatLng coordinates for the polygon.
 
 google.maps.event.addListener(marker, 'click', function() {
     infowindow.setContent(content);
     infowindow.open(map,marker);
 });
 //}
 var cityCircle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: {lat:parseFloat(location.lat),lng:parseFloat(location.long)},
            radius: Math.sqrt(parseInt(location.count)) * 1000*(location.count/len)
          });

 }
 function displayLocation2(location) {
  // console.log(location)
 //display marker info
 var addr = location.translated_address;
 var datet = location.post_time.split(" ");
//console.log(addr)
 var content =   '<div class="infoWindow"><article><strong>'  +'Address:</strong><br/>' +addr
             + '<br/>'     + '<strong>Description:</strong><br/>'+location.description + '<br/>' +'<strong>Exact Co-ordinates:</strong><br/>'
             + location.lat + ', '+ location.long + '<br/><strong>Post Date:</strong><br/>'+datet[0]+
             '<br/><strong>Post Time (24 hrs):</strong><br/>'+datet[1]+'</article></div>';

   var position = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.long));
 var marker = new google.maps.Marker({
     map: map,
     position: position,
     title: location.name
 });
 //show information attach to each marker when clicked on
 // Define the LatLng coordinates for the polygon.
 
 google.maps.event.addListener(marker, 'click', function() {
     infowindow.setContent(content);
     infowindow.open(map,marker);
 });
 //}
      
 }
 $(document).ready(function(){
  setTimeout(() => {
    init(1);
  }, 1000);
  $('#dateInput').hide();
   $('#inlineFormCustomSelect').on('change',function(){
    var params = "";
    var val = $('#inlineFormCustomSelect').val();
             if(val==4){
              $('#dateInput').show();
            
             var len = date1.length&&date2.length&&time1.length&&time2.length;
             
              $("#Mapchanger").on('click',function(){
                var date1=$("#date1").val();
             var date2=$("#date2").val();
             var time1=$("#time1").val();
             var time2=$("#time2").val();
                  params = 'date1='+date1+'&date2='+date2+'&time1='+time1+'&time2='+time2;
               
    
    setTimeout(() => {
                console.log(params)
       console.log(val)
      init(params);
     }, 1500);
      
              });
  
     
              
             }
             else{
              $('#dateInput').hide();
              setTimeout(() => {
       console.log(val)
      init(val)
     }, 2000);
             }
             
    
   });


 
  

   
    
  
   


 
  

 });

 </script>


    <title>View Updates</title>
  </head>
  <body id="body" style="background-color:#f5f5f5">
  <div class="form-group">
      <div class="col-md-4 mx-auto">
      <div class="input-group">
      <span class="" id="error"></span>
      <select class="custom-select mr-sm-2" id="inlineFormCustomSelect">
        <option value="1" selected>Display All Potential Locations</option>
        <option value="2">Possible Zones</option>
        <option value="3">Display Total Area Covered</option>
        <option value="4">Display Locations Based on Time Period</option>
      </select>
      <div class="input-group-addon input-group-button">
    <input style="float: right" type="button" class="btn btn-primary" id="Mapchanger" value="Go!"/>
    </div>
   </div>
   </div>
  </div>
   <div class="col-md-4 mx-auto" id="dateInput">
   <!-- date 1-->
   <div class="form-group">
    <label for="date1">Date 1</label>
    <input type="date" class="form-control" id="date1" >
  </div>
   <!-- date 2-->
   <div class="form-group">
    <label for="date2">Date 2</label>
    <input type="date" class="form-control" id="date2" >
  </div>
  <!-- time 1-->
  <div class="form-group">
    <label for="time1">Time 1</label>
    <input type="time" class="form-control" id="time1">
  </div>
  <div class="form-group">
    <label for="time2">Time 1</label>
    <input type="time" class="form-control" id="time2">
  </div>
   </div>
      <section id="main">
  <div id="map_canvas" class="mx-auto" style="width: 550px; height: 580px;border: 2px solid white; border-radius: 5px;" align="center"></div>
 

</section>



  </body>
</html>
