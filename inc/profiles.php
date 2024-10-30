<?php

	global $wp_inbox_gmap_loaded, $wp_inbox_stopwords;
	$wp_inbox_stopwords = array();
	$wp_inbox_gmap_loaded = false;

	function wp_inbox_get_user_address($user_id=0, $user_meta=array(), $force=false){
		
		$user_id = ($user_id==0?get_current_user_id():$user_id);
		
		
		if(empty($user_meta)){
			$user_meta_pre = get_user_meta($user_id);
			$user_meta_pre = is_array($user_meta_pre)?$user_meta_pre:array();
			
			$user_meta = array_map( function( $a ){ return $a[0]; },  $user_meta_pre);
		}
		
		//pree($user_meta);
		
		extract($user_meta);
		//pree($wp_user_address);
		if(isset($wp_user_address)){
			$wp_user_address_check = maybe_unserialize($wp_user_address);
			if(isset($wp_user_address_check['address'])){
				$force = (!$force && trim($wp_user_address_check['address'])=='');
			}
		}
		
		if(isset($wp_user_address) && !$force){
			$wp_user_address = maybe_unserialize($wp_user_address);
			$address = $wp_user_address['address'];
			
		}else{

			$address = array();
			$addresses = '';

			if(isset($billing_address_1) && $billing_address_1)
			$addresses .= $billing_address_1;
			
			if(isset($billing_address_2) && $billing_address_2)
			$addresses .= ' '.$billing_address_2;
			
			if(isset($addresses) && trim($addresses))
			$address[] = $addresses;
			
			if(isset($billing_city) && $billing_city)
			$address[] = $billing_city;
			
			if(isset($billing_state) && $billing_state)
			$address[] = $billing_state;
			
			if(isset($billing_postcode) && $billing_postcode)
			$address[] = $billing_postcode;
			
			if(isset($billing_country) && $billing_country)
			$address[] = $billing_country;		
			
			
			$address = implode(', ', $address);
			
			wp_inbox_update_address($user_id, $address);
		
		}
		
		return $address;

	}
	
	add_action('edit_user_profile_update', 'wp_inbox_update_extra_profile_fields');
	add_action( 'woocommerce_customer_save_address', 'wp_inbox_update_extra_profile_fields' );
		
	//add_action( 'woocommerce_save_account_details', 'wp_inbox_save_additional_account_details', 10, 1 ); 
	
	function wp_inbox_address_to_lat_lng($wp_user_address){

		global $wp_version;
		
		$wp_user_address = trim($wp_user_address);
	
		$latitude = '';
		$longitude = '';
		
		if($wp_user_address!=''){
			$prepAddr = str_replace(' ','+',$wp_user_address);
			$wp_inbox_gplus_api = get_option('wp_inbox_gplus_api', '');
			$url = 'https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key='.$wp_inbox_gplus_api;
			//pree($url);
			$geocode = '';//file_get_contents($url);
			
						
			
			$args = array(
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => true,
				'headers'     => array(),
				'cookies'     => array(),
				'body'        => null,
				'compress'    => false,
				'decompress'  => true,
				'sslverify'   => true,
				'stream'      => false,
				'filename'    => null
			); 
			
			if($wp_inbox_gplus_api){	
				$response = wp_remote_post( $url, $args );
				
								
				if ( is_wp_error( $response ) ) {
				   $error_message = $response->get_error_message();
				   echo "Something went wrong: $error_message";
				} else {
				   $geocode = $response['body'];
				}			
			
				if($geocode){
					$output= json_decode($geocode);
					//pree($output);
					$latitude = $output->results[0]->geometry->location->lat;
					$longitude = $output->results[0]->geometry->location->lng;	
				}
				
			}
			//pree($latitude);
			//pree($longitude);
		}else{
		}
		return array('latitude'=>$latitude, 'longitude'=>$longitude);	
	}
	
	
	function wp_inbox_update_address($user_id, $address) {

			$lat_lng = wp_inbox_address_to_lat_lng($address);

			extract($lat_lng);
			
			$address = array('address'=>$address, 'lat'=>$latitude, 'lng'=>$longitude);

			update_user_meta($user_id, 'wp_user_address', $address);
	}	
		
	function wp_inbox_update_extra_profile_fields($user_id) {
	 	//pree($user_id);
		$wp_user_address = wp_inbox_get_user_address($user_id, array(), true);
		
		if ( current_user_can('edit_user', $user_id) ){
			
			
			wp_inbox_update_address($user_id, $wp_user_address);
		
		
		}
	 
	}	
	 
	add_action('wp_footer', 'wp_inbox_map_on_search');
	
	
	 
	function wp_inbox_map_on_search(){
		
		global $wp_inbox_gmap_loaded;
		
		if($wp_inbox_gmap_loaded){
			return false;
		}
		
		//pree($wp_inbox_gmap_loaded);
		
				
		
		$address = wp_inbox_get_user_address();
		
?>

	<script type="text/javascript" language="javascript">
		jQuery(document).ready(function($){
			$('.search.search-results .shop-container').prepend('<div id="gmap" class="wp_inbox_gmap"></div>');//, .logged-in.single-product.renting .shop-container
			var gcontrols = '<input name="rm" type="button" value="-" /><input name="rp" type="button" value="+" />';
			var gfields = '<input type="text" id="filter_by_address" name="filter_by_address" value="<?php echo isset($_GET['a'])?$_GET['a']:''; ?>" placeholder="<?php echo __('Type any location here to filter the results', 'inbox'); ?>" /><input type="button" value="<?php echo __('Filter', 'inbox'); ?>" class="filter_btn button primary" />';
			$('<div class="gmap_controls">'+gcontrols+gfields+'</div>').insertAfter('.wp_inbox_gmap');
		});	
	
	</script>
    
<?php		
		wp_inbox_do_map_to('gmap', $address);
	}
	
	function wp_inbox_miles_to_meters($miles){
		
		return ($miles/0.00062137);
	}
	
	function wp_inbox_meters_to_miles($meters){
		
		return ($meters*0.00062137);
	}	
	
	
	
	function wp_inbox_do_map_to($id='map', $this_address=''){
		
		
		
		global $wpdb, $wp_inbox_gmap_loaded, $wp_ca_booking_users_ids;
		//$wp_ca_booking_users_ids[] = 44;
		//$wp_ca_booking_users_ids[] = 43;
		//$wp_ca_booking_users_ids[] = 20;
		
		$wp_inbox_gplus_api = get_option('wp_inbox_gplus_api', '');
		
		
		if(!trim($wp_inbox_gplus_api))
		return;
		
		
		
		
		
		
		
		
		

		$qry = '';
		
		$wp_inbox_gmap_loaded = true;
		
		
		
		$results = array();

		$searching_address = (isset($_GET['a'])?$_GET['a']:$this_address);//Model Town Lahore Pakistan.';


		
		$miles = (isset($_SESSION['searchRadius'])?$_SESSION['searchRadius']:'30m');				
		$miles = (int)$miles;
		$miles = ($miles>0?$miles:5);

		
		if(isset($_GET['s'])){
					
			$lat_lng = wp_inbox_address_to_lat_lng($searching_address);
			extract($lat_lng);
			
			$extra_query = '';
			$qry = "
			
			SELECT 
				
				user_id, 
				SUBSTR(SUBSTRING_INDEX(meta_value, 'lat\";d:', -1), 1, 10) AS lat, 
				SUBSTR(SUBSTRING_INDEX(meta_value, 'lng\";d:', -1), 1, 10) AS lng
			FROM 
				$wpdb->usermeta
			WHERE 
				meta_value REGEXP '.*\"address\";s:[0-9]+:.*'".$extra_query." 
			HAVING 
				(((acos(sin((".$latitude."*pi()/180)) * sin((lat*pi()/180))+cos((".$latitude."*pi()/180)) * cos((lat*pi()/180)) * cos(((".$longitude."- lng)*pi()/180))))*180/pi())*60*1.1515) <= $miles
				
			";
			//exit;
	
			
	
			
			
		
		}elseif(!empty($wp_ca_booking_users_ids)){
			$qry = wp_inbox_user_llq_by_ids($wp_ca_booking_users_ids);
			
			
		}
		//pree($results);exit;
		
		
		
		$getMeters = wp_inbox_miles_to_meters($miles);	
		
		if($qry)
		$markers = wp_inbox_map_markers($qry);
		
		
			
?>		
	<style type="text/css">
	#gmap {
		height: 400px;
		width: 100%;
		margin-bottom:30px;
	}
	.search-results #gmap{
		width:94%;
		margin: 0 auto 30px auto;
	}
	input[name="filter_by_address"]{
		width:400px;
		
	}
	input[type="button"].filter_btn{
		margin: 0 0 14px 8px;
		font-size: 14px;
	}		
	.logged-in.single-product.renting .shop-container .gmap_controls{
		display:none;
	}
	</style>
		<script type="text/javascript" language="javascript">
		
		var base_obj;
		var map_obj;
		var circler;
		var markers = [];
		
		
		var citymap = {
			<?php if(!empty($markers)): ?>
			<?php $f = 0; foreach($markers as $uid=>$arr): $f++; ?>
			<?php echo $uid; ?>: {
				  user_title: '<?php echo $arr['user_title']; ?>',
				  user_address: '<?php echo addSlashes($arr['user_address']); ?>',
				  user_lat: <?php echo $arr['user_lat']; ?>,
				  user_lng: <?php echo $arr['user_lng']; ?>,
				  user_image: '<?php echo $arr['user_image']; ?>',
				  user_link: '<?php echo $arr['user_link']; ?>'
				}<?php echo (count($markers)==$f?'':','); ?>	
			<?php endforeach; ?>
			<?php endif; ?>		
		};	
		
			
		function initMap() {
			var base_address = '<?php echo ($this_address?$this_address:'Florida, United States'); ?>';
			
			
			geocoder = new google.maps.Geocoder(); 
			geocoder.geocode( { 'address': base_address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					
					lati = results[0].geometry.location.lat();
					lngi = results[0].geometry.location.lng();	
					//console.log(lati+':'+lngi);	
					map_obj = new google.maps.Map(document.getElementById('<?php echo $id; ?>'), {
					  zoom: 8,
					  center: new google.maps.LatLng(lati, lngi),
					  mapTypeId: 'terrain'
					});
						
						
						
					circler = new google.maps.Circle({
						strokeColor: '#A6B2CA',
						strokeOpacity: 0.2,
						strokeWeight: 38,					
						fillColor: '#FFFFFF',
						fillOpacity: 0.1,
						map: map_obj,
						center: {lat: lati, lng: lngi},					
						radius: <?php echo $getMeters; ?>
					});
					base_obj = {user_title: base_address, user_address: base_address, user_lat: lati, user_lng: lngi, user_image: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png', user_link: ''};
					addMarker(base_obj, map_obj);
					for (var city in citymap) {
						addMarker(citymap[city], map_obj);
					}							
								
				}
			});
				
		}
		
		function setMapOnAll(map) {
			for (var i = 0; i < markers.length; i++) {
			  markers[i].setMap(map);
			}
		  }
		
			
		function addMarker(obj, map_obj) {
		 // console.log(title+': '+address);
			var myLatLng = {lat: obj.user_lat, lng: obj.user_lng};
		
			
			var marker = new google.maps.Marker({
				map: map_obj,
				position: myLatLng,
				//label: obj.user_title,
				icon: obj.user_image
			  });	
			markers.push(marker);
  
			var contentString = '<div class="pin-content"><a target="_blank" href="'+obj.user_link+'"><strong>'+obj.user_title+'</strong></a><br /><p>'+obj.user_address+'</p></div>';  
			//console.log(contentString);
			var infowindow = new google.maps.InfoWindow({
			  content: contentString
			});
			  
			marker.addListener('click', function() {
			  infowindow.open(map_obj, marker);
			});
			  
			//console.log(obj);
		}	  
		
		jQuery(document).ready(function($){		
		
		$('input[type="button"].filter_btn').on('click', function(){
			var url = "<?php echo home_url().'/?s='.$_GET['s'].'&post_type='.$_GET['post_type'].'&a='; ?>";
			var filter_by_address = $('input[name="filter_by_address"]').val();
			if(filter_by_address!=''){
				document.location.href = url+encodeURI(filter_by_address);
			}
			
		});		
		

		
		function push_item(id, title, url, img, home_url, currency_symbol, price){
			var item_html = '<div class="product type-product  product-small col col-md-3 has-hover post-'+id+'   has-post-thumbnail"><div class="col-inner 7"><div class="badge-container absolute left top z-1"></div><div class="product-small col-md-12"><div class="box-image"><div class="image-fade_in_back"><a href="'+url+'"><img src="'+img+'" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="'+title+'" title="'+title+'" /><img src="'+img+'" class="show-on-hover absolute fill hide-for-small back-image" alt="" /></a></div><div class="image-tools is-small top right show-on-hover"></div><div class="image-tools is-small hide-for-small bottom left show-on-hover"></div><div class="image-tools grid-tools text-center hide-for-small bottom hover-slide-in show-on-hover"><a class="quick-view quick-view-added" data-prod="'+id+'" href="#quick-view">Quick View</a></div></div><!-- box-image --><div class="box-text box-text-products text-center grid-style-2"><div class="title-wrapper">  <p class="category uppercase is-smaller no-text-overflow product-cat op-7"></p> <p class="name product-title"><a href="'+url+'">'+title+'</a></p></div><div class="price-wrapper"><span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'+currency_symbol+'</span>'+price+'</span></span></div><div class="add-to-cart-button"><a href="'+home_url+'/?product_cat&amp;s=a&amp;post_type=product&amp;add-to-cart='+id+'" rel="nofollow" data-product_id="'+id+'" class="ajax_add_to_cart add_to_cart_button product_type_simple button primary is-flat mb-0 is-small">Add to cart</a></div></div><!-- box-text -->				</div><!-- box --></div><!-- .col-inner --></div>';			
			
			$('.shop-container .products').append(item_html);
		}
		
		<?php if(isset($_GET['s'])): ?>
			$('body').on('click', '.gmap_controls input[type="button"]', function(){
				
				var radius_pad = 6000;
				var radius = circler.radius;
				
				switch($(this).attr('name')){
					default:
					case "rp":
						radius+=radius_pad;
					break;
					case "rm":
						if(typeof circler!="undefined" && circler.radius>=radius_pad)
						radius-=radius_pad;
					break;
				}
				
				
				if(typeof circler!="undefined"){
					circler.setRadius(radius);
			
				}else{
					$('#gmap, .gmap_controls').fadeOut();
				}
				
				var data = {
					'action': 'wpinboxsearch',
					's': '<?php echo $_GET['s']; ?>',
					'meters':radius,
					'searching_address':'<?php echo $searching_address; ?>'
				};		
				citymap = {};						
				$.post(wp_inbox.ajaxurl, data, function(response) {
					setMapOnAll(null);
					var response = $.parseJSON(response);

					$.each(response.markers, function(i, v){
					
						
						citymap[i] = {};
						$.each(v, function(key, value){
							citymap[i][key] = value;
						});
				
					});
					for (var city in citymap) {
						addMarker(citymap[city], map_obj);
					}	
					
					$('.shop-container .products').html('');
					$.each(response.items, function(vendor_id, products){
						$.each(products, function(product_id, product_data){
							push_item(product_id, product_data.title, product_data.link, product_data.img_url, wp_inbox.home_url, wp_inbox.currency, product_data.price);
						});
					});
				});
			});
		<?php endif; ?>			
				
		});
		
		
		var placeSearch, autocomplete;
		var componentForm = {
		street_number: 'short_name',
		route: 'long_name',
		locality: 'long_name',
		administrative_area_level_1: 'short_name',
		country: 'long_name',
		postal_code: 'short_name'
		};
		
		function initAutocomplete() {
		// Create the autocomplete object, restricting the search to geographical
		// location types.
		autocomplete = new google.maps.places.Autocomplete(
			/** @type {!HTMLInputElement} */(document.getElementById('filter_by_address')),
			{types: ['geocode']});
		
		// When the user selects an address from the dropdown, populate the address
		// fields in the form.
		// autocomplete.addListener('place_changed', fillInAddress);
		}
		
		function fillInAddress() {
		// Get the place details from the autocomplete object.
		var place = autocomplete.getPlace();
		
		for (var component in componentForm) {
		  document.getElementById(component).value = '';
		  document.getElementById(component).disabled = false;
		}
		
		// Get each component of the address from the place details
		// and fill the corresponding field on the form.
		for (var i = 0; i < place.address_components.length; i++) {
		  var addressType = place.address_components[i].types[0];
		  if (componentForm[addressType]) {
			var val = place.address_components[i][componentForm[addressType]];
			document.getElementById(addressType).value = val;
		  }
		}
		}
		
		// Bias the autocomplete object to the user's geographical location,
		// as supplied by the browser's 'navigator.geolocation' object.
		function geolocate() {
		if (navigator.geolocation) {
		  navigator.geolocation.getCurrentPosition(function(position) {
			var geolocation = {
			  lat: position.coords.latitude,
			  lng: position.coords.longitude
			};
			var circle = new google.maps.Circle({
			  center: geolocation,
			  radius: position.coords.accuracy
			});
			autocomplete.setBounds(circle.getBounds());
		  });
		}
		}
		
		
		var iac = setInterval(function(){
					if(typeof initAutocomplete != "undefined"){
						initAutocomplete();
						clearInterval(iac);
					}
		}, 3000);			
        </script>
        
