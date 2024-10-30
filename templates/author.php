<?php






//pree(get_option('pages_layout'));
if(get_option('pages_layout')!='' && get_option('pages_layout') != 'default') {

	

	// Get default template from theme options.

	echo get_template_part('page', get_option('pages_layout'));

	return;



} else {



global $poi_acf;

if ( $author_id = get_query_var( 'author' ) ) { $author = get_user_by( 'id', $author_id ); }


$about_me = ($poi_acf?get_field( "about_me", 'user_'.$author_id):'');



wp_update_user( array( 'ID' => $author_id, 'description' => $about_me ) );

$company_name = ($poi_acf?get_field( "company_name", 'user_'.$author_id ):'');

$userdata = get_userdata( $author_id );
//pree($userdata);

$profile_pic = get_avatar_url($author->ID, 400);



$author_bio = get_the_author_meta( 'description', $author->ID );



$user_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta($author->ID) );



//pree($user_meta);

extract($user_meta);



$location = $billing_city.', '.$billing_country;



$address = wp_inbox_get_user_address($author->ID, $user_meta);

$inbox_thread_id = 0;

if(function_exists('wp_inbox_get_thread_id')){
	
	$inbox_thread_id = wp_inbox_get_thread_id($author->ID, get_current_user_id());
	//pree($inbox_thread_id);
	$inbox_thread_id = (!empty($inbox_thread_id)?end($inbox_thread_id):0);
	//pree($inbox_thread_id);
	$inbox_thread_id = (is_object($inbox_thread_id)?$inbox_thread_id->umeta_id:0);
	//pree($inbox_thread_id);
}




$args = array(

	'posts_per_page'   => -1,

	'offset'           => 0,

	'category'         => '',

	'category_name'    => '',

	'orderby'          => 'date',

	'order'            => 'DESC',

	'include'          => '',

	'exclude'          => array($post->ID),

	'post_type'        => 'product',

	'post_mime_type'   => '',

	'post_parent'      => '',

	'author'	   => $author->ID,

	'author_name'	   => '',

	'post_status'      => 'publish',

	'suppress_filters' => true 

);

if(function_exists('wp_user_additional_info')){
$user_info = wp_user_additional_info($author->ID);
extract($user_info);
}		
$seller_products = get_posts( $args );	

	$tags = array();
	if(!empty($seller_products)){
		foreach($seller_products as $pros){
			$tags[] = '<a href="/?product_cat=&post_type=product&s='.urlencode(strtolower($pros->post_title)).'" class="tag">#'.$pros->post_title.'</a> ';
		}
		shuffle($tags);
	}

get_header();

do_action( 'flatsome_before_page' ); ?>

<div id="content" class="content-area page-wrapper" role="main">

	<div class="row row-main">

		<div class="col-outw mx-auto"><!--large-12 col-->

			<div class="col-inner 1">

				

	

<?php echo '<link rel="stylesheet" href="https://bootswatch.com/cosmo/bootstrap.min.css">'; ?>

<style type="text/css">

.mainbody {

    background:#f0f0f0;

}

/* Special class on .container surrounding .navbar, used for positioning it into place. */

.navbar-wrapper {

  /*position: fixed;*/

  top: 0;

  left: 0;

  right: 0;

  z-index: 20;

  margin-left: -15px;

  margin-right: -15px;

}



/* Flip around the padding for proper display in narrow viewports */

.navbar-wrapper .container {

  padding-left: 0;

  padding-right: 0;

}

.navbar-wrapper .navbar {

  padding-left: 15px;

  padding-right: 15px;

}



.navbar-content

{

    width:320px;

    padding: 15px;

    padding-bottom:0px;

}

.navbar-content:before, .navbar-content:after

{

    display: table;

    content: "";

    line-height: 0;

}

.navbar-nav.navbar-right:last-child {

    margin-right: 15px !important;

}

.navbar-footer 

{

    background-color:#DDD;

}

.navbar-footer-content { padding:15px 15px 15px 15px; }

.dropdown-menu {

padding: 0px;

overflow: hidden;

}



.brand_network {

    color: #9D9D9D;

    float: left;

    position: absolute;

    left: 70px;

    top: 30px;

    font-size: smaller;

}



.post-content {

    margin-left:58px;

}



.badge-important {

    margin-top: 3px;

    margin-left: 25px;

    position: absolute;

}



.dropdown.ns-active.open{

	width: 322px;

}



#wide-nav{

	display:none;

}

.navbar.navbar-default.navbar-static-top {

	padding-left: 0;

}

.products_display .col-md-4 {

	

}
.author .wp_inbox_message_box_toggle_div{
	display:none;
}
.author .request_custom{
	cursor:pointer;
}

.author.archive .panel.panel-default .products_display {
	clear: both;
	margin: 18px 0;
	padding: 22px 0;
}

.author.archive .upp > div{
	width:236px;
	height:190px;
	text-align:center;
}

