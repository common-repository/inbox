<?php

	defined('ABSPATH') or die(__('No script kiddies please!', 'inbox'));
	
	if (!is_user_logged_in()) {
	
		wp_die(__('You do not have sufficient permissions to access this page.', 'inbox'));
	
	}

    $all_messages = ($wp_inbox_pro?wp_inbox_get_admin_messages():array());
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <section class="content col">
                    <a class="btn btn-info" href="<?php echo get_permalink($wp_inbox_pages['Inbox']); ?>"><?php _e('Back', 'inbox'); ?></a>

                    <h2 class="text-center py-2"><?php _e('Admin Messages', 'inbox'); ?></h2>


                    <div class="card w-100" style="max-height: 800px; overflow: auto">

                        <div class="card-body" id="wp_inbox_admin_msg_container">

                        <?php

                            if(!empty($all_messages)){

                                $all_messages_ids = array_keys($all_messages);
                                $current_user = get_current_user_id();
                                $current_user_read_msgs = get_user_meta($current_user,'wp_inbox_read_msg', true);
                                $current_user_read_msgs = is_array($current_user_read_msgs) ? $current_user_read_msgs : array();
                                $new_msgs = array_diff($all_messages_ids, $current_user_read_msgs);
                                $read_msgs = array_merge($all_messages_ids, $current_user_read_msgs);
                                $read_msgs = array_unique($read_msgs);
                                update_user_meta($current_user,'wp_inbox_read_msg', $read_msgs);

                                foreach($all_messages as $msg_id => $msg_array){

                                    $is_new = in_array($msg_id, $new_msgs);

                                    ?>


                                    <div class="card w-100 mb-2">

                                        <div class="card-body" id="wp_inbox_table_container">


                                            <div class="text-info mb-3" >
                                                <?php echo wp_inbox_date($msg_array['time']); ?>
                                                <?php if($is_new): ?>
                                                    <span class="badge badge-primary float-right"><?php _e('New', 'inbox') ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="message_str">
                                                <?php echo $msg_array['message']; ?>
                                            </div>


                                        </div>

                                    </div>


                                    <?php


                                }

                            }else{


                                ?>

                                <div class="alert alert-warning text-center"><?php _e('No message found.', 'inbox'); ?></div>

                                <?php

                            }

                        ?>


                        </div>

                    </div>


            </section>

        </div>
    </div>
</div>
