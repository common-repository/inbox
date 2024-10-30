<div class="wp-inbox-messages">

<div class="container nopadding">
	<div class="row">
    	

		<section class="content col-md-12 nopadding">
        	<a class="btn btn-info" href="<?php echo get_permalink($wp_inbox_pages['Inbox']); ?>"><?php _e('Back', 'inbox'); ?></a>
			<h1><?php echo $dept_name; ?></h1>
			<div class="col-md-12">
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

//                                     pree($dept_name);
//                                     pree($dept_id);
//                                     pree($chat_ids);
//                                     pree($ms);

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
					$photo1 = get_avatar_url( get_current_user_id() );
					$photo2 = '';
					//pree(end($ms));
					foreach($ms as $msg){
						
						
						
						list($str, $user_id, $time) = $msg;
						$str = (is_array($str)?array_filter($str, 'strlen'):array($str));
						
						
						$str = implode(', ', $str);						
						
						if($str!=''){
							
							//pree($user_id);
							//pree($dept_id);
							$me = false;
							
							if($user_id==$dept_id){
								$display_name = $dept_name;
								$me = !empty($depts);
								
								
							}else{
								$display_name = (isset($sender->display_name)?$sender->display_name:'');
								$me = ($user_id==get_current_user_id());
								
							}
					
					
							
							if(!$me)
							$photo2 = ($photo2==''?get_avatar_url($user_id):$photo2);
							
							
							
							
							
	?>
	
    
                    <tr data-status="pendiente" class="wp_inbox_msg_tr <?php echo (is_admin()?'':($me?'':'selected '));  echo $me ? 'me_tr' : 'not_me_tr'?>" style="">

                        <td colspan="3">
                            <div class="media">
                                <a class="pull-left">
                                    <img src="<?php echo ($me?$photo1:$photo2); ?>" class="media-photo" />
                                </a>
                                <div class="media-bodi">
                                    <span class="media-meta pull-right msg_time"><?php echo wp_inbox_date($time); ?></span>
                                    <h4 class="title" title="<?php echo $dept_desc; ?>" style="padding: 10px 0 5px 0">
                                       <?php echo ($me?'Me: ':$display_name.': '); ?>
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
				
				if(!is_admin()){
					//pree($sender_id);
					if($umeta_results->user_id==get_current_user_id()){
						
						wp_help_message_box($dept_id, $umeta_results->user_id); 
					
					}else{
						//DEPARTMENT VIEW
					
						wp_help_message_box($umeta_results->user_id, $dept_id); 
						
					}
				}
				
				?></td></tr> -->
			
			
	
    						
									
									
									
									
								</tbody>
							</table>
						</div>
					</div>

					<div class="panel-footer">

					<?php 
				
				if(!is_admin()){
					//pree($sender_id);
					if($umeta_results->user_id==get_current_user_id()){
						
						wp_help_message_box($dept_id, $umeta_results->user_id); 
					
					}else{
						//DEPARTMENT VIEW
					
						wp_help_message_box($umeta_results->user_id, $dept_id); 
						
					}
				}
				
				?>

					</div>


				</div>
				
			</div>
		</section>
		
	</div>
</div>

</div>
<style type="text/css">

</style>