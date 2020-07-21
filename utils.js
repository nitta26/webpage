function map_plot(lon, lat){
	// Marker
	var marker = new google.maps.Marker( {
		map: map ,
		position: new google.maps.LatLng(lon, lat) ,
	} ) ;
}
