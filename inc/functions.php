<?php

	

	if ( ! defined( 'ABSPATH' ) ) {

		exit; // Exit if accessed directly

	}
	
	global $wp_inbox_mail_headers;

	$wp_inbox_mail_headers = array("MIME-Version: 1.0\r\n");
	$wp_inbox_mail_headers[] = "Reply-To: ". get_bloginfo('admin_email') . "\r\n";
    $wp_inbox_mail_headers[] = "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$wp_inbox_mail_headers[] = 'From: '.get_bloginfo( 'name' ).' <'.get_bloginfo('admin_email').'>';
	
	function wp_inbox_mail($arr=array()){
		
		global $wp_inbox_mail_headers;
		
		

		if(empty($arr)){
			$arr = array('to'=>'', 'subject'=>'', 'body'=>'', 'template_id'=>'');
		}
		
		
		$ret = array('msg'=>false);
		
		
		$to = trim(isset($_POST['to'])?$_POST['to']:$arr['to']);
		
		$user = get_user_by( 'email', $to );
		
		if(!empty($user)){
			$mail_notification = get_inbox_mail_notification($user->ID);
			
			if($mail_notification){
			
				$subject = isset($_POST['subject'])?$_POST['subject']:$arr['subject'];
				$body = isset($_POST['body'])?(array)$_POST['body']:$arr['body'];		
				$template_id = (isset($_POST['body'])?(array)$_POST['body']:(isset($arr['template_id'])?$arr['template_id']:839));
				
				
				$body['SITE_NAME'] = get_bloginfo( 'name' );
				$body['SITE_URL'] = get_bloginfo( 'wpurl' );
				$body['SLOGAN'] = get_bloginfo( 'description' );
				
				//pree($body);
				
				if(function_exists('wp_inbox_smart_mail_get')){
					
					//pree($template_id);			
					//pree($body);			
					$body_plus = stripslashes(wp_inbox_smart_mail_get($template_id, $body));
					//pree($body_plus);
					if(wp_mail( array($to), $subject, $body_plus, $wp_inbox_mail_headers )){
						$ret['msg'] = true;
					}	
				}		
			}
		}
		
		
		//echo json_encode($ret);		
		//exit;
		return $ret;
		
	}
	
	add_action('wp_ajax_wp_inbox_mail', 'wp_inbox_mail');
	add_action('wp_ajax_nopriv_wp_inbox_mail', 'wp_inbox_mail');
	
	function wp_inbox_mail_notification($arr=array()){
		

		$switch = isset($_POST['switch'])?$_POST['switch']:'off';
		
		$ret = 'success';

		if(is_user_logged_in()){
			update_user_meta(get_current_user_id(), 'mail_notification', $switch);
		}else{
			$ret = 'failed';
		}
		echo $ret;
		exit;
		
	}	
	
	add_action('wp_ajax_wp_inbox_mail_notification', 'wp_inbox_mail_notification');
	
	function get_inbox_mail_notification($user_id=0){
		
		$mail_notification = get_user_meta(($user_id?$user_id:get_current_user_id()), 'mail_notification', true);
		
		$mail_notification = ($mail_notification==''?'on':$mail_notification);
		
		$mail_notification = ($mail_notification=='on');
		
		return $mail_notification;
	}

	
	
	function wp_inbox_smart_mail_get($slug='', $arr = array()){
		
		$ret = '';
		
		if($slug!=''){
			
			$args = array(
				'posts_per_page'   => 1,
				'name'           => $slug,
				'post_type'        => 'page',
				'post_status'      => 'draft'
			);
			
			$mail_template = get_post( $slug );	
			//pre($mail_template);
			if (!empty($mail_template)) {
				
				$ret = $mail_template->post_content;
				if(!empty($arr)){
					foreach($arr as $k=>$v){
						$ret = str_replace($k, $v, $ret);
					}
				}
				
			}
		}
		
		
		return $ret;
	}
		
	function wp_inbox_mailer($arr=array()){
	
		extract($arr);
		
		$subject = __('You\'ve received a message from', 'inbox').' '.$from_name;
		
		$params = array('GREETINGS_TEXT'=>$subject, 'USER_NAME'=>$to_name, 'BODY_TEXT'=>'Dear '.$to_name.',<br /><br />'.$from_name.' '.__('left you a message in your inbox', 'inbox').':<br /><br /><br />"'.$message.'"<br /><br /><br />', 'CALL_TO_TEXT'=>''.__('View and Reply', 'inbox').'', 'CALL_TO_ACTION'=>get_bloginfo('url').'/inbox/'.$thread_link);
		
		//echo '<div style="display:none;"';
		$mail_arr = array('template_id'=> 836,'to'=>$to_email, 'subject'=>$subject, 'body'=>$params);
		//pree($mail_arr);
		$ret = wp_inbox_mail($mail_arr);
		//echo '</div>';
		
		return $ret;
		
	}	

	

	

	if(!function_exists('wp_inbox_pre')){

	function wp_inbox_pre($data){

			if(isset($_GET['debug'])){

				wp_inbox_pree($data);

			}

		}	 

	} 	

	if(!function_exists('wp_inbox_pree')){

	function wp_inbox_pree($data){

				echo '<pre>';

				print_r($data);

				echo '</pre>';	

		

		}	 

	} 

	

	

	function wp_inbox_messages($str = ''){

		

		$words = get_option('wp_inbox_messages', 'skype');

		

		$ret = __('Terms of Service reminder: Providing email, Skype, or phone number is only allowed if it is needed as part of the service.', 'inbox').' '.__('Otherwise, all communication must go through ', 'inbox').get_bloginfo('name').'.';

		

		return array('words'=>$words, 'alert'=>$ret);



	}

	



	function wp_inbox_menu()

	{

		global $wp_inbox_data;

		$title = $wp_inbox_data['Name'];

		add_options_page($title, $title, 'install_plugins', 'wp_inbox', 'wp_inbox');



		add_submenu_page(null, $title.' '.__('Threads', 'inbox'), $title.' '.__('Threads', 'inbox'), 'manage_options', 'wp-inbox-threads', 'wp_inbox_view_threads');



	}

	function wp_inbox_unread_threads($user_id=0){
		$user_id = ($user_id?$user_id:get_current_user_id());
		global $wpdb;
		$ret = 0;
		$urquery = "SELECT COUNT(*) AS unread FROM $wpdb->usermeta WHERE user_id=$user_id AND meta_key LIKE '_unread_status_%' AND meta_value=1";
		
		$_unread_status = $wpdb->get_row($urquery);
		
		if(!empty($_unread_status)){
			$ret = $_unread_status->unread;
		}
		
		return $ret;
		
	}

	function wp_inbox_view_threads(){





		if ( !current_user_can( 'install_plugins' ) )  {



			wp_die( __( 'You do not have sufficient permissions to access this page.', 'inbox' ) );



		}

		

		global $wpdb, $wp_inbox_dir;



		$id = (isset($_GET['id'])?wp_inbox_sanitize($_GET['id']):0);

		$hid = isset($_GET['h'])?wp_inbox_sanitize($_GET['h']):0;

		

		if($id>0){

			

			

			

			$umeta_results = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE umeta_id=".$id." LIMIT 1");

			

			

			if(!empty($umeta_results)){

			

					//pree($umeta_results);

					

					$ms = maybe_unserialize($umeta_results->meta_value);

					$ms = is_array($ms)?$ms:array();

					

					

					if($umeta_results->user_id==get_current_user_id()){

						$sender_id = str_replace('INBOX_', '', $umeta_results->meta_key);

					}else{

						$sender_id = $umeta_results->user_id;

					}

					//pree($sender_id);

					

					$sender = get_user_by( 'id', $sender_id );

					

					

					$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_$sender_id' AND user_id=".get_current_user_id().")";

				

					//pree($urquery);

				

					$_unread_status = $wpdb->query($urquery);



	

					include($wp_inbox_dir.'/inc/inbox_view_single.php');		

					

			}

		}elseif($hid>0){

				

				$query = "SELECT * FROM $wpdb->usermeta WHERE umeta_id=".$hid." LIMIT 1";

	

				

				$umeta_results = $wpdb->get_row($query);

				

			

								 

				

				if(!empty($umeta_results)){

				

						$ms = maybe_unserialize($umeta_results->meta_value);

						$ms = is_array($ms)?$ms:array();

						

						

						$dept_id = str_replace('HELP_BOX_', '', $umeta_results->meta_key);

						

						if($umeta_results->user_id==get_current_user_id()){

							$sender_id = str_replace('HELP_BOX_', '', $umeta_results->meta_key);

						}else{

							$sender_id = $umeta_results->user_id;

						}

						//pree($sender_id);

						

						$sender = get_user_by( 'id', $sender_id );

						

							

						$dept_data = get_option(wp_inbox_option_by_id($dept_id), array());

						

						$dept_desc = '';

						

						if(is_array($dept_data))

						list($dept_name, $dept_desc) = $dept_data;						

						

						

						if($umeta_results->user_id==get_current_user_id())

						$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_".$sender_id."' AND user_id=".get_current_user_id().")";

						else

						$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_".$sender_id."' AND user_id=$dept_id)";

						

						//pree($urquery);

						

						$_unread_status = $wpdb->query($urquery);						

						

	

						include($wp_inbox_dir.'/inc/inbox_view_single_help.php');





					

				}

				

			}		

	}



	function wp_inbox(){ 







		if ( !current_user_can( 'install_plugins' ) )  {







			wp_die( __( 'You do not have sufficient permissions to access this page.', 'inbox' ) );







		}







		global $wpdb; 



		



				

		include('inbox_settings.php');	



		



	}

	

	

	function wp_inbox_plugin_links($links) { 



		global $wp_inbox_premium_link, $wp_inbox_pro;





		$settings_link = '<a href="admin.php?page=wp_inbox">'.__('Settings', 'inbox').'</a>';



		

		if($wp_inbox_pro){

			array_unshift($links, $settings_link); 

		}else{

			 
			if($wp_inbox_premium_link){
				$wp_inbox_premium_link = '<a href="'.esc_url($wp_inbox_premium_link).'" title="'.__('Go Premium', 'inbox').'" target="_blank">'.__('Go Premium', 'inbox').'</a>'; 
				array_unshift($links, $settings_link, $wp_inbox_premium_link); 
			}else{
				array_unshift($links, $settings_link); 
			}

		

		}

				

		

		return $links; 

	}

	

	function wp_inbox_setup(){

		global $wp_inbox_pages;

		$pages_required = array(

								'Inbox'=>0,

								'Help'=>0,

								//'User Profile Edit'=>0

								);

								

		if(!empty($pages_required)){			

			foreach($pages_required as $required=>$id){

				//wp_inbox_pree($required);

				$status = get_page_by_title( $required );

				

				//wp_inbox_pree($status);exit;

				if(empty($status)){

					$my_post = array(

					  'post_title'    => wp_strip_all_tags( $required ),

					  'post_content'  => '[WP-'.str_replace(' ', '-', strtoupper($required)).']',

					  'post_status'   => 'publish',

					  'post_type'     => 'page',

					  'post_author'   => get_current_user_id(),

					);

					

					//wp_inbox_pree($my_post);

					// Insert the post into the database

					if(is_super_admin())

					$status = wp_insert_post( $my_post );					

					//wp_inbox_pree($status);

				}

				

				$pages_required[$required] = (isset($status->ID)?$status->ID:$status);

				

				//wp_inbox_pree($status);

			}

			

		

		}

		

		$wp_inbox_pages = $pages_required;

		

		//exit;	

	}

	



	function wp_inbox_update(){

		

		if(isset($_GET['page']) && $_GET['page']=='wp_inbox'){

			wp_inbox_setup();

		}



		

		if(!empty($_POST) && isset($_POST['wp_inbox'])){

			 

			global $wp_inbox_currency, $wp_inbox;

			$wp_inbox_currency = (function_exists('get_woocommerce_currency_symbol')?get_woocommerce_currency_symbol():'');


			

				

			if ( 

				! isset( $_POST['wp_inbox_field'] ) 

				|| ! wp_verify_nonce( $_POST['wp_inbox_field'], 'wp_inbox_action' ) 

			) {

			

			   _e( 'Sorry, your nonce did not verify.', 'inbox');

			   exit;

			

			} else {

			

			   

					//update_option('wp_inbox', $wp_inbox_updated);

			  

	

			   		

			   

			}

			

			

		}

		

				

		if(isset( $_POST['wp_inbox_dept_field'] )){		

			if ( 

				! isset( $_POST['wp_inbox_dept_field'] ) 

				|| ! wp_verify_nonce( $_POST['wp_inbox_dept_field'], 'wp_inbox_dept_action' ) 

			) {

			

			   _e( 'Sorry, your nonce did not verify.', 'inbox');

			   exit;

			

			} else {

			

			   // process form data

			  $wp_inbox_dept_id = is_numeric(wp_inbox_decode($_POST['wp_inbox_dept_staff']))?wp_inbox_decode($_POST['wp_inbox_dept_staff']):0;

			   

			  $wp_inbox_option_exists = wp_inbox_option_exists($wp_inbox_dept_id);

			   

			   if($wp_inbox_dept_id!='' &&  $wp_inbox_option_exists){

			  

				   $wp_inbox_dept_staff_key = 'wp_inbox_department_'.$wp_inbox_dept_id.'_staff';

				   

				   $wp_inbox_dept = $_POST['wp_inbox_dept'];

				   

				   update_option($wp_inbox_dept_staff_key, $wp_inbox_dept);

				   //pree($_POST);
			   

			   }

			   

			  //exit;

			}		

		}
		
		
		
		

		if(!empty($_POST) && isset($_POST['wp_inbox_gplus_api'])){

				

			if ( 

				! isset( $_POST['wp_inbox_gplus_field'] ) 

				|| ! wp_verify_nonce( $_POST['wp_inbox_gplus_field'], 'wp_inbox_gplus_action' ) 

			) {

			

			   _e( 'Sorry, your nonce did not verify.', 'inbox');

			   exit;

			

			} else {

			

			   		$wp_inbox_gplus_api = wp_inbox_sanitize($_POST['wp_inbox_gplus_api']);

					update_option('wp_inbox_gplus_api', $wp_inbox_gplus_api);

			  
			}

			

			

		}		

		

	}

	

	add_action('admin_init', 'wp_inbox_update');	

	

	

	

	

	function wp_inbox_init(){

		

		wp_inbox_setup();


		if(is_user_logged_in() && !is_admin()){

			global $wp_inbox_timezone;

			$wp_inbox_timezone = get_user_meta( get_current_user_id(), '_wp_inbox_timezone', true);
			if(!$wp_inbox_timezone){		
				$wp_inbox_timezone = get_option('timezone_string');
			}			
			
		}		

	}

	

	add_action('init', 'wp_inbox_init');
	


	function wp_inbox_sanitize( $input ) {
		if(is_array($input)){		
			$new_input = array();	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?wp_inbox_sanitize($val):stripslashes(sanitize_text_field( $val )));
			}			
		}else{
			$new_input = stripslashes(sanitize_text_field($input));			
			if(stripos($new_input, '@') && is_email($new_input)){
				$new_input = sanitize_email($new_input);
			}
			if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
				$new_input = esc_url($new_input);
			}			
		}	
		return $new_input;
	}


	function wp_inbox_front_scripts() {

		

		global $wp, $wp_inbox_pages, $wp_inbox_currency, $wpdb, $post, $wp_inbox_pro, $is_chat_ajax_based;

	    $request = explode( '/', $wp->request );

        $inbox_page = 'no';

        if(is_object($post) && $post->post_type == 'page'&& has_shortcode($post->post_content, 'WP-INBOX')){

            $inbox_page = 'yes';
        }


		$wp_ca_get_pages = function_exists('wp_ca_get_pages')?wp_ca_get_pages():array();

		

		//pree($wp_ca_get_pages);exit;		//if(empty($_POST) && get_the_ID() && in_array(get_the_ID(), $wp_inbox_pages) || is_product() || array_key_exists(get_the_ID(), $wp_ca_get_pages)){

		

	

		//}

        wp_enqueue_script('date-scripts', plugins_url('js/date.js', dirname(__FILE__)), array('jquery'), date('Ymhi'));
		
		
		if(!is_checkout() && !is_account_page()){//! ( end($request) == 'my-account'
			wp_enqueue_style( 'wp-inbox-front-bs-css', plugins_url('bootstrap/css/bootstrap.min.css', dirname(__FILE__)), array(), date('mhi') );
			
			wp_enqueue_script('wp-inbox-front-popper', plugins_url('bootstrap/js/popper.min.js', dirname(__FILE__)), array('jquery'), date('Ymhi'));
			wp_enqueue_script('wp-inbox-front-bs', plugins_url('bootstrap/js/bootstrap.min.js', dirname(__FILE__)), array('jquery'), date('Ymhi'));
		}


        if($wp_inbox_pro && !$is_chat_ajax_based){

            wp_enqueue_script('chat-scripts', plugins_url('pro/chat.js', dirname(__FILE__)), array('jquery'), date('Ymhi'));


        }

		wp_enqueue_script('front-scripts', plugins_url('js/front-scripts.js?t='.time(), dirname(__FILE__)), array('jquery'), date('Ymhi'));

		wp_enqueue_style( 'wp-inbox-common', plugins_url('css/common-styles.css', dirname(__FILE__)), array(), date('mhi') );	

		wp_enqueue_style( 'wp-inbox-front', plugins_url('css/front-style.css', dirname(__FILE__)), array(), date('mhi') );

        $sender_receiver = wp_inbox_enqueue_chat_ids();
        $sender_receiver = $sender_receiver ?? array();

        $translation_array = array(

            'home_url' => home_url(),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'currency'=>$wp_inbox_currency,
            'unread'=>wp_inbox_unread_threads(),
            'sender_id' => $sender_receiver['sender_id'] ?? -1,
            'receiver_id' => $sender_receiver['receiver_id'] ?? -1,
            'is_inbox_page' => $inbox_page,
            'is_pro' => $wp_inbox_pro,
            'is_chat_ajax_based' => $is_chat_ajax_based,

        );


        $pro_array = function_exists('wp_inbox_get_pro_array') ? wp_inbox_get_pro_array() : array();



        $translation_array = array_merge($translation_array, $pro_array);
        wp_localize_script( 'date-scripts', 'wp_inbox', $translation_array );


    }

	

		

	function wp_inbox_admin_scripts() {

		

		if(isset($_GET['page']) && in_array($_GET['page'], array('wp_inbox', 'wp-inbox-threads'))){

		

			wp_enqueue_style('wp-inbox-common', plugins_url('css/common-styles.css', dirname(__FILE__)));	

			wp_enqueue_style('wp-inbox-admin', plugins_url('css/admin-style.css?t'.time(), dirname(__FILE__)));

			

			if(is_admin()){
				
				$inbox_options = get_option('wpinbox_options', array());

			    $translation_array = array(
			            'this_url' => admin_url( 'options-general.php?page=wp_inbox' ),
                        'wp_inbox_nonce_field' => wp_create_nonce('wp_inbox_nonce_action'),
						'nonce' => wp_create_nonce('wpinbox_update_options_nonce'),
						'empty_settings' => empty($inbox_options),
						'msg_required' => __('Please type a message to send.', 'inbox'),
						'date_required' => __('Please provide message deliver date.', 'inbox'),
                );


				
				if (isset($_GET['page']) && $_GET['page'] == 'wp_inbox') {
	
					wp_enqueue_style( 'jquery-ui-css', plugins_url('css/jquery-ui.min.css', dirname(__FILE__)), array(), date('mhi') );
					wp_enqueue_script('jquery-ui-datepicker');
					wp_enqueue_script('wp-inbox-scripts', plugins_url('js/admin-scripts.js?t'.time(), dirname(__FILE__)), array('jquery'));

					wp_localize_script( 'wp-inbox-scripts', 'wp_inbox', $translation_array );

					wp_enqueue_style( 'wp-inbox-bs', plugins_url('bootstrap/css/bootstrap.min.css', dirname(__FILE__)), array(), date('mhi') );

					wp_enqueue_script('wp-scripts-bs', plugins_url('bootstrap/js/bootstrap.min.js', dirname(__FILE__)), array('jquery'), date('Ymhi'));
					
				}
		

			}else{

				wp_enqueue_script('wp-inbox-scripts', plugins_url('js/front-scripts.js?t='.time(), dirname(__FILE__)), array('jquery'), date('Ymhi'));					

			}
				

		

		}

		

	}		

		
	
	add_action('wp_ajax_wpinbox_update_option', 'wpinbox_update_option');
	
	if(!function_exists('wpinbox_update_option')){
		function wpinbox_update_option(){
	
	
	
			if(isset($_POST['wpinbox_update_option_nonce'])){
	
				$nonce = $_POST['wpinbox_update_option_nonce'];
	
				$return = array(
	
					
				);
	
				if ( ! wp_verify_nonce( $nonce, 'wpinbox_update_options_nonce' ) )
					die (__("Sorry, your nonce did not verify.", 'wp-docs'));
	
				if(isset($_POST['wpinbox_options'])){
	
					$inbox_options = isset($_POST['wpinbox_options']) ? $_POST['wpinbox_options'] : array();
					
					//pree($inbox_options);
	
	
					$sanitized_option = sanitize_wpdocs_data($inbox_options);
	
					$return = $sanitized_option;
	
					update_option('wpinbox_options', $sanitized_option);
					
					//pree($update);
				}
	
	
	
	
	
				echo  json_encode($return);
	
			}
	
			wp_die();
	
		}
	}



	function wp_inbox_get_pages($reverse=false){

		global $wp_inbox_pages;

		

		$ret = array();

		

		if(!empty($wp_inbox_pages)){

			$i = 0;

			foreach($wp_inbox_pages as $title => $content){ $i++;

				$page = get_page_by_title( $title, ARRAY_A, 'page' );

				

				//pree($page);

				

				$t = 'X-'.$i;

				$e = 'N-'.$i;

				if(empty($page))

				$ret[$e] = $title;

				else{

					if($page['post_status']=='publish')

					$ret[$page['ID']] = $title;

					else

					$ret[$t] = $title;

				}

			}

		}
		if($reverse){
			if(!empty($ret)){
				$nr = array();
				foreach($ret as $id=>$title){
					$slug = wp_inbox_underscore($title);
					$nr[$slug] = $id;
				}
				$ret = $nr;
			}
		}
		
		

		return $ret;

		

	}

	

	

	function wp_inbox_header_scripts(){

		$menus = wp_get_nav_menus();

		$pages = wp_inbox_get_pages();

		$wp_inbox_messages = wp_inbox_messages();

		

		//pree($pages);exit;

		$obj_ids = array();

		

		foreach ( $menus as $menu ):

		$menu_items = wp_get_nav_menu_items($menu->name);

		//pree($menu_items);

		if(!empty($menu_items)){

			foreach($menu_items as $items){

				$obj_ids[$items->object_id][] = $items->ID;

			}

		}

		endforeach;		

?>

	<style type="text/css">

		<?php if(!is_user_logged_in() && !empty($pages)){ foreach($pages as $id=>$title){ $ids = isset($obj_ids[$id])?$obj_ids[$id]:array();

			

			if(!empty($ids)){ foreach($ids as $ds){ 

		?>

		.menu-item-<?php echo $ds; ?>{ display:none !important; }

		

		<?php } } } } ?>

		@media only screen and (max-device-width: 480px) {

			

			

		}			

	</style>

    <script type="text/javascript" language="javascript">

		var alarming_words = '<?php echo $wp_inbox_messages['words']; ?>';

		var alarming_words_arr = alarming_words.split(',');

		var alarming_words_alert = '<?php echo $wp_inbox_messages['alert']; ?>';

		var msg;

		jQuery(document).ready(function($){

			

			var found;

			

			$('textarea[name="wp_inbox_message"]').keypress(function(){

				

				msg = $(this).val().toLowerCase();

		

				msg = msg.split(' ');

				

				found = false;

				

				$.each(alarming_words_arr, function(i, v){

					if(!found){

						if($.inArray(v, msg)>=0) {

							found = true;

						}else{

							

						}

					}

				});

				

				if(found){

					if($('.wp_inbox_message_alert').length>0){

						$('.wp_inbox_message_alert').fadeIn();

					}else{						

						$('<div class="wp_inbox_message_alert alert alert-danger clear-fix">'+alarming_words_alert+'</div>').insertAfter($('textarea[name="wp_inbox_message"]'));

					}												

				}else{

					$('.wp_inbox_message_alert').hide();

				}

				

			

			});

		});

	</script>

<?php		

		

	}

	

	add_action('wp_head', 'wp_inbox_header_scripts');	

	

	

	function wp_inbox_is_in_good_control($post_id){

		$ret = false;

	

		if(is_user_logged_in()){

	

			if ( get_post_status ( $post_id ) ) {

				$post = get_post($post_id);

				if(is_admin() || $post->post_author==get_current_user_id()){		

					$ret = true;

					

					if($post->post_author==get_current_user_id()){

						update_post_meta($post->ID, '_tlms_ustatus', 'replied');

						update_post_meta($post->ID, '_tlms_astatus', 'unread');

					}else{

						update_post_meta($post->ID, '_tlms_ustatus', 'unread');

						update_post_meta($post->ID, '_tlms_astatus', 'replied');						

					}

				}

			}

		}



		return $ret;

	}

	
	function wp_inbox_get_thread_id($sender_id, $receiver_id=0){
		
		global $wpdb;
		
		if($receiver_id>0){
			$query = "
					SELECT 
							* 
					FROM 
							$wpdb->usermeta 
					WHERE 
							(
								((user_id=".$sender_id." AND meta_key LIKE 'INBOX_%') OR (meta_key = 'INBOX_$sender_id'))
								
								AND
								
								((user_id=".$receiver_id." AND meta_key LIKE 'INBOX_%') OR (meta_key = 'INBOX_$receiver_id'))
							)
							
				";
		}else{
			
			$query = "SELECT * FROM $wpdb->usermeta WHERE ((user_id=".$sender_id." AND meta_key LIKE 'INBOX_%') OR (meta_key = 'INBOX_$sender_id'))";
		}
		
		//echo $query;

		$res = $wpdb->get_results($query);		
		
		return $res;
	}
	 

	function wp_inbox_threads(){

		

		ob_start();

		

	
		if(is_user_logged_in()){

			

			global $wpdb, $wp_inbox_pages, $wp_inbox_dir, $wp_inbox_pro;

			

			$user_id = get_current_user_id();

			

			$id = isset($_GET['i'])?wp_inbox_sanitize($_GET['i']):0;

			$hid = isset($_GET['h'])?wp_inbox_sanitize($_GET['h']):0;



			if(function_exists('wp_inbox_get_live_chat_meta_id')){

                $chat_meta_id = wp_inbox_get_live_chat_meta_id();

                if($hid == 0 && isset($_GET['receiver_h'])){
                    $hid = $chat_meta_id;
                }

                if($id ==0 && isset($_GET['receiver_i'])){
                    $id = $chat_meta_id;
                }
            }



			

			if($id){

				

				$umeta_results = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE umeta_id=".$id." AND ((user_id=".$user_id." AND meta_key LIKE 'INBOX_%') OR (meta_key = 'INBOX_$user_id'))");

				

				

				if(!empty($umeta_results)){

				

						//pree($umeta_results);

						

						$ms = maybe_unserialize($umeta_results->meta_value);

						$ms = is_array($ms)?$ms:array();

						

						

						if($umeta_results->user_id==get_current_user_id()){

							$sender_id = str_replace('INBOX_', '', $umeta_results->meta_key);

						}else{

							$sender_id = $umeta_results->user_id;

						}

						//pree($sender_id);

						

						$sender = get_user_by( 'id', $sender_id );

						

						

						$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_$sender_id' AND user_id=".get_current_user_id().")";

					

						//pree($urquery);

					

						$_unread_status = $wpdb->query($urquery);

						

	?>	
<?php

		

						include($wp_inbox_dir.'/inc/inbox_view_single.php');





					

				}else{

					_e('Sorry, you are not allowed to access this thread.', 'inbox');

				}

				

			}elseif($hid){

				

				$query = "SELECT * FROM $wpdb->usermeta WHERE umeta_id=".$hid." AND (user_id=".$user_id." AND meta_key LIKE 'HELP_BOX_%')";

				

				

				$depts = wp_inbox_dept_by_staff();

				

				

				if(!empty($depts)){

					$query = "SELECT * FROM $wpdb->usermeta WHERE umeta_id=".$hid." AND ((user_id=".$user_id." AND meta_key LIKE 'HELP_BOX_%')";

					

					foreach($depts as $dept){

						$query .= "OR (meta_key = 'HELP_BOX_$dept') ";

					}

					$query .= ')';

				}

								

				

				$umeta_results = $wpdb->get_row($query);

				

				



								 

				

				if(!empty($umeta_results)){

				

						$ms = maybe_unserialize($umeta_results->meta_value);

						$ms = is_array($ms)?$ms:array();

						

						

						$dept_id = str_replace('HELP_BOX_', '', $umeta_results->meta_key);

						

						if($umeta_results->user_id==get_current_user_id()){

							$sender_id = str_replace('HELP_BOX_', '', $umeta_results->meta_key);

						}else{

							$sender_id = $umeta_results->user_id;

						}

						//pree($sender_id);

						

						$sender = get_user_by( 'id', $sender_id );

						

							

						$dept_data = get_option(wp_inbox_option_by_id($dept_id), array());

						

						$dept_desc = '';

						

						if(is_array($dept_data))

						list($dept_name, $dept_desc) = $dept_data;						

						

						

						if($umeta_results->user_id==get_current_user_id())

						$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_".$sender_id."' AND user_id=".get_current_user_id().")";

						else

						$urquery = "UPDATE $wpdb->usermeta SET meta_value=0 WHERE (meta_key = '_unread_status_".$sender_id."' AND user_id=$dept_id)";

						

						//pree($urquery);

						

						$_unread_status = $wpdb->query($urquery);						

						

	?>		
  
<?php

		

						include($wp_inbox_dir.'/inc/inbox_view_single_help.php');





					

				}

				

			}else{

		
	
				$res = wp_inbox_get_thread_id($user_id);

				$depts = wp_inbox_dept_by_staff();

				

				$query = "SELECT * FROM $wpdb->usermeta WHERE (user_id=".$user_id." AND meta_key LIKE 'HELP_BOX_%') ";

				if(!empty($depts)){

					foreach($depts as $dept){

						$query .= "OR (meta_key = 'HELP_BOX_$dept') ";

					}

				}

				//echo $query;

				$res_help = $wpdb->get_results($query);

		

				//pree($res_help);

                if(isset($_GET['admin_messages'])){

				    include($wp_inbox_dir.'/inc/inbox_admin_view.php');

                }else{

				    include($wp_inbox_dir.'/inc/inbox_view.php');

                }



			}

		

		}else{

			_e('Sorry, you are not allowed to access this thread.', 'inbox');

		}
		
		
		$out1 = ob_get_contents();		

		

		ob_end_clean();
		

		return $out1;

	}

	

	add_shortcode('WP-INBOX', 'wp_inbox_threads');

	add_shortcode('WP-USERS-STRIP', 'wp_users_strip');
	
	add_shortcode('WP-USERS-LIST', 'wp_users_list');

	add_shortcode('WP-HELP', 'wp_inbox_help');

	

	//wp_inbox_dept_id
	
	
	if(!function_exists('wp_users_strip')){
		function wp_users_strip(){
			
			ob_start();
			
			_e('Sorry, you are not allowed to access the premium features.', 'inbox');
			
			$out1 = ob_get_contents();		
			ob_end_clean();
			
	
			return $out1;		
		}
	}
	
	
	if(!function_exists('wp_users_list')){
		function wp_users_list(){
			
			ob_start();
			
			_e('Sorry, you are not allowed to access the premium features.', 'inbox');
			
			$out1 = ob_get_contents();		
			ob_end_clean();
			
	
			return $out1;		
		}
	}
		

	function get_inbox_departments(){

		global $wpdb;

		

		$ret = array();

		

		$res = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '%wp_inbox_departments_%'");

		//pree($res);

		if(!empty($res)){

			foreach($res as $r){				

				$v = maybe_unserialize($r->option_value);

				list($title, $desc) = $v;

				$slug = wp_inbox_underscore($title);

				$ret[$slug] = array($r->option_id, $title, $desc);

			}

			ksort($ret);

		}

		
		//pree($ret);
		

		

		return $ret;

	}

	

	function wp_inbox_help(){

		

		global $wp_inbox_dir;

		$departments = get_inbox_departments();		

		ob_start();

		

		include_once($wp_inbox_dir.'templates/wp_inbox_help.php');

		

		$out1 = ob_get_contents();		

		

		ob_end_clean();

		

		return $out1;

		

	}

	

	function wp_inbox_date($t){
		
		$ret = date('d M, Y h:i A', $t);
		
		if(is_user_logged_in() && !is_admin()){
			global $wp_inbox_timezone;
			if($wp_inbox_timezone){
				$userTimezone = new DateTimeZone($wp_inbox_timezone);
				$myDateTime = new DateTime('2016-03-21 13:14');
							
				$tz = 'Europe/London';
				$dt = new DateTime("now", new DateTimeZone($wp_inbox_timezone));
				$dt->setTimestamp($t);
				$ret = $dt->format('d M, Y h:i A');		
			}
		}	
		
		return $ret;

	}

	

	function wp_inbox_contact_seller_on_product_page(){

		global $wp_inbox_woo_activated;

		

		if(!$wp_inbox_woo_activated)

		return;

		

		global $post;

		

		if($post->post_author!=get_current_user_id()){

			wp_inbox_message_box($post->post_author);

		}

		

		//wp_inbox_message_box($receiver_id);

		

	}

	

	add_action('woocommerce_before_add_to_cart_form', 'wp_inbox_contact_seller_on_product_page');

	

	function wp_inbox_encode($key){

		

		$encoded = base64_encode($key);

		

		return $encoded;

	}

	

	

	function wp_inbox_decode($key){

		

		$decoded = base64_decode($key);

		

		return $decoded;

	}



	function wp_order_message_box($receiver_id, $products=array()){

		

		$inbox = false;

		$author = get_user_by('id', $receiver_id);

		//pree($author->display_name);
		$author_name = (!empty($author)?$author->display_name:'');

		

		

		

		

?>

<div class="wp_inbox_message_box_toggle_div">

<?php if(!$inbox): ?>

<a class="wp_inbox_message_box_toggle"><?php echo __('Contact', 'inbox').' '.$author_name; ?></a>

<div class="wp_inbox_message_box">

<?php if(!empty($products)): ?>

<ul class="wp_inbox_products">

<?php foreach($products as $product): ?>

<li><a href="<?php echo get_permalink($product->ID); ?>" target="_blank" title="<?php echo $product->post_title; ?>">

<img src="<?php echo get_the_post_thumbnail_url($product->ID, 'thumb'); ?>" alt="<?php echo $product->post_title; ?>" />

<h6><?php echo $product->post_title; ?></h6>

</a></li>

<?php endforeach; ?>

</ul>

<?php endif; ?>

<?php endif; ?>

<textarea id="wp_inbox_message" name="wp_inbox_message" placeholder="<?php _e('Type message here to send', 'inbox'); echo ' '.$author->display_name; ?>..."></textarea>

<input class="button wp_inbox_message_send lin" type="button" value="<?php _e('Send Message', 'inbox'); ?>" /><input class="button wp_inbox_message_cancel" type="button" value="<?php _e('Cancel', 'inbox'); ?>" /><br />

<input type="hidden" id="wp_nonce" value="<?php echo wp_inbox_encode($receiver_id); ?>" />



<?php if(!$inbox): ?>

</div>



<?php endif; ?>

<div class="wp_inbox_message_success alert alert-success clear-fix"></div>

</div>

<?php		

	}

	function wp_inbox_options($key=''){
		$inbox_options = get_option('wpinbox_options', array());
		//pree($inbox_options);
		$is_key = array_key_exists($key, $inbox_options);	
		return $is_key;
	}

	function wp_inbox_message_box($receiver_id, $inbox=false, $customization=array()){



	$customization['button_text'] = (isset($customization['button_text']) && $customization['button_text']?$customization['button_text']:__('Send Message', 'inbox'));
	$customization['default_text'] = (isset($customization['default_text']) && $customization['default_text']?$customization['default_text']:'');
	$customization['caption_text'] = (isset($customization['caption_text']) && $customization['caption_text']?$customization['caption_text']:'');
		

?>

<div class="wp_inbox_message_box_toggle_div">

<?php if(!$inbox): ?>
<?php if(wp_inbox_options('contact_seller')): ?>
<a class="wp_inbox_message_box_toggle"><?php _e('Contact Seller', 'inbox'); ?></a>
<?php endif; ?>

<div class="wp_inbox_message_box">

<?php endif; ?>
<strong><?php echo $customization['caption_text']; ?></strong>
<textarea id="wp_inbox_message" class="form-control" name="wp_inbox_message" placeholder="<?php _e('Type message here to send', 'inbox'); ?>..."><?php echo $customization['default_text']; ?></textarea>

<?php if(is_user_logged_in()): ?>

<input class="button wp_inbox_message_send lin" type="button" value="<?php echo $customization['button_text']; ?>" /><input class="button wp_inbox_message_cancel" type="button" value="<?php _e('Cancel', 'inbox'); ?>" /><br />

<?php 

else:

?>

<input class="button wp_inbox_message_send lout" type="button" value="<?php _e('Login to Send Message', 'inbox'); ?>" /><br />

<?php endif; ?>

<input type="hidden" id="wp_nonce" value="<?php echo wp_inbox_encode($receiver_id); ?>" />

<?php if(!$inbox): ?>

</div>

<?php endif; ?>

<div class="wp_inbox_message_success alert alert-success clear-fix"></div>

</div>

<?php		

	}

	

	function wp_help_message_box($receiver_id, $sender_id){

	//pree($receiver_id);

	//pree($sender_id);

	

		

?>

<textarea id="wp_inbox_message" class="form-control" name="wp_inbox_message" placeholder="<?php _e('Type message here to send', 'inbox'); ?>..."></textarea>

<input id="wp_help_message_send" class="button wp_help_message_send" type="button" value="<?php _e('Send Message', 'inbox'); ?>" /><br />

<input type="hidden" id="wp_nonce" value="<?php echo wp_inbox_encode($receiver_id); ?>" />

<input type="hidden" id="wp_nonces" value="<?php echo wp_inbox_encode($sender_id); ?>" />

<div class="wp_inbox_message_success alert alert-success clear-fix"></div>

<?php		

	}	

	

	function update_wp_inbox($obj_id, $str_arr, $user_id=false, $box_type='INBOX_'){
		

		$o_message = (is_array($str_arr)?array_filter($str_arr, 'strlen'):array($str_arr));


		//pree($str_arr);
		//pree("$obj_id, $user_id, $box_type");
		$ret = false;

		if(!empty($str_arr)){

			//pree($str_arr);

			$user_id = ($user_id?$user_id:get_current_user_id());



            //pree($obj_id);pree($user_id);exit;

			

			$sender_id = $user_id;

			$receiver_id = $obj_id;


			$key = $box_type.$sender_id;			

			$ms = get_user_meta($receiver_id, $key, true);

			



			

			

			//pree($ms);

			if(!empty($ms)){	

				$ms = is_array($ms)?$ms:array();			

				$ms[] = array($str_arr, $user_id, time());	

				update_user_meta($receiver_id, $box_type.$sender_id, $ms);


			}else{

				$key = $box_type.$receiver_id;

				$ms = get_user_meta($sender_id, $key, true);	

				$ms = is_array($ms)?$ms:array();			
				//pree($ms);
				$ms[] = array($str_arr, $user_id, time());	
				//pree($ms);
				
				//pree("$sender_id, $box_type.$receiver_id, $ms");
				update_user_meta($sender_id, $box_type.$receiver_id, $ms);			

			}

			

			//exit;

			update_user_meta($receiver_id, '_unread_status_'.$sender_id, true);	
			
			//pree(get_current_user_id());
			//pree($sender_id);
			//pree($receiver_id);

			$o_sender_id  = ($sender_id==get_current_user_id()?$sender_id:$receiver_id);
			$o_receiver_id  = ($sender_id!=get_current_user_id()?$sender_id:$receiver_id);
			
			
			$sender = get_user_by( 'id', $o_sender_id );
			$receiver = get_user_by( 'id', $o_receiver_id );
			
			$receivers = array();
			
			
			switch($box_type){
				case 'HELP_BOX_':
					//pree($receiver);
					if(!empty($receiver)){}else{
						
						$wp_inbox_dept_staff_key = 'wp_inbox_department_'.$o_receiver_id.'_staff';
						$dept_staff = get_option($wp_inbox_dept_staff_key, array());
						//pree($dept_staff);
						if(!empty($dept_staff)){
							
							$dept_staff_ids = array();
							
							foreach($dept_staff as $id=>$status){
								$dept_staff_ids[] = $id;	
							}
							
							if(!empty($dept_staff_ids)){
							
								$args = array(
									'blog_id'      => $GLOBALS['blog_id'],
									'role'         => '',
									'role__in'     => array(),
									'role__not_in' => array(),
									'meta_key'     => '',
									'meta_value'   => '',
									'meta_compare' => '',
									'meta_query'   => array(),
									'date_query'   => array(),        
									'include'      => $dept_staff_ids,
									'exclude'      => array(),
									'orderby'      => 'login',
									'order'        => 'ASC',
									'offset'       => '',
									'search'       => '',
									'number'       => '',
									'count_total'  => false,
									'fields'       => 'all',
									'who'          => '',
								 ); 
								$dept_staff_users = get_users( $args );
								
								if(!empty($dept_staff_users)){
									foreach($dept_staff_users as $staff_users){
										$receivers[] = $staff_users;
									}
								}
							}
						}
					}
					
				
				default:
					$receivers[] = $receiver;
				
			}
			
			if(!empty($receivers)){
				foreach($receivers as $receiver){
			
						
					$arr = array(
						'from_name'=>$sender->display_name,
						'to_name'=>$receiver->display_name,
						'message'=> implode(' > ', $o_message),
						'to_email'=>$receiver->user_email,
						'thread_link'=>'',
					);
					//pree($arr);
					$results = wp_inbox_mailer($arr);			
				}
			}
			
			//pree($results);

			$ret = true;

		}

		return $ret;

	}

	

		

	function wp_inbox_message_send() {

		// Save logic goes here. Don't forget to include nonce checks!

		$ret = __('Please wait, there is some problem in the system.', 'inbox');

		$obj_id = wp_inbox_decode($_POST['obj_id']);



		
		if(!strpos($_POST['wp_inbox_ref_link'], '/inbox'))
		$_POST['wp_inbox_message'] .= '<br /><br /><a target="_blank" href="'.$_POST['wp_inbox_ref_link'].'">'.$_POST['wp_inbox_ref_link'].'</a>';

		$ret = $obj_id;




		$updated = update_wp_inbox($obj_id, nl2br($_POST['wp_inbox_message']), get_current_user_id());

		

		if($updated){

			$ret = __('Your message has been sent successfully.', 'inbox');

		}



		echo $ret;

		exit;

	}

	

	add_action( 'wp_ajax_wpinboxmessagesend', 'wp_inbox_message_send' );

	

	

		

	function wp_help_message_send() {

		// Save logic goes here. Don't forget to include nonce checks!

		$ret = __('Please wait, there is some problem in the system.', 'inbox');

		$obj_id = wp_inbox_decode($_POST['obj_id']);

		$obj_id2 = wp_inbox_decode($_POST['obj_id2']);



		$ret = $obj_id;

		if(!strpos($_POST['wp_inbox_ref_link'], '/inbox'))
		$_POST['wp_inbox_message'] .= '<br /><br /><a target="_blank" href="'.$_POST['wp_inbox_ref_link'].'">'.$_POST['wp_inbox_ref_link'].'</a>';
		

		$updated = update_wp_inbox($obj_id, array('', '', nl2br($_POST['wp_inbox_message'])), $obj_id2, 'HELP_BOX_');

		

		if($updated){

			$ret = __('Your message has been sent successfully.', 'inbox');
			
		

		}



		echo $ret;

		exit;

	}

	

	add_action( 'wp_ajax_wphelpmessagesend', 'wp_help_message_send' );	





	

	function wp_inbox_help_page() {



		$ret = array('desc'=>'<strong>'.__('Sorry!', 'inbox').'</strong> '.__('Please login, only registered users are allowed to contact.', 'inbox'), 'msg'=>false);

		

		if(is_user_logged_in()){

			

			$obj_id = ($_POST['wp_inbox_dept']);

			$wp_inbox_help_email = wp_inbox_sanitize($_POST['wp_inbox_help_email']);

			$wp_inbox_help_subject = wp_inbox_sanitize($_POST['wp_inbox_help_subject']);

			$wp_inbox_help_description = nl2br(wp_inbox_sanitize($_POST['wp_inbox_help_description']));

			

			$updated = update_wp_inbox($obj_id, array($wp_inbox_help_email, $wp_inbox_help_subject, $wp_inbox_help_description), get_current_user_id(), 'HELP_BOX_');

				

			if($updated){

				$ret['desc'] = __('Your message has been sent successfully.', 'inbox');

				$ret['msg'] = true;

			}

	

		}

		echo json_encode($ret);

		exit;

	}

	

	add_action( 'wp_ajax_wpinboxhelppage', 'wp_inbox_help_page' );
	add_action( 'wp_ajax_nopriv_wpinboxhelppage', 'wp_inbox_help_page' );

	
	function wp_inbox_timezone_update() {



		$ret = array('desc'=>'<strong>'.__('Sorry!', 'inbox').'</strong> '.__('Please login, only registered users are allowed to update timezone settings.', 'inbox'), 'msg'=>false, 'Error'=>is_user_logged_in().'-'.get_current_user_id());

		

		if(is_user_logged_in()){

			
			$wp_timezone = wp_inbox_sanitize($_POST['timezone']);

			update_user_meta( get_current_user_id(), '_wp_inbox_timezone', $wp_timezone );
			
			/*$ret[] = $wp_timezone;	
			$ret[] = $updated;	
			$ret[] = get_current_user_id();				
			$ret[] = get_user_meta( get_current_user_id(), '_wp_inbox_timezone', true);*/

			

			$ret['desc'] = __('Timezone settings are saved.', 'inbox');

			$ret['msg'] = true;


	

		}

		echo json_encode($ret);

		exit;

	}

	
	add_action( 'wp_ajax_wpinboxtz', 'wp_inbox_timezone_update' );
	add_action( 'wp_ajax_nopriv_wpinboxtz', 'wp_inbox_timezone_update' );
	
	//add_action( 'wp_ajax_nopriv_wpinboxmessagesend', 'wp_inbox_message_send' );

		

	function wp_inbox_humanize($str){

		return ucwords(str_replace(array('_', '-'), ' ', $str));

	}

	

	function wp_inbox_underscore($str){

		return str_replace(' ', '_',  strtolower($str));

	}



	function wp_inbox_option_by_id($option_id=0){

		global $wpdb;

		

		$row = array();

		if($option_id>0)

		$row = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_id=$option_id LIMIT 1");

		

		return (!empty($row)?$row->option_name:array());

	}

		

	function wp_inbox_option_exists($option_id=0){

		global $wpdb;

		

		$row = array();

		if($option_id>0)

		$row = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_id=$option_id LIMIT 1");

		

		return (!empty($row));

	}

	

	function wp_inbox_dept_remove() {

		

		$ret = array();

		

		$wp_inbox_dept_id = is_numeric(wp_inbox_decode($_POST['wp_inbox_dept_id']))?wp_inbox_decode($_POST['wp_inbox_dept_id']):0;

		

		$ret[] = $wp_inbox_dept_id; 

		

		$wp_inbox_option_exists = wp_inbox_option_exists($wp_inbox_dept_id);

		

		$ret[] = $wp_inbox_option_exists;

		

		if($wp_inbox_dept_id!='' && $wp_inbox_option_exists){

			

			global $wpdb;

			

			$wpdb->query("DELETE FROM $wpdb->options WHERE option_id=$wp_inbox_dept_id LIMIT 1");			

			

			$old_option_key = 'wp_inbox_department_'.$wp_inbox_dept_id.'_staff';

			

			delete_option($old_option_key);

		}

		

		echo json_encode($ret);

		exit;

	}

	

	add_action( 'wp_ajax_wpinboxdeptdelete', 'wp_inbox_dept_remove' );

	

	function wp_inbox_dept_by_staff($user_id=0) {

		

		$ret = array();

		

		$user_id = ($user_id?$user_id:get_current_user_id());

		

		$dept = get_inbox_departments();

		

		if(!empty($dept)){

			foreach($dept as $data){

				list($id, $title, $desc) = $data;

				$option_key = 'wp_inbox_department_'.$id.'_staff';

				$staff = get_option($option_key, array());

				//pree($staff);

				if(array_key_exists($user_id, $staff)){

					$ret[] = $id;

				}

			}

		}

		

		return $ret;

		

	}

	

	function wp_inbox_dept_staff() {

		

		$wp_inbox_dept_id = is_numeric(wp_inbox_decode($_POST['wp_inbox_dept_id']))?wp_inbox_decode($_POST['wp_inbox_dept_id']):0;

		

		$wp_inbox_option_exists = wp_inbox_option_exists($wp_inbox_dept_id);

		

		if($wp_inbox_option_exists){

		

			$old_option_key = 'wp_inbox_department_'.$wp_inbox_dept_id.'_staff';

			

			$staff = get_option($old_option_key, array());

			

			$staff = array('staff' => array_keys($staff));

			

			echo json_encode($staff);

		

		}

		

		exit;

	}

	

	add_action( 'wp_ajax_wpinboxdeptstaff', 'wp_inbox_dept_staff' );

		

	

	function wp_inbox_dept_add() {

		global $wpdb;

		// Save logic goes here. Don't forget to include nonce checks!

		$ret = __('Please wait, there is some problem in the system.', 'inbox');

		$wp_inbox_dept_id = is_numeric(wp_inbox_decode($_POST['wp_inbox_dept_id']))?wp_inbox_decode($_POST['wp_inbox_dept_id']):0;

		

		$wp_inbox_option_exists = wp_inbox_option_exists($wp_inbox_dept_id);

		

	

		

		$wp_inbox_dept_title = wp_inbox_sanitize($_POST['wp_inbox_dept_title']);

		$key = wp_inbox_underscore($wp_inbox_dept_title);

		$wp_inbox_dept_desc = wp_inbox_sanitize($_POST['wp_inbox_dept_desc']);

		

	

		$option_value = array($wp_inbox_dept_title, $wp_inbox_dept_desc);

		//pree($wp_inbox_dept_id);

		if($wp_inbox_dept_id!='' && $wp_inbox_option_exists){

			

			

			$query = "SELECT option_name FROM $wpdb->options WHERE option_id=$wp_inbox_dept_id LIMIT 1";

			$row = $wpdb->get_row($query);

			

			$option_key = $row->option_name;

			

		}else{

			$option_key = 'wp_inbox_departments_'.time();

			

		}

		

		update_option($option_key, $option_value);	

		

		$ret = __('Your request has been completed successfully.', 'inbox');

		



		echo $ret;

		exit;

	}	

	

	add_action( 'wp_ajax_wpinboxdeptadd', 'wp_inbox_dept_add' );

		

	function action_woocommerce_order_details_after_customer_details( $order ) { 

		global $wp_inbox_woo_activated;

		

		if(!$wp_inbox_woo_activated)

		return;

		

		global $post;

		

		$items = $order->get_items();

		//$order->id

		//pree($items);

		$vendors = array();

		

		if(!empty($items)){

			foreach( $items as $item_key => $item_values ){

				

				$product = get_post($item_values->get_product_id());

				

				$vendors[$product->post_author][] = $product;

			}

		}		

		

		if(!empty($vendors)){

?>
<?php //pree(wp_inbox_options('contact_seller')); 
if(wp_inbox_options('contact_seller')): ?>
<div class="wp_inbox_message_boxes">


<h3><?php echo (count($vendors)>1?__('Need to contact sellers?', 'inbox'):__('Need to contact seller?', 'inbox')); ?></h3>


<?php			

			foreach($vendors as $vendor=>$products){

				if($vendor!=get_current_user_id()){

					wp_order_message_box($vendor, $products);

				}

			}

?>

</div>
<?php endif; ?>
<?php				

		}

		

	}

	

	add_action( 'woocommerce_order_details_after_order_table', 'action_woocommerce_order_details_after_customer_details', 10, 1 );	

	
	function wp_inbox_account_menu_items( $items ) {
		
		
		$userdata = get_userdata( get_current_user_id() );
		$caps = array_keys($userdata->caps);
		$nitems = array();
		
		//if(in_array('administrator', $caps)){
			$wp_inbox_pages_default = wp_inbox_get_pages();
			$wp_inbox_pages_updated = wp_inbox_get_pages(true);
			
			$page_title = $wp_inbox_pages_default[$wp_inbox_pages_updated['inbox']];
			switch($page_title){
				default:
				break;
				case 'Inbox':
					$page_title = __('Inbox', 'inbox');
				break;
			}


			$nitems['inbox'] = $page_title; 
			
		
		
		//}
		
	 	$nitems = array_merge($nitems, $items);
		
		return $nitems;
	 
	}
	
	add_filter( 'woocommerce_account_menu_items', 'wp_inbox_account_menu_items', 10, 1 );		

	function wp_copy_author_file(){
		$copy_to_dir = get_template_directory();
		$copy_from_dir = plugin_dir_path(dirname(__FILE__)).'templates/';
		$response_array = array();
		$is_file_exist = 'no';
		$file_copy = 'no';
		
		$response_array['is_error'] = 'no';
		$response_array['error'] = '';

		if(file_exists($copy_to_dir.'/author.php')){
			$is_file_exist = 'yes';
		}else{

			try{

				$copy =	copy($copy_from_dir.'author.php', $copy_to_dir.'/author.php');
				if($copy){
					$file_copy = 'yes';
				}
			
			} catch(Exception $e){
			
				$response_array['is_error'] = 'yes';
				$response_array['error'] = $e->getMessage();

			}

			

		}

		$response_array['file_exist'] = $is_file_exist;

		if($is_file_exist == 'yes' && isset($_POST['replace_file'])){

			
			try{

				$copy =	copy($copy_from_dir.'author.php', $copy_to_dir.'/author.php');
				if($copy){
					$file_copy = 'yes';
				}
			
			} catch(Exception $e){
			
				$response_array['is_error'] = 'yes';
				$response_array['error'] = $e->getMessage();

			}

		}
			// echo $copy_from_dir;
		$response_array['file_copy'] = $file_copy;

		header('Content-Type: application/json');
		echo json_encode($response_array);
		exit;
	}

	add_action( 'wp_ajax_wp_copy_author_file', 'wp_copy_author_file');







    if(!function_exists('wp_inbox_enqueue_chat_ids')){
        function wp_inbox_enqueue_chat_ids(){

            global $wpdb;

            $receiver_h = isset($_GET['receiver_h']);

            $receiver_i = isset($_GET['receiver_i']);


            $h = isset($_GET['h']);

            $i = isset($_GET['i']);

            $get_array = array($receiver_h, $receiver_i, $h, $i);


            if(!in_array(true, $get_array)) return;


            $receiver_h  = $receiver_h ? wp_inbox_sanitize($_GET['receiver_h']) : '';
            $receiver_i  = $receiver_i ? wp_inbox_sanitize($_GET['receiver_i']) : '';


            $current_user_id = get_current_user_id();


            if($receiver_h || $receiver_i){

                if($receiver_h){

                    $query_option = "SELECT * FROM $wpdb->options WHERE option_id = $receiver_h";
                    $row = $wpdb->get_row($query_option);
                    if(is_null($row)) return false;
                    $receiver_id = wp_inbox_sanitize($_GET['receiver_h']);

                }else{


                    $user = get_user_by('id', $receiver_i);

                    if(!$user) return;

                    $receiver_id = wp_inbox_sanitize($_GET['receiver_i']);
                }

                $sender_id = $current_user_id;

                return array('sender_id' => $sender_id, 'receiver_id' => $receiver_id);

            }



            if($h || $i){

                $meta_id = $h ? wp_inbox_sanitize($_GET['h']) : wp_inbox_sanitize($_GET['i']);
                $query_meta = "SELECT * FROM $wpdb->usermeta WHERE umeta_id = $meta_id";

                $meta_row = $wpdb->get_row($query_meta);

                if(is_null($meta_row)) {return;}


                $user_id = $meta_row->user_id;
                $meta_key = $meta_row->meta_key;


                if($h){

                    $receiver_id = str_replace('HELP_BOX_', '', $meta_key);

                }else{

                    $receiver_id = str_replace('INBOX_', '', $meta_key);
                }


                $sender_id = $current_user_id == $user_id ? $current_user_id : $receiver_id;
                $receiver_id = $current_user_id == $user_id ? $receiver_id : $user_id;

                return array('sender_id' => $sender_id, 'receiver_id' => $receiver_id);

            }

            //result from here



        }
    }



    add_action('wp_ajax_wp_inbox_ajax_save_settings', 'wp_inbox_ajax_save_settings');

    if(!function_exists('wp_inbox_ajax_save_settings')){

        function wp_inbox_ajax_save_settings(){

            if(isset($_POST['wp_inbox_live_chat_settings'])){


                if (

                    ! isset( $_POST['wp_inbox_nonce_field'] )

                    || ! wp_verify_nonce( $_POST['wp_inbox_nonce_field'], 'wp_inbox_nonce_action' )

                ) {



                    _e( 'Sorry, your nonce did not verify.', 'inbox');

                    exit;



                } else {



                        $wp_inbox_live_chat_settings = wp_inbox_sanitize($_POST['wp_inbox_live_chat_settings']);
                        $result = update_option('wp_inbox_live_chat_settings', $wp_inbox_live_chat_settings);

                        echo $result ? 'saved' : '';



                }

            }

            wp_die();

        }
    }

    if(!function_exists('wp_inbox_get_admin_msg_body')){

        function wp_inbox_get_admin_msg_body($echo_body = true){

            global $wp_inbox_url, $wp_inbox_pro;
            $wp_inbox_admin_msg = get_option("wp_inbox_admin_msg", array());
            $wp_inbox_admin_msg = array_reverse($wp_inbox_admin_msg);


            ob_start();

            ?>

            <tbody id="wp_inbox_msg_history_body">

            <?php

            if(!empty($wp_inbox_admin_msg)){

                foreach($wp_inbox_admin_msg as $msg_id => $msg){

                    $msg_status = $msg['status'];
                    $deliver_time_sec = $msg['deliver_date'] ? $msg['deliver_date'] : 0;
                    $deliver_date = (isset($msg['deliver_date']) && $msg['deliver_date'] != '') ? date('F j, Y', $msg['deliver_date']) :'';
                    $msg['deliver_date'] = $deliver_date;
                    $message_encode = base64_encode(json_encode($msg));

                    $status_str = $msg_status;
                    $current_time = time();
                    if($msg_status == 'sent' && $deliver_time_sec > $current_time){

                        $status_str = __('scheduled', 'ibox');
                    }

                    $status_str = ucfirst($status_str);




                    ?>
                        <tr data-id="<?php echo $msg_id;  ?>" data-message="<?php echo $message_encode;  ?>">
                            <th scope="row"><?php echo date('F j, Y h:i a', $msg['time']); ?></th>
                            <td class="wp_inbox_msg"><?php echo $msg['message']; ?></td>
                            <td><?php echo $status_str; ?></td>
                            <td><?php echo $deliver_date ? $deliver_date : __('Now', 'inbox'); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm wp_msg_action mb-md-0 mb-1" data-action="load"><?php  _e('Load', 'inbox') ?></button>
                                <?php if($msg_status == 'draft'){ ?>
                                    <button class="btn btn-success btn-sm wp_msg_action mb-md-0 mb-1" <?php echo $wp_inbox_pro?'':'disabled="disabled"'; ?> data-action="send"><?php  _e('Send', 'inbox') ?></button>
                                    <button class="btn btn-danger btn-sm wp_msg_action mb-md-0 mb-1" data-action="delete"><?php  _e('Delete', 'inbox') ?></button>
                                <?php } ?>
                                <span class="msg_loading d-inline-block mb-md-0 mb-1"><img src="<?php echo $wp_inbox_url?>img/loading.gif" alt=""></span>
                            </td>
                        </tr>
                    <?php


                }
            }else{

                ?>
                    <tr>
                        <td colspan="5">

                            <div class="alert alert-info text-center">
                                <?php _e('No message found.', 'inbox') ?>
                            </div>
                        </td>

                    </tr>
                <?php

            }

            ?>

            </tbody>


            <?php

            $body_content = ob_get_clean();

            if($echo_body){

                echo $body_content;

            }else{

                return $body_content;

            }


        }
    }


    if(!function_exists('wp_inbox_get_admin_msg_id')){
        function wp_inbox_get_admin_msg_id(){

            $wp_inbox_admin_msg_id = get_option('wp_inbox_admin_msg_id');

            $msg_id = 1;

            if($wp_inbox_admin_msg_id && is_numeric($wp_inbox_admin_msg_id)){

                $wp_inbox_admin_msg_id++;

                $msg_id = $wp_inbox_admin_msg_id;

            }else{

                $wp_inbox_admin_msg = get_option("wp_inbox_admin_msg");

                if(is_array($wp_inbox_admin_msg) && !empty($wp_inbox_admin_msg)){
                    $msgs_ids = array_keys($wp_inbox_admin_msg);
                    $msg_id = max($msgs_ids);

                }
                

            }

            return $msg_id;

        }
    }

    add_action('wp_ajax_wp_inbox_admin_msg_save', 'wp_inbox_admin_msg_save');

    if(!function_exists('wp_inbox_admin_msg_save')){


        function wp_inbox_admin_msg_save(){


            $result = array(
                    'status' => false,
                    'msg_body' => '',
            );

            if(!empty($_POST) && isset($_POST['wp_inbox_msg_obj'])){


                if(!isset($_POST['wp_inbox_nonce']) || !wp_verify_nonce($_POST['wp_inbox_nonce'], 'wp_inbox_nonce_action')){

                    wp_die(__('Sorry, your nonce did not verify.', 'inbox'));
                }else{

                    //your code here



                    $wp_inbox_admin_msg = get_option('wp_inbox_admin_msg', array());

                    $wp_inbox_msg_obj = wp_inbox_sanitize($_POST['wp_inbox_msg_obj']);
                    $msg_id_post = isset($_POST['msg_id']) ? wp_inbox_sanitize($_POST['msg_id']) : false;
                    $wp_in_msg_status = $wp_inbox_msg_obj['status'];
                    $wp_inbox_msg_obj['user'] = get_current_user_id();
                    $wp_inbox_msg_obj['time'] = time();
                    $new_msg_id = wp_inbox_get_admin_msg_id();
                    $msg_id = $msg_id_post ? $msg_id_post : 'msg_'.$new_msg_id;
                    $wp_inbox_msg_obj['deliver_date'] = $wp_inbox_msg_obj['deliver_date'] ? strtotime($wp_inbox_msg_obj['deliver_date']) : '';
                    if($wp_in_msg_status == 'sent'){

                        $result['sent'] = true;
                        $wp_inbox_msg_obj['deliver_date'] = $wp_inbox_msg_obj['send_type'] == 'now' ? time() : $wp_inbox_msg_obj['deliver_date'];

                    }



                    $wp_inbox_admin_msg[$msg_id] = $wp_inbox_msg_obj;
                    $update_status = update_option('wp_inbox_admin_msg', $wp_inbox_admin_msg);
                    if($update_status){
                        update_option('wp_inbox_admin_msg_id', $new_msg_id);
                    }

                    $result['status'] = $update_status;
                    $result['msg_body'] = wp_inbox_get_admin_msg_body(false);

                }

            }

            wp_send_json($result);

        }
    }

    add_action('wp_ajax_wp_inbox_admin_msg_action', 'wp_inbox_admin_msg_action');

    if(!function_exists('wp_inbox_admin_msg_action')){


        function wp_inbox_admin_msg_action(){


            $result = array(
                'status' => false,
                'msg_body' => '',
            );

            if(isset($_POST['msg_id']) && isset($_POST['wp_inbox_msg_status']) && isset($_POST['wp_inbox_action'])){


                if(!isset($_POST['wp_inbox_nonce']) || !wp_verify_nonce($_POST['wp_inbox_nonce'], 'wp_inbox_nonce_action')){

                    wp_die(__('Sorry, your nonce did not verify.', 'inbox'));
                }else{

                    //your code here



                    $wp_inbox_admin_msg = get_option('wp_inbox_admin_msg', array());


                    $msg_id = wp_inbox_sanitize($_POST['msg_id']);
                    $msg_status = wp_inbox_sanitize($_POST['wp_inbox_msg_status']);
                    $msg_action = wp_inbox_sanitize($_POST['wp_inbox_action']);

                    if($msg_action == 'delete' && $msg_status == 'draft' && array_key_exists($msg_id, $wp_inbox_admin_msg)){

                        unset($wp_inbox_admin_msg[$msg_id]);

                    }else if($msg_action == 'send'){

                        $current_msg = $wp_inbox_admin_msg[$msg_id];
                        $current_msg['status'] = 'sent';
                        $current_msg['deliver_date'] = $current_msg['send_type'] == 'now' ? time() : $current_msg['deliver_date'];
                        $wp_inbox_admin_msg[$msg_id] = $current_msg;
                        $result['sent'] = true;

                    }

                    $update_status = update_option('wp_inbox_admin_msg', $wp_inbox_admin_msg);

                    $result['status'] = $update_status;
                    $result['msg_body'] = wp_inbox_get_admin_msg_body(false);

                }

            }

            wp_send_json($result);

        }
    }

    add_action('init', function(){

        if(isset($_GET['j'])){
			
			
			if(function_exists('wp_inbox_get_admin_messages')){
            	wp_inbox_get_admin_messages();
			}
            exit;
        }

    });

 



	include_once('profiles.php');