<?php   
		if($wp_inbox_gplus_api)
		echo '<script src="https://maps.googleapis.com/maps/api/js?key='.$wp_inbox_gplus_api.'&libraries=places&callback=initMap" async defer></script>';     
		
        }
		
		add_action( 'wp_ajax_wpinboxsearch', 'wp_inbox_search' );
		function wp_inbox_search(){
			//pree($_POST);
			$miles = wp_inbox_meters_to_miles($_POST['meters']);
			//pree($miles);exit;		
					
			$product_search = wp_inbox_product_search(array('s'=>$_POST['s']));
			//pree($product_search);exit;
			
			$vendors = array_keys($product_search);
			
			$qry = wp_inbox_search_query($_POST['searching_address'], $miles, $vendors);	
			
			
			
			$markers = wp_inbox_map_markers($qry, $vendors);
			
			//pree($product_search);
			//pree($markers);
			
			$valid_vendor_products = array_intersect_key($product_search, $markers);
			
			//pree($valid_vendor_products);exit;
			
			$resp = array('markers'=>$markers, 'items'=>$valid_vendor_products);
			echo json_encode($resp);
			
			exit;
		}
		function wp_inbox_user_llq_by_ids($wp_ca_booking_users_ids = array()){
			
			if(empty($wp_ca_booking_users_ids))
			return;
			
			$qry = "
			
			SELECT 
				
				user_id, 
				SUBSTR(SUBSTRING_INDEX(meta_value, 'lat\";d:', -1), 1, 10) AS lat, 
				SUBSTR(SUBSTRING_INDEX(meta_value, 'lng\";d:', -1), 1, 10) AS lng
			FROM 
				$wpdb->usermeta
			WHERE 
				user_id IN (".implode(',', $wp_ca_booking_users_ids).")
				
			";		
			
			return $qry;
		}
		
		function wp_inbox_search_query($searching_address, $miles, $vendors=array()){
			
			global $wpdb;
			
			
			
			$lat_lng = wp_inbox_address_to_lat_lng($searching_address);
			extract($lat_lng);
			
			$extra_query = '';
			
			if(!empty($vendors)){
				$extra_query = ' AND user_id IN ('.implode(',', $vendors).') ';
			}
			
			$qry = "
			
				SELECT 
					
					user_id, 
					SUBSTR(SUBSTRING_INDEX(meta_value, 'lat\";d:', -1), 1, 10) AS lat, 
					SUBSTR(SUBSTRING_INDEX(meta_value, 'lng\";d:', -1), 1, 10) AS lng
				FROM 
					$wpdb->usermeta
				WHERE 
					meta_value REGEXP '.*\"address\";s:[0-9]+:.*'".$extra_query."
					
				HAVING 
					(((acos(sin((".$latitude."*pi()/180)) * sin((lat*pi()/180))+cos((".$latitude."*pi()/180)) * cos((lat*pi()/180)) * cos(((".$longitude."- lng)*pi()/180))))*180/pi())*60*1.1515) <= $miles
					
			";	
			
			return $qry;
		}
		
		
		function wp_inbox_map_markers($qry){
			global $wpdb;
			$markers = array();
			$results = $wpdb->get_results($qry);
			if(!empty($results)){
				//pree($results);exit;
				foreach($results as $user){
					$user_data = get_userdata( $user->user_id );
					$address = get_user_meta($user->user_id, 'wp_user_address', true);
					//pree($user_data);exit;
					$location['user_title'] = ($user_data->display_name?$user_data->display_name:$user_data->user_login);
					$location['user_link'] = get_author_posts_url($user->user_id);	
					$location['user_image'] = get_avatar_url($user->user_id, array('size'=>32));
					$location['user_address'] = (isset($address['address'])?$address['address']:'');
					$location['user_lat'] = (isset($address['lat'])?$address['lat']:"'"."'");
					$location['user_lng'] = (isset($address['lng'])?$address['lng']:"'"."'");
					$markers[$user->user_id] = $location;				
					//$markers[]
				}
				//pree($markers);exit;
			}		
					
                	
			return $markers;	
		}
				
		function wp_inbox_get_search_stopwords() {
			global $wp_inbox_stopwords;
			
			if ( isset( $wp_inbox_stopwords ) )
			return $wp_inbox_stopwords;
		 
			/* translators: This is a comma-separated list of very common words that should be excluded from a search,
			 * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
			 * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
			 */
			$words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
				'Comma-separated list of search stopwords in your language' ) );
		 
			$stopwords = array();
			foreach ( $words as $word ) {
				$word = trim( $word, "\r\n\t " );
				if ( $word )
					$stopwords[] = $word;
			}
		 
			/**
			 * Filters stopwords used when parsing search terms.
			 *
			 * @since 3.7.0
			 *
			 * @param array $stopwords Stopwords.
			 */
			$wp_inbox_stopwords = apply_filters( 'wp_search_stopwords', $stopwords );
			return $wp_inbox_stopwords;
		}		
					
		function wp_inbox_parse_search_terms( $terms ) {
			$strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
			$checked = array();
		 
			$stopwords = wp_inbox_get_search_stopwords();
		 
			foreach ( $terms as $term ) {
				// keep before/after spaces when term is for exact match
				if ( preg_match( '/^".+"$/', $term ) )
					$term = trim( $term, "\"'" );
				else
					$term = trim( $term, "\"' " );
		 
				// Avoid single A-Z and single dashes.
				if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) )
					continue;
		 
				if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
					continue;
		 
				$checked[] = $term;
			}
		 
			return $checked;
		}				
		function wp_inbox_parse_search( $q ) {
			global $wpdb, $query;
		 
			$search = '';
		 
			// added slashes screw with quote grouping when done early, so done later
			$q['s'] = stripslashes( $q['s'] );
			if ( empty( $_GET['s'] ) )// && $query->is_main_query()
				$q['s'] = urldecode( $q['s'] );
			// there are no line breaks in <input /> fields
			$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );
			$q['search_terms_count'] = 1;
			if ( ! empty( $q['sentence'] ) ) {
				$q['search_terms'] = array( $q['s'] );
			} else {
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
					$q['search_terms_count'] = count( $matches[0] );
					$q['search_terms'] = wp_inbox_parse_search_terms( $matches[0] );
					// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
					if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
						$q['search_terms'] = array( $q['s'] );
				} else {
					$q['search_terms'] = array( $q['s'] );
				}
			}
		 
			$n = ! empty( $q['exact'] ) ? '' : '%';
			$searchand = '';
			$q['search_orderby_title'] = array();
		 
			/**
			 * Filters the prefix that indicates that a search term should be excluded from results.
			 *
			 * @since 4.7.0
			 *
			 * @param string $exclusion_prefix The prefix. Default '-'. Returning
			 *                                 an empty value disables exclusions.
			 */
			$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );
		 
			foreach ( $q['search_terms'] as $term ) {
				// If there is an $exclusion_prefix, terms prefixed with it should be excluded.
				$exclude = $exclusion_prefix && ( $exclusion_prefix === substr( $term, 0, 1 ) );
				if ( $exclude ) {
					$like_op  = 'NOT LIKE';
					$andor_op = 'AND';
					$term     = substr( $term, 1 );
				} else {
					$like_op  = 'LIKE';
					$andor_op = 'OR';
				}
		 
				if ( $n && ! $exclude ) {
					$like = '%' . $wpdb->esc_like( $term ) . '%';
					$q['search_orderby_title'][] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
				}
		 
				$like = $n . $wpdb->esc_like( $term ) . $n;
				$search .= $wpdb->prepare( "{$searchand}(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_excerpt $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s))", $like, $like, $like );
				$searchand = ' AND ';
			}
		 
			if ( ! empty( $search ) ) {
				$search = " AND ({$search}) ";
				if ( ! is_user_logged_in() ) {
					$search .= " AND ({$wpdb->posts}.post_password = '') ";
				}
			}
		 
			return $search;
		}		

		function wp_inbox_product_search($q=array(), $offset=0, $limit=10){
			global $wpdb;
			$qry = "SELECT ID, post_title, post_author FROM $wpdb->posts WHERE $wpdb->posts.post_type IN ('product') AND $wpdb->posts.post_status IN ('publish')";
			//pree($qry);exit;
			//pree($q);exit;
			$qry .= wp_inbox_parse_search($q);
			$qry .= "LIMIT $offset, $limit";
			//pree($qry);exit;
			$searched = $wpdb->get_results($qry);			
			$products_by_vendors = array();	
			
			if(!empty($searched)){
				foreach($searched as $posts){
					$featured_img_url = get_the_post_thumbnail_url($posts->ID,'full'); 	
					$_product = wc_get_product( $posts->ID );
					$price = $_product->get_price();
					$products_by_vendors[$posts->post_author][$posts->ID] = array('title'=>$posts->post_title, 'link'=>get_permalink($posts->ID), 'img_url'=>$featured_img_url, 'price'=>$price);
				}
			}
			return $products_by_vendors;
		}
		
		function wp_inbox_init_profiles(){
		
			
			
			
		}
		add_action('init', 'wp_inbox_init_profiles');