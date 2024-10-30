<div class="wp-inbox-messages">



<div class="container nopadding">

	<div class="row">


		<section class="content col">
        	<a class="btn btn-info" href="<?php echo get_permalink($wp_inbox_pages['Inbox']); ?>"><?php _e('Back', 'inbox'); ?></a>

			<h1><?php echo $sender->display_name; ?></h1>

			<div class="col-md-12 nopadding">

				<div class="panel panel-default">

					<div class="panel-body" id="wp_inbox_table_container">



						<div class="table-container">

							<table class="table table-filter tl-messages thread wp_inbox_msg_table">

								<tbody>

                                <?php

                                global $wp_inbox_pro;

                                $chat_ids = wp_inbox_enqueue_chat_ids();

                                if($wp_inbox_pro && is_array($chat_ids)){

                                    extract($chat_ids);


                                    $photo1 = get_avatar_url( get_current_user_id() );
                                    $photo2 = get_avatar_url( $receiver_id );

                                    $receiver = get_user_by('id', $receiver_id);
                                    $display_name = $receiver ? $receiver->display_name : $dept_name;


                                    ?>


                                    <tr data-status="pendiente" class="wp_inbox_msg_tr_sample selected  not_me_tr" style="display: none">

                                        <td colspan="3">
                                            <div class="media">
                                                <a class="pull-left">
                                                    <img src="<?php echo $photo2; ?>" class="media-photo" />
                                                </a>
                                                <div class="media-bodi">
                                                    <span class="media-meta pull-right msg_time"></span>
                                                    <h4 class="title" title="<?php echo $dept_desc; ?>" style="padding: 10px 0 5px 0;">
                                                        <?php echo $display_name.': '; ?>

                                                        <span class="pull-right pendiente hide">(Pendiente)</span>
                                                    </h4>
                                                    <p class="summary"></p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr data-status="pendiente" class="wp_inbox_msg_tr_sample me_tr" style="display: none">
                                        <td colspan="3">
                                            <div class="media">
                                                <a class="pull-left">
                                                    <img src="<?php echo $photo1; ?>" class="media-photo" />
                                                </a>
                                                <div class="media-bodi">
                                                    <span class="media-meta pull-right msg_time"></span>
                                                    <h4 class="title" title="<?php echo $dept_desc; ?>" style="padding: 10px 0 5px 0;">
                                                        Me:
                                                        <span class="pull-right pendiente hide">(Pendiente)</span>
                                                    </h4>
                                                    <p class="summary"></p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

<?php

                                }
				if(!empty($ms)){

					$photo = '';

					

					foreach($ms as $msg){

						//pree($msg);

						list($str, $user_id, $time) = $msg;

						if($str!=''){

							

							

							$me = ($user_id==get_current_user_id());

							

							$photo = get_avatar_url($user_id);

							

							$user_this = get_user_by('id', $user_id);

							

	?>

	

    

                    <tr data-status="wp_inbox_msg_tr pendiente" class="<?php echo (is_admin()?'':($me?'':'selected')); echo $me ? ' me_tr' : ' not_me_tr'?>?> " style="">



                        <td colspan="3">

                            <div class="media">

                                <a class="pull-left">

                                    <img src="<?php echo ($photo); ?>" class="media-photo" />

                                </a>

                                <div class="media-bodi">

                                    <span class="media-meta pull-right"><?php echo wp_inbox_date($time); ?></span>

                                    <h4 class="title" data-profile="<?php echo $profile = $me?get_author_posts_url($user_id):get_author_posts_url($user_this->ID); ?>">

                                       <a href="<?php echo $profile; ?>" target="_blank"><?php echo (is_admin()?($user_this->display_name.': '):($me?'Me: ':$sender->display_name.': ')); ?></a>

                                        <span class="pull-right pendiente hide">(Pendiente)</span>

                                    </h4>

                                    <p class="summary"><?php echo $str; ?></p>

                                </div>

                            </div>

                        </td>

                    </tr>    



	<?php

						}

					}

				}

	?>

            

                

                

                <!-- <tr><td colspan="3"><?php 

				

				

				//pree($sender_id);

				if(!is_admin())

				wp_inbox_message_box($sender_id, true); ?></td></tr> -->

			

			

	

    						

									

									

									

									

								</tbody>

							</table>

						</div>

					</div>
					<div class="panel-footer">
						
					<?php 

				

				

						//pree($sender_id);

						if(!is_admin())

						wp_inbox_message_box($sender_id, true); ?>
					</div>

				</div>

				

			</div>

		</section>

		

	</div>

</div>



</div>

<style type="text/css">



</style>