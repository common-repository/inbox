<?php global $wp_inbox_timezone; ?>
<div class="wp-inbox-messages">
	<?php $mail_notification = get_inbox_mail_notification(); ?>


	<div class="container nopadding">

		<div class="row">

			<section class="content col-lg-12 nopadding">

				<h1><?php _e('Conversations', 'inbox'); ?></h1>

				<div class="col-md-12 nopadding">

					<div class="panel panel-default">

						<div class="panel-heading">
							<div class="row">
							<div class="col-sm-6">
							<div class="pull-left wp_inbox_email_notifications">

								<strong><?php _e('Email Notifications', 'inbox'); ?></strong>
								<div class="onoffswitch">
									<div class="btn-group btn-toggle">
										<button data-switch="on" class="btn btn-sm <?php echo ($mail_notification ? 'active btn-info' : 'btn-default'); ?>"><?php _e('ON', 'inbox'); ?></button>
										<button data-switch="off" class="btn btn-sm <?php echo (!$mail_notification ? 'active btn-info' : 'btn-default'); ?>"><?php _e('OFF', 'inbox'); ?></button>
									</div>
								</div>



							</div>
							</div>
							<div class="col-sm-6">
							<div class="pull-right">

								<div class="btn-group">

									<button type="button" class="btn btn-success btn-filter btn-sm" data-target="messages"><?php _e('Messages', 'inbox'); ?></button>

									<button type="button" class="btn btn-warning btn-filter btn-sm" data-target="support"><?php _e('Support', 'inbox'); ?></button>

									<button type="button" class="btn btn-default btn-filter btn-sm" data-target="all"><?php _e('All', 'inbox'); ?></button>

								</div>
                                
                                <div class="text-right mr-2 mb-3">
                                <?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                                <?php if(!empty($tzlist)): ?>
                                <select name="wp_inbox_tz" title="<?php _e('Timezone Settings', 'inbox'); ?>">
                                <?php foreach($tzlist as $tz): ?>
                                	<option value="<?php echo $tz; ?>" <?php selected($wp_inbox_timezone==$tz); ?>><?php echo $tz; ?></option>
                                <?php endforeach; ?>    
                                </select>
                                <?php endif; ?>
                                </div>

							</div>
							</div>

							</div>

						</div>

						<div class="panel-body">



							<div class="table-container">

								<table class="table table-filter tl-messages">

									<tbody>






										<?php

                                        $wp_inbox_admin_messages = function_exists('wp_inbox_get_admin_messages') ? wp_inbox_get_admin_messages_html() : '';

                                        echo $wp_inbox_admin_messages;

										if (!empty($res)) {







											foreach ($res as $data) :


												$str = $time = $sender_id = '';



												$meta_value = maybe_unserialize($data->meta_value);



												$last = end($meta_value);

												list($str, $sender_id, $time) = $last;



												$other_user = (get_current_user_id() == $data->user_id ? str_replace('INBOX_', '', $data->meta_key) : $data->user_id);





												$sender = get_user_by('id', $other_user);





												$me = ($sender_id == get_current_user_id());





												$photo2 = get_avatar_url($other_user);







												$urquery = "SELECT * FROM $wpdb->usermeta WHERE (meta_key = '_unread_status_$other_user' AND user_id=" . get_current_user_id() . ") LIMIT 1";





												//pree($urquery);

												$_unread_status = $wpdb->get_row($urquery);



												?>

												<tr data-status="messages" class="<?php echo $_unread_status->meta_value ? 'selected' : ''; ?> tl-msg fi inbox" style="" data-template="<?php echo $data->umeta_id; ?>">

													<td>

														<div class="ckbox">

															<input type="checkbox" id="checkbox<?php echo $data->umeta_id; ?>">

															<label for="checkbox<?php echo $data->umeta_id; ?>"></label>

														</div>

													</td>

													<td>

														<a href="javascript:;" class="star hide">

															<i class="glyphicon glyphicon-star"></i>

														</a>

													</td>

													<td>

														<div class="media">

															<a class="pull-left">

																<img src="<?php echo ($photo2); ?>" class="media-photo" />

															</a>

															<div class="media-bodi" data-href="?i=<?php echo $data->umeta_id; ?>">

																<span class="media-meta pull-right"><?php echo wp_inbox_date($time); ?></span>

																<h4 class="title">



																	<?php echo $sender->display_name; ?>

																	<span class="pull-right pagado hide">(Pagado)</span>

																</h4>

																<p class="summary"><?php echo ($me ? 'Me: ' : '') . wp_trim_words($str, 13); ?></p>

															</div>

														</div>

													</td>

												</tr>

											<?php endforeach;
											} else if(!$wp_inbox_admin_messages){

												?>

											<tr>
												<td colspan="3"><?php _e('There are no messages in your inbox.', 'inbox'); ?></td>
											</tr>

										<?php

										}

										?>



										<?php



										if (!empty($res_help)) {




											function sort_by_time($a, $b)
											{

												$a = maybe_unserialize($a->meta_value);
												$b = maybe_unserialize($b->meta_value);

												$last_a = end($a);
												$last_b = end($b);

												list($str_a, $sender_id_a, $time_a) = $last_a;
												list($str_b, $sender_id_b, $time_b) = $last_b;

												return $time_b - $time_a;
											}

											usort($res_help, 'sort_by_time');


											foreach ($res_help as $data) :






												$str = $time = $sender_id = '';




												$meta_value = maybe_unserialize($data->meta_value);





												$last = end($meta_value);





												list($str, $sender_id, $time) = $last;





												$str = (is_array($str) ? array_filter($str, 'strlen') : array($str));





												$str = implode(', ', $str);





												$dept = '';



												if (!empty($depts)) {

													$dept = str_replace('HELP_BOX_', '', $data->meta_key);
												} else {

													//$dept = $data->user_id;

												}


												$other_user = (get_current_user_id() == $data->user_id ? str_replace('HELP_BOX_', '', $data->meta_key) : $data->user_id);

												//pree($other_user);



												$sender = get_user_by('id', $other_user);





												$me = ($sender_id == get_current_user_id());





												$photo2 = get_avatar_url($other_user);





												$dept_data = get_option(wp_inbox_option_by_id(($dept != '' ? $dept : $other_user)));

												//pree($dept_data);

												list($dept_name, $dept_desc) = $dept_data;





												if (empty($depts))

													$urquery = "SELECT * FROM $wpdb->usermeta WHERE (meta_key = '_unread_status_" . $other_user . "' AND user_id=" . get_current_user_id() . ") LIMIT 1";

												else

													$urquery = "SELECT * FROM $wpdb->usermeta WHERE (meta_key = '_unread_status_" . $other_user . "' AND user_id=$dept) LIMIT 1";



												//pree($urquery);



												$_unread_status = $wpdb->get_row($urquery);





												//pree($_unread_status);



												?>







												<tr data-status="support" class="<?php echo $_unread_status->meta_value ? 'selected' : ''; ?> tl-msg fi help" style="" data-template="<?php echo $data->umeta_id; ?>">

													<td>

														<div class="ckbox">

															<input type="checkbox" id="checkbox<?php echo $data->umeta_id; ?>">

															<label for="checkbox<?php echo $data->umeta_id; ?>"></label>

														</div>

													</td>

													<td>

														<a href="javascript:;" class="star">

															<i class="glyphicon glyphicon-star"></i>

														</a>

													</td>

													<td>

														<div class="media">

															<a class="pull-left">

																<img src="<?php echo ($photo2); ?>" class="media-photo" />

															</a>

															<div class="media-bodi" data-href="?h=<?php echo $data->umeta_id; ?>">

																<span class="media-meta pull-right"><?php echo wp_inbox_date($time); ?></span>

																<h4 class="title" title="<?php echo $dept_desc; ?>">



																	<?php echo $dept_name; ?>

																	<span class="pull-right pagado hide">(Pagado)</span>

																</h4>

																<p class="summary"><?php echo ($me ? 'Me: ' : '') . $str; ?></p>

															</div>

														</div>

													</td>

												</tr>

										<?php endforeach;
										}



										?>











									</tbody>

								</table>

							</div>

						</div>

					</div>

				</div>

			</section>

		</div>

	</div>

</div>

<style type="text/css">



</style>