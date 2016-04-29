

jQuery(function($){
	function update_map_from_address(ask) {
		if(ask==false)
			var change = confirm("Czy chcesz uaktualnić mapę oraz współrzędne?");
		else
			var change = true;
		if(change==false)
			return;
		$('#monument-map').gmap3({
            getaddress: {
                address: $('#oz_city').val()+', '+$('#oz_address').val(),
                callback: function(results){
                    if(results) {
                    	console.log(results[0]);
                    	$('#monument-map').gmap3({
	                    	clear: "marker",
	                        marker: {
	                            latLng: results[0].geometry.location,
	                            options:{
									draggable:true
								},
								events:{
	              					dragend: function(marker){
	              						var pos = marker.getPosition();
	              						$("#oz_lat").val(pos.lat());
	              						$("#oz_lng").val(pos.lng());
	              						$("#oz_human_lat").val(dec2StrLat(pos.lat()));
	              						$("#oz_human_lng").val(dec2StrLng(pos.lng()));
	              					}
	              				}
	                        },
	                        map:{
	                            options: {
	                                center: results[0].geometry.location,
	                            }
	                        }
	                    });
	                    var pos = results[0].geometry.location;
  						$("#oz_lat").val(pos.lat());
  						$("#oz_lng").val(pos.lng());
  						$("#oz_human_lat").val(dec2StrLat(pos.lat()));
  						$("#oz_human_lng").val(dec2StrLng(pos.lng()));
	                }
	                else {
	                	$('#monument-map').gmap3({
	                    	clear: "marker"
	                    });
	                    $("#oz_lat").val("");
  						$("#oz_lng").val("");
  						$("#oz_human_lat").val("");
  						$("#oz_human_lng").val("");
	                }
	            }
            }
        });
	}

	function dec2StrLat(decLatitude) {
        var intDegree;
        var intMinute;
        var decSecond;
        strLatitude = "N";
        if (decLatitude < 0) {
            strLatitude = "S";
            decLatitude = decLatitude * -1;
        }

        intDegree = Math.floor(decLatitude);
        intMinute = Math.floor((decLatitude-intDegree) * 60);
        decSecond = myRound((decLatitude-intDegree-(intMinute/60))*3600);

        return String(intDegree)+"° " +String(intMinute)+"' "+String(decSecond)+"\" "+strLatitude;

    }

    function dec2StrLng(decLongitude) {
        var intDegree;
        var intMinute;
        var decSecond;
        strLongitude = "E";
        if (decLongitude < 0) {
            strLongitude = "W";
            decLongitude = decLongitude * -1;
        }
        intDegree = Math.floor(decLongitude);
        intMinute = Math.floor((decLongitude-intDegree) * 60);
        decSecond = myRound((decLongitude-intDegree-(intMinute/60))*3600);

        return String(intDegree)+"° " +String(intMinute)+"' "+String(decSecond)+"\" "+strLongitude;
    }

    function myRound(value){
		return Math.round(value*100)/100;
	}
	
	$(document).ready(function(){
		if($('#oz_lat').val()!='' && $('#oz_lng').val()!='') 
			var center = [$('#oz_lat').val(), $('#oz_lng').val()];

		else
			var center = [50.675107, 17.921298];

		$('#monument-map').gmap3({
			map:{ 
				options:{
					zoom: 15,
					center: center,
				}
			},
			marker: {
                latLng: center,
                options:{
					draggable:true
				},
				events:{
  					dragend: function(marker){
  						var pos = marker.getPosition();
  						$("#oz_lat").val(pos.lat());
  						$("#oz_lng").val(pos.lng());
  						$("#oz_human_lat").val(dec2StrLat(pos.lat()));
  						$("#oz_human_lng").val(dec2StrLng(pos.lng()));
  					}
  				}
            }
		});
		$('#oz_city').change(function(event) {
			if($('#oz_address').val()!='')
				update_map_from_address(false);
		});
		$('#oz_address').change(function(event) {
			if($('#oz_city').val()!='')
				update_map_from_address(false);
		});

		$('#update_map_from_address_button').click(function(event) {
			event.preventDefault();
			update_map_from_address(true);
		});

		$('#update_map_from_coords_button').click(function(event) {
			event.preventDefault();
			var center = [$('#oz_lat').val(), $('#oz_lng').val()];
			$('#monument-map').gmap3({
				clear: "marker",
                marker: {
                    latLng: center,
                    options:{
						draggable:true
					},
					events:{
      					dragend: function(marker){
      						var pos = marker.getPosition();
      						$("#oz_lat").val(pos.lat());
      						$("#oz_lng").val(pos.lng());
      						$("#oz_human_lat").val(dec2StrLat(pos.lat()));
      						$("#oz_human_lng").val(dec2StrLng(pos.lng()));
      					}
      				}
                },
                map:{
                    options: {
                        center: center,
                    }
                }
			});
			$("#oz_human_lat").val(dec2StrLat($('#oz_lat').val()));
  			$("#oz_human_lng").val(dec2StrLng($('#oz_lng').val()));

		});

		//if($('#oz_lat').val()!='' && $('#oz_lng').val()!='')
		//	update_map_from_address(true);
	});
});