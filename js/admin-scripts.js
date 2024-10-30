// JavaScript Document
function parse_query_string(query) {
	var vars = query.split("&");
	var query_string = {};
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		// If first entry with this name
		if (typeof query_string[pair[0]] === "undefined") {
			query_string[pair[0]] = decodeURIComponent(pair[1]);
			// If second entry with this name
		} else if (typeof query_string[pair[0]] === "string") {
			var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
			query_string[pair[0]] = arr;
			// If third or later entry with this name
		} else {
			query_string[pair[0]].push(decodeURIComponent(pair[1]));
		}
	}
	return query_string;
}
jQuery(document).ready(function ($) {



	$('.woo_inst_checkout_options').on('click', function () {
		if ($(this).is(':checked')) {
			$(this).parent().addClass('selected');
		} else {
			$(this).parent().removeClass('selected');
		}
	});

	$('.wpinbox_settings_div a.nav-tab').click(function () {
		$(this).siblings().removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		$('.nav-tab-content').hide();
		$('.nav-tab-content').eq($(this).index()).show();
		window.history.replaceState('', '', wp_inbox.this_url + '&t=' + $(this).index());


	});

	$('#wp_inbox_add_dept').on('click', function () {

		var wp_inbox_dept_title = $('#wp_inbox_dept_title').val();

		if ($.trim(wp_inbox_dept_title) != '') {

			var data = {
				'action': 'wpinboxdeptadd',
				'wp_inbox_dept_id': $('#wp_inbox_dept_id').val(),
				'wp_inbox_dept_title': wp_inbox_dept_title,
				'wp_inbox_dept_desc': $('#wp_inbox_dept_desc').val()
			};

			$.post(ajaxurl, data, function (response) {

				$('#wp_inbox_dept_title, #wp_inbox_dept_desc').val('');
				document.location.href = wp_inbox.this_url + '&t=' + $('.wpinbox_settings_div a.nav-tab-active').index();

			});
		}

	});

	$('.wp_inbox_department_edit').on('click', function () {
		var obj = $(this).parents().eq(1);
		obj.parent().find('.selected').removeClass('selected');
		obj.addClass('selected');

		$('#wp_inbox_dept_id').val($(this).data('key'));
		$('#wp_inbox_dept_title').val(obj.find('td').eq(0).html());
		$('#wp_inbox_dept_desc').val(obj.find('td').eq(1).html());
		$('#wp_inbox_add_dept').html('Update');
		$('div.wp_inbox_add_dept').show();
		$('button.wp_inbox_add_dept').hide();
		$('.add_edit').show();
		$('.staff-members').hide();
	});

	$('button.wp_inbox_add_dept, a.wp_inbox_add_dept').on('click', function () {
		$('div.wp_inbox_add_dept').show();
		$('button.wp_inbox_add_dept').hide();
		$('.staff-members').hide();
		$('#wp_inbox_dept_id, #wp_inbox_dept_title, #wp_inbox_dept_desc').val('');
		$('#wp_inbox_add_dept').html('Add');
		$('.add_edit').hide();
	});


	$('.wp_inbox_department_delete').on('click', function () {
		var obj = $(this).parents().eq(1);

		var ask = confirm("Are you sure, you want to delete?");

		if (ask) {
			var data = {
				'action': 'wpinboxdeptdelete',
				'wp_inbox_dept_id': $(this).data('key')
			};

			obj.fadeOut();

			$.post(ajaxurl, data, function (response) {

				obj.remove();

			});
		}
	});


	$('.wp_inbox_department_staff').on('click', function () {

		var obj = $(this).parents().eq(1);
		obj.parent().find('.selected').removeClass('selected');
		obj.addClass('selected');

		var data = {
			'action': 'wpinboxdeptstaff',
			'wp_inbox_dept_id': $(this).data('key')
		};


		if ($(this).data('key') != '') {
			$('.staff-members').show();
		} else {
			$('.staff-members').hide();
		}

		$('input[name="wp_inbox_dept_staff"]').val($(this).data('key'));

		$('.staff-members input[name^="wp_inbox_dept"]').prop('disabled', true);

		$('.staff-members input[name^="wp_inbox_dept"]').prop('checked', false);

		$('div.wp_inbox_add_dept').hide();
		$('button.wp_inbox_add_dept').show();

		$.post(ajaxurl, data, function (response) {

			response = $.parseJSON(response);
			var staff = response.staff;


			$('.staff-members input[name^="wp_inbox_dept"]').prop('disabled', false);

			$.each(staff, function (i, v) {

				$('.staff-members input[name="wp_inbox_dept[' + v + ']').prop('checked', true);

			});

		});

	});

	var query = window.location.search.substring(1);
	var qs = parse_query_string(query);
	if (typeof (qs.t) != 'undefined') {
		$('.wpinbox_settings_div a.nav-tab').eq(qs.t).click();
	}
	if ($('.wpinbox_settings_div').length > 0)
		$('.wpinbox_settings_div').show();

	$('.wpinbox_settings_div').on('click', '.advance_settings ul li > a', function () {
		$(this).parent().find('div').toggle();
	});
	$('.wpinbox_settings_div').on('click', '.advance_settings ul li > div img', function () {
		window.open($(this).attr('src'));
	});

	$('.wp_copy_author').on('click', function () {

		var txt, r;


		var data = {
			'action': 'wp_copy_author_file',
		}

		$.post(ajaxurl, data, function (response, status) {
			if (status == 'success') {
				if(response.is_error != 'yes'){

					if (response.file_exist == 'yes') {
						$('.wp-inbox-modal').modal('show')
					}

					if(response.file_copy == 'yes'){
						alert('file coppied');
					}

				}else{
					alert(response.error);
				}
			}
		});

		

	});

	$('.author_replace_confirm').on('click', function () {
		
		$('.wp-inbox-modal').modal('hide');
		var data = {
			'action': 'wp_copy_author_file',
			'replace_file': 'yes',
		}

		$.post(ajaxurl, data, function (response, status) {
			if (status == 'success') {
				if(response.is_error != 'yes'){


					if (response.file_exist == 'yes' && response.file_copy == 'yes') {
						
						alert('file coppied');					

					}

				}else{

					alert(response.error);

				}
			}
		});

		

	});


	$('.live_chat_settings_container input:checkbox.live_chat_settings').on('change', function(){

		var wp_inbox_live_chat_settings =  {};
		var checkboxes = $('.live_chat_settings_container input:checkbox.live_chat_settings');
		var success_msg = $('.live_chat_settings_container .success_msg');

		$.each(checkboxes, function(){

			var value = $(this).prop('checked') ? 'on' : 'off';
			var name = $(this).prop('name');

			wp_inbox_live_chat_settings[name] = value;


		});



		var data = {
			'action' : 'wp_inbox_ajax_save_settings',
			'wp_inbox_live_chat_settings': wp_inbox_live_chat_settings,
			'wp_inbox_nonce_field' : wp_inbox.wp_inbox_nonce_field

		}

		$.post(ajaxurl, data, function(response, code){

			if(code == 'success' && response == 'saved'){

				success_msg.show();

				setTimeout(function(){
					success_msg.fadeOut();
				}, 5000);

			}
		});

	});


	$('input[name^="wpinbox_options"], select[name^="wpinbox_options"]').on('change', function(event){
		
		
		//console.log(event.target.type);
		//console.log($(this).parents().eq(1));
		//$(this).parents().eq(1).find('ul').toggleClass('d-none');
		
		var wpinbox_option_checked = $('input[name^="wpinbox_options"][type="checkbox"]:checked');
		var wpinbox_option_text = $('input[name^="wpinbox_options"][type="text"]');
		var wpinbox_option_select = $('select[name^="wpinbox_options"]');

		var wpinbox_options_post = {};

		if(wp_inbox.empty_settings){

			wpinbox_options_post['wpinbox_options_update'] = true;
			wp_inbox.empty_settings = false;

		}


			if(wpinbox_option_select.length > 0 ){
				$.each(wpinbox_option_select, function () {

					wpinbox_options_post[$(this).data('name')] = $(this).val();

				});
			}


			if(wpinbox_option_text.length > 0 ){
				$.each(wpinbox_option_text, function () {

					wpinbox_options_post[$(this).data('name')] = $(this).val();

				});
			}

			if(wpinbox_option_checked.length > 0 ){
				$.each(wpinbox_option_checked, function () {

					wpinbox_options_post[$(this).val()] = true;

				});
			}
		
		var wpinbox_option_colors = $('input[name^="wpinbox_options"][type="color"]');

		if(wpinbox_option_colors.length > 0 && !wp_inbox.empty_settings){
			$.each(wpinbox_option_colors, function () {

				wpinbox_options_post[$(this).attr('id')] = $(this).val();

			});
		}




		var data = {

			action : 'wpinbox_update_option',
			wpinbox_update_option_nonce : wp_inbox.nonce,
			wpinbox_options : wpinbox_options_post,

		}

		$.post(ajaxurl, data, function(code, response){

			//console.log(response);

			if(response == 'success' && !wp_inbox.empty_settings){

				$('.wpinbox-options .alert').removeClass('d-none').addClass('show');
				setTimeout(function(){
					$('.wpinbox-options .alert').addClass('d-none');
				}, 10000);

			}



		});
		

	});

	if(wp_inbox.empty_settings){

		$('input[name^="wpinbox_options"]').change();

	}


	var msg_load = $('.wpin_admin_msg_content .msg_main_loading img');
	var msg_update_btn = $('.wpin_admin_msg_content .wpin_msg_save.wpin_update');
	var msg_save_btn = $('.wpin_admin_msg_content .wpin_msg_save[data-status="draft"]:not(.wpin_update)');

	function wpin_show_update(view, msg_id = ''){

		if(view == 'show'){

			msg_update_btn.show();
			msg_save_btn.hide();
			msg_update_btn.data('id', msg_id)

		}else if(view == 'hide'){

			msg_update_btn.hide();
			msg_save_btn.show();
			msg_update_btn.data('id', '');

		}
	}

	$('.wpin_admin_msg_content .wpin_msg_save').on('click', function(){

		var msg_box = $('#wp_inbox_admin_msg');

		var msg_string = $('#wp_inbox_admin_msg').val();
		var msg_stauts = $(this).data('status');
		var msg_id = msg_update_btn.data('id');
		var send_date_type = $('.wp_inbox_send_type:checked');
		var send_date = $('input.wp_inbox_msg_date');



		if(msg_string.length < 1){

			alert(wp_inbox.msg_required)


			return;
		}

		if(send_date_type.val() == 'date' && send_date.val() == ''){

			alert(wp_inbox.date_required)

			return;
		}

		var msg_obj = {
			message : msg_string,
			status : msg_stauts,
			send_type : send_date_type.val(),
			deliver_date : send_date.val(),
		}

		var data = {

			action: 'wp_inbox_admin_msg_save',
			wp_inbox_nonce: wp_inbox.wp_inbox_nonce_field,
			wp_inbox_msg_obj : msg_obj,
			msg_id : msg_id,

		}



		console.log(data);
		msg_load.show();
		$.post(ajaxurl, data, function(response){

			msg_load.hide();
			if(response.status){

				msg_box.val('');
				$('#wp_inbox_msg_history_body').replaceWith(response.msg_body);
				wpin_show_update('hide');

			}

		});
	});


	$('.wpin_admin_msg_content .wpin_msg_update').on('click', function(){

		var msg_box = $('#wp_inbox_admin_msg');

		var msg_string = $('#wp_inbox_admin_msg').val();
		var msg_stauts = $(this).data('status');


		if(msg_string.length < 1){
			return;
		}

		var msg_obj = {
			message : msg_string,
			status : msg_stauts,
		}

		var data = {

			action: 'wp_inbox_admin_msg_save',
			wp_inbox_nonce: wp_inbox.wp_inbox_nonce_field,
			wp_inbox_msg_obj : msg_obj,

		}

		msg_load.show();
		$.post(ajaxurl, data, function(response){

			msg_load.hide();
			if(response.status){

				msg_box.val('');
				$('#wp_inbox_msg_history_body').replaceWith(response.msg_body)
			}

		});
	});

	//table action load, send and delete

	$('body').on('click', 'table tr .wp_msg_action', function(e){

		e.preventDefault();

		var this_action_btn = $(this);
		var this_parent = this_action_btn.parents('tr:first');
		var msg_id = this_parent.data('id');
		var this_msg = this_parent.data('message');
		this_msg = JSON.parse(atob(this_msg));
		var msg_status = this_msg.status;
		var action = this_action_btn.data('action');
		var wp_inbox_msg = this_parent.find('td.wp_inbox_msg').html();
		var msg_box = $('#wp_inbox_admin_msg');
		var action_load = this_parent.find('.msg_loading img');

		switch(action){

			case 'load':

				var send_date_type = $('.wp_inbox_send_type[value="'+this_msg.send_type+'"]');
				var send_date = $('input.wp_inbox_msg_date');
				send_date_type.prop('checked', true);

				if(this_msg.send_type == 'now'){
					send_date.val('');
				}else if(this_msg.send_type == 'date'){
					send_date.val(this_msg.deliver_date);
				}

				msg_box.val(wp_inbox_msg);
				send_date_type.change();

				if(msg_status == 'draft'){

					wpin_show_update('show', msg_id);

				}else{

					wpin_show_update('hide');

				}



				break;

			case 'send':
			case 'delete':

					action_load.show();

				var data = {

						action: 'wp_inbox_admin_msg_action',
						wp_inbox_nonce: wp_inbox.wp_inbox_nonce_field,
						msg_id : msg_id,
						wp_inbox_action : action,
						wp_inbox_msg_status : msg_status,

					}
					$.post(ajaxurl, data, function(response){

						if(response.status){
							msg_box.val('');
							$('#wp_inbox_msg_history_body').replaceWith(response.msg_body);
							action_load_done = $('#wp_inbox_msg_history_body tr[data-id="'+msg_id+'"] .msg_loading_done');
							wpin_show_update('hide');

						}


					});


				break;
		}


	});

	//change event for message sending now or date, if date is selected input date will appear

	$('body').on('change', '.wp_inbox_send_type', function(e){

		e.preventDefault();

		var this_radio = $(this);
		var date_input = $('.wp_inbox_msg_date');
		var type = this_radio.val();



		if(type == 'now'){

			date_input.hide();


		}else{

			date_input.css('display', 'block');
		}


	});


	//init datepicker field for message sending
	$('.wp_inbox_msg_date ').datepicker({

		minDate: new Date(),

	});


});