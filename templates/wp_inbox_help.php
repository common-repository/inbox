<form class="col col-md-12 form-horizontal wp_inbox_help_page">
  <input type="hidden" value="" name="wp_inbox_dept" id="wp_inbox_dept" />
  <div class="form-group">
    <h2><?php the_title(); ?></h2>
    <h3><?php _e('Submit a request', 'inbox'); ?></h3>
  </div>

  <div class="form-group">
    <div class="dropdown help-dept-selection department-dropdown">
      <button class="btn btn-danger btn-lg " type="button" data-toggle="dropdown"><?php _e('Select the relevant department', 'inbox'); ?>
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
        <?php
        $items = array();
		
        if (!empty($departments)) :
          foreach ($departments as $key => $d_item) : list($id, $title, $description) = $d_item;
            //$key = wp_inbox_underscore($title);
            $items[$id] = '<li><a data-key="' . ($id) . '" title="' . $description . '">' . ucwords($title) . '</a></li>';
          endforeach;
          ksort($items);
        endif;


        echo implode('', $items);
        ?>
      </ul>
    </div>
  </div>

    <div class="alert alert-success hides clear-fix dp_online_alert my-3">
        <strong class="dp_name"></strong>
        <?php _e(' department is online now ', 'inbox'); ?>
        <a href=""><?php _e('Click Here', 'inbox'); ?></a> <?php _e('for live chat or leave a message below.', 'inbox'); ?>
    </div>

  <div class="form-group <?php echo (is_user_logged_in() ? 'hide' : ''); ?>">
    <label for="wp_inbox_help_email"><?php _e('Email address', 'inbox'); ?></label>
    <input type="email" class="form-control" id="wp_inbox_help_email" aria-describedby="emailHelp" placeholder="<?php _e('Enter email', 'inbox'); ?>">
    <small id="emailHelp" class="form-text text-muted"><?php _e("We'll never share your email with anyone else.", 'inbox'); ?></small>
  </div>

  <div class="form-group">
    <label for="wp_inbox_help_subject"><?php _e('Subject', 'inbox'); ?></label>
    <input type="text" class="form-control" id="wp_inbox_help_subject" placeholder="">
  </div>


  <div class="form-group">
    <label for="wp_inbox_help_description"><?php _e('Description', 'inbox'); ?></label>
    <textarea class="form-control" id="wp_inbox_help_description" rows="6"></textarea>
  </div>


  <div class="form-group">
    <button type="button" class="btn btn-primary submit"><?php _e('Submit', 'inbox'); ?></button>
  </div>

</form>

<div class="alert alert-success hides clear-fix"><strong><?php _e('Thank you', 'inbox'); ?>!</strong> <?php _e('Our team will respond you shortly.', 'inbox'); ?></div>
<div class="alert alert-warning hides clear-fix">
  <strong><?php _e('Warning', 'inbox'); ?>!</strong> <?php _e('Please write something before submit.', 'inbox'); ?>
</div>
<div class="alert alert-danger hides clear-fix">
  <strong><?php _e('Alert', 'inbox'); ?>!</strong> <?php _e('Please select a relevant department to contact.', 'inbox'); ?>
</div>