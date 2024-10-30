// JavaScript Document



inbox_ws = typeof  inbox_ws == 'undefined' ? false : inbox_ws;


jQuery(document).ready(function($){



	$('.wp_inbox_message_box_toggle').click(function(){

		$('.wp_inbox_message_box_toggle').show();

		$('.wp_inbox_message_box').hide();

		$(this).hide();

		$(this).parent().find('.wp_inbox_message_box').slideDown();

	});


	$('.wp_inbox_message_cancel').on('click', function(){

		$('.wp_inbox_message_box_toggle, .request_custom').show();
		$(this).parent().hide();
		$('.wp_inbox_message_success').html('').hide();
		
	});

	$('.wp_inbox_message_send.lin').on('click', function(){

		var btn = $(this);

		var wp_inbox_message = btn.parents().eq(1).find('textarea[name="wp_inbox_message"]').val();

		

		if($.trim(wp_inbox_message)){

			var data = {

				'action': 'wpinboxmessagesend',

				'wp_inbox_message': wp_inbox_message,
				
				'wp_inbox_ref_link': window.location.href,

				'obj_id': $('#wp_nonce').val()

			};

			if(wp_inbox.receiver_id != -1){
				data.obj_id = btoa(wp_inbox.receiver_id);
			}


			if(typeof wp_inbox_send_msg == 'function' && inbox_ws != false){

				wp_inbox_send_msg($);

			}

			$('#wp_inbox_message').val('');
			$.post(wp_inbox.ajaxurl, data, function(response) {

				

				btn.parents().eq(1).find('textarea[name="wp_inbox_message"]').val('');



				if(wp_inbox.is_chat_ajax_based || inbox_ws == false || wp_inbox.is_inbox_page != 'yes' || !wp_inbox.is_pro) {

					btn.parents().eq(1).find('.wp_inbox_message_success').html(response).show().delay(60000).fadeOut();

				}

				btn.parents().eq(1).find('.wp_inbox_message_box_toggle').delay(60000).fadeIn();

				btn.parents().eq(1).find('.wp_inbox_message_box').slideUp();

				

				

			});

		}

	});	

	$('.wp_inbox_email_notifications .btn-toggle').click(function() {
		$(this).find('.btn').toggleClass('active');  
		
		if ($(this).find('.btn-primary').length>0) {
			$(this).find('.btn').toggleClass('btn-primary');
		}
		if ($(this).find('.btn-danger').length>0) {
			$(this).find('.btn').toggleClass('btn-danger');
		}
		if ($(this).find('.btn-success').length>0) {
			$(this).find('.btn').toggleClass('btn-success');
		}
		if ($(this).find('.btn-info').length>0) {
			$(this).find('.btn').toggleClass('btn-info');
		}
		
		$(this).find('.btn').toggleClass('btn-default');
	   
		var data = {
			'action': 'wp_inbox_mail_notification',
			'switch': $(this).find('.active').data('switch')
		};



		$.post(wp_inbox.ajaxurl, data, function(response) {
			
		});
		
	});	

	$('#wp_help_message_send').on('click', function(){

		var btn = $(this);

		var wp_inbox_message = $('textarea[name="wp_inbox_message"]').val();



		if($.trim(wp_inbox_message)){

			var data = {

				'action': 'wphelpmessagesend',

				'wp_inbox_message': wp_inbox_message,
				
				'wp_inbox_ref_link': window.location.href,

				'obj_id': $('#wp_nonce').val(),

				'obj_id2': $('#wp_nonces').val()

			};




			if(typeof  wp_inbox_send_msg == 'function' && inbox_ws != false){

				wp_inbox_send_msg($);

			}

			$('#wp_inbox_message').val('');
			$.post(wp_inbox.ajaxurl, data, function(response) {



				if(wp_inbox.is_chat_ajax_based || inbox_ws == false || !wp_inbox.is_pro) {

					$('.wp_inbox_message_success').html(response).show().delay(20000).fadeOut();

				}

				setTimeout(function(){

					if(wp_inbox.is_chat_ajax_based || inbox_ws == false || !wp_inbox.is_pro){

						window.location.reload();

					}

				}, 6000);



			});

		}

	});		

	setTimeout(function(){

		if(wp_inbox.unread>0){
			$('.woocommerce-MyAccount-navigation-link--inbox').addClass('unread').find('a').append('<span>('+wp_inbox.unread+')</span>');
			
		}

	}, 1000);

	

	$('.help-dept-selection li').on('click', 'a', function(){

		$(this).parents().eq(3).find('button').html($(this).html()+'<span class="caret"></span>');

		$(this).parents().eq(2).find('.active').removeClass('active');

		$(this).parent().addClass('active');

		$('#wp_inbox_dept').val($(this).data('key'));

		

	});

	$('.wp_inbox_help_page .submit').on('click', function(){

		

		var wp_inbox_help_description = $('#wp_inbox_help_description').val();

		if($.trim(wp_inbox_help_description)){

			$('div.alert').fadeOut();
			$('.wpinbox-errresp').remove();
			

			var wp_inbox_dept = $('#wp_inbox_dept').val();

			

			if(wp_inbox_dept!=''){

				var data = {

					'action': 'wpinboxhelppage',

					'wp_inbox_help_email': $('#wp_inbox_help_email').val(),

					'wp_inbox_help_subject': $('#wp_inbox_help_subject').val(),

					'wp_inbox_help_description': wp_inbox_help_description,

					'wp_inbox_dept': wp_inbox_dept

				};

		

				$.post(wp_inbox.ajaxurl, data, function(response) {

					

					response = $.parseJSON(response);
					//console.log(response);
					if(response.msg){

						$('.wp_inbox_help_page').slideUp();

						$('.alert-success').fadeIn();

					}else{
						$('<div class="alert alert-danger clear-fix wpinbox-errresp">'+response.desc+'</div>').insertAfter($('.wp_inbox_help_page'));
						
					}

	

					

				});

				

			}else{

				$('div.alert-danger').fadeIn();

			}

		}else{

			$('div.alert-warning').fadeIn();

		}

	});

	

	$('.star').on('click', function () {

      $(this).toggleClass('star-checked');

    });



    $('.ckbox label').on('click', function () {

      $(this).parents('tr').toggleClass('selected');

    });

    $('.btn-filter').on('click', function () {

      var $target = $(this).data('target');

      if ($target != 'all') {

        $('.table tr').css('display', 'none');

        $('.table tr[data-status="' + $target + '"]').fadeIn('slow');

      } else {

        $('.table tr').css('display', 'none').fadeIn('slow');

      }

    });	

	$('.tl-messages').on('click', '.media-bodi', function(){

		var href = $(this).data('href');

		

		if(typeof href != 'undefined')

		document.location.href = href;

	});
	
	
	$('.wp_inbox_users_search').on('keyup', function(){
		var text = $.trim($(this).val());
		//$("ul.wp_inbox_user_strip .visible, ul.wp_inbox_user_list .visible").removeClass('visible');
		//console.log(text);
		if(text!=''){						
			//$("ul.wp_inbox_user_strip li:contains('"+text+"')")
			//$("ul.wp_inbox_user_strip li").filter(":contains('" + text + "')").addClass('visible');
			
			var divs = $("ul.wp_inbox_user_strip li, ul.wp_inbox_user_list li");					
			for (var i = 0; i < divs.length; i++) {
			  //console.log(divs[i]);
			  var para = $(divs[i]).find('p').html();
			  if(typeof para != 'undefined'){
				  var index = para.toLowerCase().indexOf(text.toLowerCase());
				  targetId = divs[i].id;
				  if (index != -1) {					 
					 $('#'+targetId).removeClass('invisible').addClass('visible');
					 
				  }else{
					 $('#'+targetId).removeClass('visible').addClass('invisible');
					 
				  }
			  }
			}  
					
		}else{
			$("ul.wp_inbox_user_strip .visible, ul.wp_inbox_user_list .invisible").removeClass('invisible').addClass('visible');
		}
		
	});
	
	$('.dropdown-toggle').on('click', function(){
		var id = $(this).attr('id');
		$('[aria-labelledby="'+id+'"]').toggle();
	});


	if(typeof wp_inbox_ready_function == 'function'){

		wp_inbox_ready_function($)
	}


	$('.wp-inbox-messages select[name="wp_inbox_tz"]').on('change', function(){

		var data = {

			'action': 'wpinboxtz',
			'timezone': $(this).val()

		};

		$.post(wp_inbox.ajaxurl, data, function(response) {
			
			response = $.parseJSON(response);
			//console.log(response);
			if(response.msg){

				$('.wp_inbox_help_page').slideUp();

				$('.alert-success').fadeIn();

			}else{

				
			}

		});

		

	});
});