.author.archive .upp > div img {
	max-height: 190px;
	max-width: 236px;
	margin: 0 auto;
	background-color: rgba(0,0,0,0.05);
	padding: 1px;
}
.archive.author .col-outw.mx-auto {
	width: 1136px;
	max-width: none;
}
.bio_wrapper.media-body p{
	word-wrap: break-word;
}
</style>
<script type="text/javascript" language="javascript">
	jQuery(document).ready(function($){
		$('.request_custom').on('click', function(){
			$('.author .wp_inbox_message_box_toggle_div').toggle();
		});
	});
</script>
<div class="mainbody container-fluid">

    <div class="row">

    

    	<?php if(get_current_user_id()==$author->ID): ?>

        <div class="navbar-wrapper">

            <div class="container-fluid">

                <div class="navbar navbar-default navbar-static-top" role="navigation">

                    <div class="container-fluid">

                        <div class="navbar-header">

                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

                                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span><span

                                    class="icon-bar"></span><span class="icon-bar"></span>

                            </button>

                            

                        </div>

                        <div class="navbar-collapse collapse">

                            <ul class="nav navbar-nav">

                                <li><a href="./ORqmj">Stream</a></li>

                                <li><a href="#">My Activity</a></li>

                                <li><span class="badge badge-important">2</span><a href="#"><i class="fa fa-bell-o fa-lg" aria-hidden="true"></i></a></li>

                                <li><a href="#"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></li>

                            </ul>

                            <ul class="nav navbar-nav navbar-right">

                                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">

                                    <span class="user-avatar pull-left" style="margin-right:8px; margin-top:-5px;">

                                        <img src="<?php echo $profile_pic; ?>" class="img-responsive img-circle" title="<?php echo $author->display_name; ?>" alt="<?php echo $author->display_name; ?>" width="30px" height="30px" />

                                    </span>

                                    <span class="user-name">

                                        <?php echo $author->display_name; ?>

                                    </span>

                                    <b class="caret"></b></a>

                                    <ul class="dropdown-menu">

                                        <li>

                                            <div class="navbar-content">

                                                <div class="row">

                                                    <div class="col-md-5">

                                                        <img src="<?php echo $profile_pic; ?>" alt="Alternate Text" class="img-responsive" width="120px" height="120px" />

                                                        <p class="text-center small">

                                                            <a href="<?php echo get_edit_user_link(); ?>"><?php _e('Change Photo'); ?></a></p>

                                                    </div>

                                                    <div class="col-md-7">

                                                        <span><?php echo $author->display_name; ?></span>

                                                        <p class="text-muted small">

                                                        </p>

                                                        <div class="divider">

                                                        </div>

                                                        <a href="<?php echo get_edit_user_link(); ?>" class="btn btn-default btn-sm"><i class="fa fa-user-o" aria-hidden="true"></i> <?php _e('Edit Profile'); ?></a>

                                                        <a href="#" class="btn btn-default btn-sm hide"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?php _e('Contacts'); ?></a>

                                                        <a href="#" class="btn btn-default btn-sm hide"><i class="fa fa-cogs" aria-hidden="true"></i> <?php _e('Settings'); ?></a>

                                                        <a href="#" class="btn btn-default btn-sm hide"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <?php _e('Help!'); ?></a>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="navbar-footer">

                                                <div class="navbar-footer-content">

                                                    <div class="row">

                                                        <div class="col-md-6">

                                                            <a href="<?php echo get_edit_user_link(); ?>" class="btn btn-default btn-sm"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <?php _e('Change Passowrd'); ?></a>

                                                        </div>

                                                        <div class="col-md-6">

                                                            <a href="<?php echo wp_logout_url(); ?>" class="btn btn-default btn-sm pull-right"><i class="fa fa-power-off" aria-hidden="true"></i> <?php _e('Sign Out'); ?></a>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </li>

                                    </ul>

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        

        <?php endif; ?>

        

        
		<?php 
		
		$cover_photo = ($poi_acf?get_field( "cover_photo", 'user_'.$author_id):array('url'=>'https://androidbubbles.files.wordpress.com/2019/02/google-maps-banner2a.jpg'));
		if(isset($cover_photo['url']) && $cover_photo['url']!=''): ?>
		<div id="gmap"><img src="<?php echo $cover_photo['url']; ?>" /></div>
        <?php endif; ?>
		<?php //pree($cover_photo['url']); ?>
		<?php echo wp_inbox_do_map_to('gmap', $address); ?>

        

        

        

        

        

        

        

        <div style="padding-top:50px; float:left; width:100%;"> </div>

		<div class="row">
        <div class="col-lg-3 col-md-3 hidden-sm hidden-xs">

            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="media upp">

                        <div>
							<?php if(get_current_user_id()==$author->ID){ ?>
                            <a href="/my-account/">
                            <?php } ?>
                            <img class="thumbnail img-responsive" src="<?php echo $profile_pic; ?>" />
							<?php if(get_current_user_id()==$author->ID){ ?>
                            </a>
                            <?php } ?>
                        </div>

                        

                    </div>
                    
                    
					<div class="media-body bio_wrapper">

                            

                            <?php if($author_bio): ?>
							<hr>
                            <h3><strong><?php _e('Bio'); ?></strong></h3>

                            <p><?php echo $author_bio; ?></p>

                            <hr>

                            <?php endif; ?>


							<h3><strong><?php _e('Age'); ?></strong></h3>

                            <p><?php echo $age; ?></p>

                            <hr>
                            
							<h3><strong><?php _e('Services/Products'); ?></strong></h3>

                            <p><?php echo $services_products; ?></p>

                            <hr>
                            
							<h3><strong><?php _e('Location'); ?></strong></h3>

                            <p><?php echo $location; ?></p>

                            <hr>
                                                                                    
                            
                        </div>                    

                </div>

            </div>

        </div>

        <div class="col col-md-9">

            <div class="panel panel-default">

                <div class="panel-body">

                    <span>

                        <h1 class="panel-title pull-left" style="font-size:30px;"><?php echo $author->display_name; ?> <small></small> <i class="fa fa-check text-success" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="<?php echo $author->display_name; ?> <?php _e('is a verified user'); ?>"></i><br />
                        <?php echo $company_name; ?>
                        </h1>
						
                        
                        <?php if(function_exists('wp_ca_valid_user') && wp_ca_valid_user($author->ID)): ?>
                        <div class="dropdown pull-right" style="position:absolute; right:10px">

                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">

                                <?php _e('Contact'); ?>

                                

                            </button>

                            <ul class="dropdown-menu request_custom_wrapper" aria-labelledby="dropdownMenu1">

                                <li><a class="request_custom"><?php echo __('Custom').' '.__('Order').'/'.__('Service'); ?></a></li>


								<?php if($inbox_thread_id>0): ?>
                                <li role="separator" class="divider"></li>

                                <li><a href="/inbox/?i=<?php echo $inbox_thread_id; ?>"><i class="fa fa-fw fa-inbox" aria-hidden="true"></i> <?php _e('Something else?'); ?></a></li>
                                <?php endif; ?>

                            </ul>

                        </div>
                        
                        
                        
                        <?php 
							if(function_exists('wp_inbox_message_box')){
								wp_inbox_message_box($author->ID, true, array('default_text'=>__('Hello '.$author->display_name.','), 'button_text'=>__('Send Request'), 'caption_text'=>__('Hi, Please describe your service request below be as detailed as possible.'))); 
							}
						
						?>
                        
                        <?php endif; ?>

                    </span>

					<?php if(!empty($tags)): ?>
                    <br><br>

						
                    <i class="fa fa-tags" aria-hidden="true"></i> <?php echo implode('', $tags); ?> 
					
                    <?php endif; ?>
                    <br><br><hr>

                    <span class="pull-left hide">

                        <a href="#" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-fw fa-files-o" aria-hidden="true"></i> Posts <span class="badge hide">0</span></a>

                        <a href="#" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-fw fa-picture-o" aria-hidden="true"></i> Photos <span class="badge hide">1</span></a>

                        <a href="#" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-fw fa-users" aria-hidden="true"></i> Contacts <span class="badge hide">2</span></a>

                    </span>

                    <span class="pull-right hide">

                        <a href="#" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-lg fa-at" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Mention"></i></a>
						<?php if($inbox_thread_id>0): ?>
                        <a href="/inbox/?i=<?php echo $inbox_thread_id; ?>" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-lg fa-envelope-o" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Message"></i></a>
                        <?php endif; ?>

                        <a href="#" class="btn btn-link" style="text-decoration:none;"><i class="fa fa-lg fa-ban" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Ignore"></i></a>

                    </span>

                </div>

            </div>

            

            <!-- Simple post content example. -->

            <div class="panel panel-default">

                <div class="panel-body">

                

<?php



				

				if(!empty($seller_products)){

?>

<div class="row products_display">

<?php					

					foreach($seller_products as $products){

						$featured_img_url = get_the_post_thumbnail_url($products->ID,'full'); 	

?>



                    <div class="col col-md-3">

                        <a href="<?php echo get_permalink($products->ID); ?>" class="inner">

                            <div class="li-img">

                                <img src="<?php echo $featured_img_url; ?>" alt="<?php echo $products->post_title; ?>" />

                            </div>

                            <div class="li-text">

                                <h4 class="li-head"><?php echo $products->post_title; ?></h4>

                                <p class="li-sub"><?php echo wp_trim_words($products->post_excerpt, 20); ?></p>

                            </div>

                        </a>

                    </div>

<?php						

					}

?>

</div>

<?php						

				}

				

?>				                

                    

                    

                    

                    

                    

                    

                    

                    

                    

                  

                    

                </div>

            </div>

            

        </div>

	</div>
    </div>

</div>

    			



				

			</div><!-- .col-inner -->

		</div><!-- .large-12 -->

	</div><!-- .row -->

</div>



<?php

do_action( 'flatsome_after_page' );

get_footer();



}