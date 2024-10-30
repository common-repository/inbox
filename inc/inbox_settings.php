<?php defined( 'ABSPATH' ) or die( __('No script kiddies please!', 'inbox') );

	if ( !current_user_can( 'install_plugins' ) ) {

		wp_die( __( 'You do not have sufficient permissions to access this page.', 'inbox' ) );

	}



	global $wpdb, $wp_inbox_data, $wp_inbox_pro, $wp_inbox_pages, $wp_inbox_required_plugins, $wp_inbox_all_plugins, $wp_inbox_plugins_activated, $wp_inbox_url, $wp_inbox_premium_link;

	$inbox_options = get_option('wpinbox_options', array());


	//pree($inbox_options);

?>





<div class="wrap wpinbox_settings_div">



        







        <div class="icon32" id="icon-options-general"><br></div><h2><?php echo $wp_inbox_data['Name']; ?> <?php echo '('.$wp_inbox_data['Version'].($wp_inbox_pro?') Pro':')'); ?> - <?php _e("Settings", 'inbox'); ?></h2> 

    

         

           

        <div class="nav-tab-wrapper">
        
        	<a class="nav-tab nav-tab-active"><?php _e("General Settings", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Pages & Shortcodes", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Communication", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Support Departments", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Admin Messages", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Google+ API", 'inbox'); ?></a>
            
            <a class="nav-tab"><?php _e("Install Plugins", 'inbox'); ?></a>
            

            
            <a class="nav-tab"><?php _e("Advance Settings", 'inbox'); ?></a>

            <a class="nav-tab"><?php _e("Live Chat", 'inbox'); ?></a>


        </div>      






<form class="nav-tab-content" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<?php wp_nonce_field( 'wp_inbox_action', 'wp_inbox_field' ); ?>

<div class="wpinbox_folders">
<?php if(!$wp_inbox_pro): ?>
<?php 
		$banners  = array('banner-2.png'); 
		$banner_index = array_rand($banners);
?>

<a href="<?php echo $wp_inbox_premium_link; ?>" target="_blank"><img src="<?php echo $wp_inbox_url; ?>/img/<?php echo $banners[$banner_index]; ?>" style="max-width:90%; margin:20px auto 0 auto" /></a>

<?php endif; ?>
</div>

<div class="wpinbox_log">
<div class="row nopadding wpinbox-options">
<?php if(!$wp_inbox_pro): ?>
<a class="btn btn-info btn-sm mx-auto" href="<?php echo esc_url($wp_inbox_premium_link); ?>" target="_blank" title="<?php echo __('Click here for Premium Version', 'inbox'); ?>"><?php echo __('Go Premium', 'inbox'); ?></a>
<?php endif; ?>


<div class="alert alert-secondary fade in alert-dismissible d-none mx-auto mt-4" style="width: 90%">
 <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('Close', 'inbox'); ?>">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button>    <strong><?php echo __('Success!', 'inbox'); ?></strong> <?php echo __('Options are updated successfully.', 'inbox'); ?>
</div>

<input type="checkbox" checked="checked" name="wpinbox_options[placeholder_index]" value="placeholder_index" id="inbox_contact_seller" class="hides"  />

<ul class="col col-md-12 mt-4">

    <li>
        <label>
            <input <?php checked(wp_inbox_options('contact_seller')); ?> type="checkbox" name="wpinbox_options[contact_seller]" value="contact_seller" />
            <?php echo __('Contact Seller Link?', 'inbox'); ?> <small><?php echo __('(Front-end)', 'inbox'); ?></small>
        </label>

    </li>
    
</ul>



<a class="btn btn-info btn-sm mx-auto " href="http://demo.androidbubble.com/help" target="_blank" title="<?php echo __('Click here for demo', 'inbox'); ?>"><?php echo __('Click here for demo', 'inbox'); ?></a>


<ul class="col col-md-12 mt-4">
	<li class="promotions"></li>
    <li style="text-align:center;">
    <a href="https://wordpress.org/plugins/gulri-slider" target="_blank" title="<?php echo __('Image Slider', 'inbox'); ?>"><img src="<?php echo $wp_inbox_url; ?>img/gslider.gif" /></a>
    </li>
</ul>
</div>
</div>

</form>






<form class="nav-tab-content hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<?php wp_nonce_field( 'wp_inbox_action', 'wp_inbox_field' ); ?>











<br />

<div class="wp_inbox_notes">

<?php echo __('Following pages are automatically created and ready to use.', 'inbox').' '.__('You can insert them in relevant menus.', 'inbox'); ?>

</div>





<table border="0">

<tbody>

<thead>

<th><?php _e('ID', 'inbox'); ?></th>

<th><?php _e('Title', 'inbox'); ?></th>

<th><?php _e('Shortcode', 'inbox'); ?></th>

<th><?php _e('Actions', 'inbox'); ?></th>

</thead>

<?php if(!empty($wp_inbox_pages)): foreach($wp_inbox_pages as $page=>$id): $page = get_page($id); ?>

<tr>

<td><?php echo $page->ID; ?></td>

<td><?php echo $page->post_title; ?></td>

<td><?php echo $page->post_content; ?></td>

<td><a href="<?php echo get_edit_post_link($page->ID); ?>" class=" btn btn-info btn-sm" target="_blank"><?php _e('Edit', 'inbox'); ?></a>  <a href="<?php echo get_permalink($page->ID); ?>" class=" btn btn-info btn-sm" target="_blank"><?php _e('View', 'inbox'); ?></a></td>

</tr>

<?php endforeach; endif; ?>





</tbody>

</table>	


<?php if(!$wp_inbox_pro): ?>
<?php 
		$banners  = array('banner-1.png', 'banner-0.gif'); 
		$banner_index = array_rand($banners);
		

?>

<a href="<?php echo $wp_inbox_premium_link; ?>" target="_blank"><img src="<?php echo $wp_inbox_url; ?>/img/<?php echo $banners[$banner_index]; ?>" style="max-width:70%; margin:60px auto 0 auto" /></a>

<?php endif; ?>

</form>



<form class="nav-tab-content hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<?php if(isset($_POST['wp_inbox_messages'])){ $msgs = wp_inbox_sanitize($_POST['wp_inbox_messages']); update_option('wp_inbox_messages', $msgs); } ?>

<br />



<textarea name="wp_inbox_messages" placeholder="<?php _e('Write here the words to disallow in communication threads', 'inbox'); ?>. <?php _e('Examples', 'inbox'); ?>: skype,phone,email,paypal,payoneer,2checkout"><?php echo get_option('wp_inbox_messages'); ?></textarea><br />

<small><?php _e('Enter the words to disallow in communication threads', 'inbox'); ?></small>

<br />





<input type="submit" class="btn btn-primary" value="<?php _e('Update', 'inbox'); ?>" />

<br />







<?php

	$res = $wpdb->get_results("SELECT um.*, u.display_name FROM $wpdb->usermeta um LEFT JOIN $wpdb->users u ON u.ID=um.user_id WHERE meta_key LIKE 'INBOX_%' ORDER BY umeta_id DESC LIMIT 100");

	if(!empty($res)){

		

		//pree($res);

?>
<div class="wp_inbox_notes">

<?php _e('Communication threads from all users.', 'inbox'); ?>

</div>

<table border="0">

<thead>

<th><?php _e('Participants', 'inbox'); ?></th>

<th><?php _e('Last Message', 'inbox'); ?></th>

<th><?php _e('Date', 'inbox'); ?></th>

<th><?php _e('Actions', 'inbox'); ?></th>

</thead>

<?php		

		foreach($res as $r){

			

			//$receiver = get_user_by('id', $r->user_id);

			$receiver_name = $r->display_name;

			

			

			$meta_value = maybe_unserialize($r->meta_value);

			$last = end($meta_value);

			

			//pree($last);

			

			list($str, $sender_id, $time) = $last;

			

			$sender_id = str_replace('INBOX_', '', $r->meta_key);

			$sender = get_user_by('id', $sender_id);

			$sender_name = is_object($sender) ? $sender->display_name : '';

			

			//pree($r->user_id.' - '.$sender_id);

			//pree($sender);

			//pree($receiver->display_name.' - '.$sender->display_name);

			//pree($receiver_name.' - '.$sender_name.' - '.$r->display_name);

			

?>

<tr>

<td><a href="<?php echo get_author_posts_url($r->user_id); ?>" target="_blank"><?php echo ucwords($receiver_name); ?></a> & <a href="<?php echo get_author_posts_url($sender_id); ?>" target="_blank"><?php echo ucwords($sender_name); ?></a></td>

<td><?php echo htmlentities($str); ?></td>

<td><?php echo wp_inbox_date($time); ?></td>

<td><a href="options-general.php?page=wp-inbox-threads&id=<?php echo $r->umeta_id; ?>" target="_blank" class=" btn btn-info btn-sm"><?php _e('View', 'inbox'); ?></a></td>

</tr>		

<?php		

		}

?>

</table>

<?php		

	}

?>



<br />

<br />

<br />





<?php

	$res = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key LIKE 'HELP_BOX_%' GROUP BY user_id ORDER BY umeta_id DESC LIMIT 100");

	if(!empty($res)){

		

		//wp_inbox_pree($res);

?>
<div class="wp_inbox_notes">

<?php _e('Communication threads from all departments.', 'inbox'); ?>

</div>
<table border="0">

<thead>

<th><?php _e('Participants', 'inbox'); ?></th>

<th><?php _e('Last Message', 'inbox'); ?></th>

<th><?php _e('Date', 'inbox'); ?></th>

<th><?php _e('Actions', 'inbox'); ?></th>

</thead>

<?php		

		foreach($res as $r){

			

			$receiver = get_user_by('id', $r->user_id);

			

			$meta_value = maybe_unserialize($r->meta_value);

			$last = end($meta_value);

			list($str, $sender_id, $time) = $last;

			

			if(is_array($str)){

				$str = array_filter($str, 'strlen');

				$str = implode(', ', $str);

			}

			

			$sender_id = str_replace('HELP_BOX_', '', $r->meta_key);

			$sender_id = wp_inbox_option_by_id($sender_id);

			

			$sender = get_option($sender_id);

			

			

			

			//wp_inbox_pree($sender);

			if(is_array($sender)){

				$display_name = current($sender);

				$sender_url = 'options-general.php?page=wp_inbox&t=2';

				

			}else{

				$display_name = $sender->display_name;

				$sender_url = get_author_posts_url($sender_id);

			}

			

?>

<tr>

<td><a href="<?php echo get_author_posts_url($r->user_id); ?>" target="_blank"><?php echo ucwords($receiver->display_name); ?></a> & <a href="<?php echo $sender_url; ?>" target="_blank"><?php echo ucwords($display_name); ?></a></td>

<td><?php echo htmlentities($str); ?></td>

<td><?php echo wp_inbox_date($time); ?></td>

<td><a href="options-general.php?page=wp-inbox-threads&h=<?php echo $r->umeta_id; ?>" target="_blank" class=" btn btn-info btn-sm"><?php _e('View', 'inbox'); ?></a></td>

</tr>		

<?php		

		}

?>

</table>

<?php		

	}

?>



</form>



<form class="nav-tab-content hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<br />

<div class="wp_inbox_notes">

<?php _e('You may define different departments and assign your staff accordingly to manage support tickets', 'inbox'); ?> <a href="https://www.youtube.com/embed/VxPdVqViD9Y" target="_blank" style="float:right;"><?php _e('Video Tutorial', 'inbox'); ?></a>

</div>



<div class="container-fluid">

  <div class="row content">

    <div class="col-sm-3 ">

    

    <div class="wp_inbox_add_dept">

        

        <div class="form-group">

            <label for="title"><?php _e('Title', 'inbox'); ?>:</label>

            <input type="text" class="form-control" id="wp_inbox_dept_title">

        </div>

        <div class="form-group">

            <label for="description"><?php _e('Description', 'inbox'); ?>:</label>

            <textarea class="form-control" id="wp_inbox_dept_desc"></textarea>

        </div>  

		<button id="wp_inbox_add_dept" type="button" class="btn btn-primary"><?php _e('Add', 'inbox'); ?></button> <span class="add_edit"><?php _e('or', 'inbox'); ?> <a class="wp_inbox_add_dept btn btn-link"><?php _e('Add', 'inbox'); ?></a></span>

		<input type="hidden" id="wp_inbox_dept_id" value="<?php echo wp_inbox_encode(0); ?>"  />

   </div> 

   <button type="button" class="wp_inbox_add_dept btn btn-default"><?php _e('Add Department', 'inbox'); ?></button>

<?php

	

	$args = array(

		'blog_id'      => $GLOBALS['blog_id'],

		'role'         => 'administrator',

		'role__in'     => array(),

		'role__not_in' => array(),

		'meta_key'     => '',

		'meta_value'   => '',

		'meta_compare' => '',

		'meta_query'   => array(),

		'date_query'   => array(),        

		'include'      => array(),

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

	

	$users = get_users( $args );

	

	//pree($users);



?>        

        <div class="form-group staff-members">

        <h3><?php _e('Staff Members', 'inbox'); ?>:</h3>

        <?php

		if(!empty($users)):

		?>

        <form action="" method="post">

        <?php wp_nonce_field( 'wp_inbox_dept_action', 'wp_inbox_dept_field' ); ?>

        <input type="hidden" name="wp_inbox_dept_staff" value="" />

            <ul>

        <?php

		foreach($users as $user):

		?>            

                <li class="checkbox">

               

					<input id="u-<?php echo $user->ID; ?>" type="checkbox" name="wp_inbox_dept[<?php echo $user->ID; ?>]" value="1" /> <label for="u-<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></label>

               

                </li>

        <?php

		endforeach;

		?>                

            </ul>

		<input type="submit" value="<?php _e('Save Changes', 'inbox'); ?>" class="btn btn-primary" />

		</form>            

        <?php

		endif;

		?>

        </div>

	</div>    

    

    <div class="col-sm-9 ">

 <?php

	$res = get_inbox_departments();//get_option("wp_inbox_departments", array());

	

	

	if(!empty($res)){

?>

<table border="0" class="wp_inbox_dept_items">

<thead>

<th><?php _e('Title', 'inbox'); ?></th>

<th><?php _e('Description', 'inbox'); ?></th>

<th><?php _e('Staff', 'inbox'); ?></th>

<th style="width:200px"><?php _e('Actions', 'inbox'); ?></th>

</thead>

<?php		

		ksort($res);

		

		foreach($res as $k=>$r){

			list($id, $title, $desc) = $r;

			$staff = get_option('wp_inbox_department_'.$id.'_staff', array());

			

?>

<tr>

<td><?php echo ucwords($title); ?></td>

<td><?php echo $desc; ?></td>

<td><?php echo count($staff); ?></td>

<td><a class="wp_inbox_department_staff btn btn-info btn-sm" data-key="<?php echo wp_inbox_encode($id); ?>"><?php _e('Staff', 'inbox'); ?></a>  <a class="wp_inbox_department_edit btn btn-info btn-sm" data-key="<?php echo wp_inbox_encode($id); ?>"><?php _e('Edit', 'inbox'); ?></a>  <a class="wp_inbox_department_delete btn btn-danger btn-sm" data-key="<?php echo wp_inbox_encode($id); ?>"><?php _e('Delete', 'inbox'); ?></a></td>

</tr>		

<?php		

		}

?>

</table>

<?php		

	}

?>   

    </div>

</div>    

</div>    





</form>

<!-- Admin Messages Content-->
<div class="nav-tab-content hides wpin_admin_msg_content">
	
    <?php if(!$wp_inbox_pro): ?>
    <div class="alert alert-danger fade in alert-dismissible show mt-4">
     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" style="font-size:20px">×</span>
      </button>    <strong><?php _e('Alert!', 'inbox'); ?></strong> <?php _e('This is a premium feature. You may add/update messages but cannot send in FREE version.', 'inbox'); ?>
    </div>
    <?php endif; ?>

    <div class="row mt-5">
        <div class="col-md-12">

            <div class="form-group">
                <label for="wp_inbox_admin_msg" class="h6"><?php _e('New Message', 'inbox') ?>:</label>
                <textarea class="form-control" id="wp_inbox_admin_msg" rows="3"></textarea>
            </div>



            <div class="form-check">
                <input class="form-check-input wp_inbox_send_type" type="radio" name="send_type" id="send_type_default" value="now" checked>
                <label class="form-check-label" for="send_type_default">
                    <?php _e('Now', 'inbox') ?>
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input wp_inbox_send_type" type="radio" name="send_type" id="send_type_date" value="date">
                <label class="form-check-label" for="send_type_date">
                    <?php _e('Date', 'inbox') ?>
                </label>

                <input class="form-control w-25 wp_inbox_msg_date mt-2 wpin_hide" type="text" placeholder="<?php echo date('F d, Y', strtotime('+2 '.__('weeks', 'inbox').'')) ?>" readonly>
            </div>


            <div class="form-group">
                <button class="btn btn-primary wpin_msg_save wpin_update" style="display: none;" data-status="draft" data-id=""><?php _e('Update', 'inbox') ?></button>
                <button class="btn btn-primary wpin_msg_save" data-status="draft"><?php _e('Save', 'inbox') ?></button>
                <button class="btn btn-danger wpin_msg_save" <?php echo $wp_inbox_pro?'':'disabled="disabled"'; ?> data-status="sent"><?php _e('Save & Send', 'inbox') ?> </button>
                <span class="msg_main_loading"><img src="<?php echo $wp_inbox_url?>img/loading.gif" alt=""></span>
            </div>

        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="h5"><?php _e('Messages History', 'inbox') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <table class="table mt-2">
                <thead>
                <tr>
                    <th scope="col"><?php _e('Date', 'inbox'); ?></th>
                    <th scope="col"><?php _e('Message', 'inbox') ?></th>
                    <th scope="col"><?php _e('Status', 'inbox') ?></th>
                    <th scope="col"><?php _e('Deliver Date', 'inbox') ?></th>
                    <th scope="col" style="width: 20%;"><?php _e('Actions', 'inbox') ?></th>
                </tr>
                </thead>
                <?php if(function_exists('wp_inbox_get_admin_msg_body')){wp_inbox_get_admin_msg_body();} ?>
            </table>

        </div>
    </div>

</div>




<form class="nav-tab-content hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<?php wp_nonce_field( 'wp_inbox_gplus_action', 'wp_inbox_gplus_field' ); ?>











<br />

<div class="wp_inbox_notes">

<?php _e('Please enter your API key to make Google Maps work on relevant pages.', 'inbox'); ?><br />
<br />

<small>
<?php _e('Web Services', 'inbox'); ?> > <?php _e('Directions API', 'inbox'); ?> > <a href="https://developers.google.com/maps/documentation/directions/get-api-key" target="_blank"><?php _e('Get API Key', 'inbox'); ?></a></small>
</div><br />


<input type="text" name="wp_inbox_gplus_api" value="<?php echo get_option('wp_inbox_gplus_api', 'YOUR_API_KEY'); ?>" style="width:50%"  /><br />
<br />


<input type="submit" value="<?php _e('Save Changes', 'inbox'); ?>" class="btn btn-primary" />

</form>




<form class="nav-tab-content hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">


<?php

if(!empty($wp_inbox_required_plugins)){

?>

<h3><?php _e('Following plugins are (maybe) required to be installed and activated.', 'inbox'); ?></h3>

<ul>	

<?php

	foreach($wp_inbox_required_plugins as $plugin=>$path){

		

		$install_link = 'plugin-install.php?tab=search&type=term&s=';

		$activate_link = 'plugins.php?plugin_status=inactive&s=';

		

		if(array_key_exists($path, $wp_inbox_all_plugins)){

			

			

			

			if(in_array($path, $wp_inbox_plugins_activated)){

				$title = '';

				$link = '';	

			}else{

				$title = __('Click here to activate', 'inbox');

				$link = $activate_link.$plugin;

			}

			

			

		}else{

			$title = __('Click here to install', 'inbox');

			$link = $install_link.$plugin;

			

		}

		

?>

<li><?php echo $plugin; ?> <?php if($title!=''){ ?><a href="<?php echo $link; ?>" target="_blank" title="<?php $title; ?>"><?php echo $title; ?></a><?php }else{ echo ' / '.__('Installed', 'inbox').' & '.__('Activated', 'inbox'); } ?></li>	

<?php		

	}

?>	

</ul>

<?php    

}

?>

</form>














<form class="nav-tab-content advance_settings hides" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<h3><?php _e('Premium', 'inbox'); ?> <?php _e('Shortcodes', 'inbox'); ?></h3>
<ul>

<li>
	<a>[WP-USERS-STRIP]</a>
    <div>
    <?php _e('This shortcode will provide a search bar filter to find user profiles.', 'inbox').' '._e('It can be used on any page, initially it is developed for inbox.', 'inbox'); ?><br />
    <img src="https://androidbubbles.files.wordpress.com/2019/03/strip.png" />
    </div>
</li>

<li>
	<a>[WP-USERS-LIST]</a>
    <div>
    <?php _e('This shortcode will provide a list of user profiles.').' '._e('It can be used on any page.', 'inbox'); ?><br />
    <img src="https://androidbubbles.files.wordpress.com/2019/03/strip.png" />
    </div>
</li>

</ul>

<a class="btn btn-primary wp_copy_author" ><?php _e('Copy', 'inbox'); ?> <?php _e('Author', 'inbox'); ?>.php</a>
</form>

<div class="nav-tab-content hides live_chat_settings_container">
    <h3><?php _e('Live Chat Settings', 'inbox'); ?></h3>

    <?php

        global  $is_chat_ajax_based;

    ?>

    <div class="alert alert-success success_msg" style="display: none;">
        <?php _e('Settings saved successfully.', 'inbox'); ?>
    </div>

    <label for="wp_inbox_ajax_based_chat"><?php _e('Ajax based chat', 'inbox'); ?></label>
    <input type="checkbox" <?php echo $is_chat_ajax_based ? 'checked' : ''; ?>  name="wp_inbox_ajax_based_chat" id="wp_inbox_ajax_based_chat" class="live_chat_settings" title="<?php _e('If checkbox is checked messages will send through ajax and on each message page will refresh.', 'inbox'); ?>">


</div>



<!-- Modal -->
<div class="wp-inbox-modal modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php _e('Already Exist', 'inbox') ;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo __('Author.php file already exist.', 'inbox').' '.__('Are you want to replace it?', 'inbox') ;?> 
      </div>
      <div class="modal-footer">
		  <button type="button " class="btn btn-primary author_replace_confirm"><?php _e('Yes', 'inbox') ;?></button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('No', 'inbox') ;?></button>
      </div>
    </div>
  </div>
</div>


</div>



<script type="text/javascript" language="javascript">

jQuery(document).ready(function($) {

	

	

	

});	

</script>



<style type="text/css">

<?php echo implode('', $css_arr); ?>

	#wpfooter{

		display:none;

	}

<?php if(!$wp_inbox_pro): ?>



	#adminmenu li.current a.current {

		font-size: 12px !important;

		font-weight: bold !important;

		padding: 6px 0px 6px 12px !important;

	}

	#adminmenu li.current a.current,

	#adminmenu li.current a.current span:hover{

		color:#9B5C8F;

	}

	#adminmenu li.current a.current:hover,

	#adminmenu li.current a.current span{

		color:#fff;

	}	

<?php endif; ?>

	.woocommerce-message,

	.update-nag{

		display:none;

	}

</style>

