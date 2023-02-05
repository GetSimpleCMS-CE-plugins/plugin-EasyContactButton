<?php

# get correct id for plugin
$EasyContactButton=basename(__FILE__, ".php");

$EasyContactButton_data = GSDATAOTHERPATH . 'EasyContactButton-settings.xml';
$site_url = $SITEURL;
$plugin_folder = basename(GSPLUGINPATH);
$plugin_url = $site_url.$plugin_folder; 

# add in this plugin's language file
i18n_merge($EasyContactButton) || i18n_merge($EasyContactButton, 'en_US');

# register plugin
register_plugin(
	$EasyContactButton,								# ID of plugin, should be filename minus php
	i18n_r($EasyContactButton.'/MAIN_TITLE'), 		# Title of plugin
	'1.0',											# Version of plugin
	'islander',										# Author of plugin
	'http://get-simple.info/forums/member.php?action=profile&uid=4228',	# Author URL
	i18n_r($EasyContactButton.'/DESCRIPTION'), 		# Plugin Description
	'plugins',										# Page type of plugin
	'easy_button_settings'							# Function that displays content
);

# Front-End Hooks
add_action('theme-header','easy_button_css');
add_action('theme-footer','easy_button_js'); 

# Back-End Hooks
add_action('plugins-sidebar','createSideMenu', array($EasyContactButton, i18n_r($EasyContactButton.'/MAIN_TITLE')));

# ===== # ===== # ===== # =====

# ===== functions Header =====
function easy_button_css() {
	global $EasyContactButton_data;
	global $SITEURL;
	$file= $EasyContactButton_data;
	$s = getXML($file);
	if (  $s->include_fa  == 'yes'){
	echo '
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">';}
	
	echo '
		<link href="'.$SITEURL.'plugins/EasyContactButton/assets/css/easy-chat-support.min.css" rel="stylesheet">';
}

# ===== functions Content =====

	global $EasyContactButton_data;
	$file= $EasyContactButton_data;
	$s = getXML($file); 
	
	if (  $s->include_div_man  == 'yes'){
	add_action('content-bottom','easy_button_div_own');

	function easy_button_div_own(){
		echo '';
	};		

	} else {
	add_action('content-bottom','easy_button_div_wa');

		function easy_button_div_wa() {
			global $EasyContactButton_data;
			$file= $EasyContactButton_data;
			$s = getXML($file); 
			
			if (  $s->buttonType  == 'type_wa'){
			echo '
			<div id="whatsapp-us-button"></div>
			';
			};
		}

add_action('content-bottom','easy_button_div_contact');

		function easy_button_div_contact() {
			global $EasyContactButton_data;
			$file= $EasyContactButton_data;
			$s = getXML($file); 
			
			if (  $s->buttonType  == 'type_contact'){
			echo '
			<div id="contact-us-button"></div>
			';
			};
		};
	}

# ===== functions Footer =====

function easy_button_js() {
	global $SITEURL;
	$file = GSDATAOTHERPATH . 'EasyContactButton-settings.xml';  
	if (file_exists($file)) {
		$s = getXML($file);   
		
		$buttonType 		= false;
		$include_jquery 	= false;
		$include_fa 		= false;
		$include_div_man 	= false;
		
		$wa_position 		= false;
		$wa_speechBubble 	= false;
		$wa_avatar 			= false;
		$wa_popup_title 	= false;
		$wa_popup_description = false;
		$wa_popup_message 	= false;
		$wa_popup_textbox 	= false;
		$wa_phone 			= false;
		
		$wa_mon_start 		= false;
		$wa_mon_end 		= false;
		$wa_tue_start 		= false;
		$wa_tue_end 		= false;
		$wa_wed_start 		= false;
		$wa_wed_end			= false;
		$wa_thu_start 		= false;
		$wa_thu_end 		= false;
		$wa_fri_start 		= false;
		$wa_fri_end 		= false;
		$wa_sat_start 		= false;
		$wa_sat_end 		= false;
		$wa_sun_start 		= false;
		$wa_sun_end			= false;
		
		$wa_browser_tab 	= false;
		
		$ci_position 		= false;
		$ci_button_title 	= false;
		$ci_button_color 	= false;
		$ci_automaticOpen 	= false;
		$ci_popup_title 	= false;
		$ci_popup_description = false;
		$ci_phone_description = false;
		$ci_phone_display 	= false;
		$ci_phone_link 		= false;
		
		$ci_mon_start 		= false;
		$ci_mon_end 		= false;
		$ci_tue_start 		= false;
		$ci_tue_end 		= false;
		$ci_wed_start 		= false;
		$ci_wed_end			= false;
		$ci_thu_start 		= false;
		$ci_thu_end 		= false;
		$ci_fri_start 		= false;
		$ci_fri_end 		= false;
		$ci_sat_start 		= false;
		$ci_sat_end 		= false;
		$ci_sun_start 		= false;
		$ci_sun_end			= false;
		
		$ci_email_description	= false;
		$ci_email_display	= false;
		$ci_email_link		= false;
		$ci_address_description	= false;
		$ci_address_display	= false;
		$ci_address_link	= false;
		
		$ci_browser_tab 	= false;
	
		$buttonType 		= $s->buttonType;
		$include_jquery 	= $s->include_jquery;
		$include_fa 		= $s->include_fa;
		$include_div_man 	= $s->include_div_man;
		
		$wa_position 		= $s->wa_position;
		$wa_speechBubble 	= $s->wa_speechBubble;
		$wa_avatar 			= $s->wa_avatar;
		$wa_popup_title 	= $s->wa_popup_title;
		$wa_popup_description = $s->wa_popup_description;
		$wa_popup_message 	= $s->wa_popup_message;
		$wa_popup_textbox 	= $s->wa_popup_textbox;
		$wa_phone 			= $s->wa_phone;
		
		$wa_mon_start 		= $s->wa_mon_start;
		$wa_mon_end 		= $s->wa_mon_end;
		$wa_tue_start 		= $s->wa_tue_start;
		$wa_tue_end			= $s->wa_tue_end;
		$wa_wed_start 		= $s->wa_wed_start;
		$wa_wed_end			= $s->wa_wed_end;
		$wa_thu_start 		= $s->wa_thu_start;
		$wa_thu_end 		= $s->wa_thu_end;
		$wa_fri_start 		= $s->wa_fri_start;
		$wa_fri_end 		= $s->wa_fri_end;
		$wa_sat_start 		= $s->wa_sat_start;
		$wa_sat_end 		= $s->wa_sat_end;
		$wa_sun_start 		= $s->wa_sun_start;
		$wa_sun_end 		= $s->wa_sun_end;
		
		$wa_browser_tab 	= $s->wa_browser_tab;
		
		$ci_position 		= $s->ci_position;
		$ci_button_title 	= $s->ci_button_title;
		$ci_button_color 	= $s->ci_button_color;
		$ci_automaticOpen 	= $s->ci_automaticOpen;
		$ci_popup_title 	= $s->ci_popup_title;
		$ci_popup_description = $s->ci_popup_description;
		$ci_phone_description = $s->ci_phone_description;
		$ci_phone_display 	= $s->ci_phone_display;
		$ci_phone_link 		= $s->ci_phone_link;
		
		$ci_mon_start 		= $s->ci_mon_start;
		$ci_mon_end 		= $s->ci_mon_end;
		$ci_tue_start 		= $s->ci_tue_start;
		$ci_tue_end			= $s->ci_tue_end;
		$ci_wed_start 		= $s->ci_wed_start;
		$ci_wed_end			= $s->ci_wed_end;
		$ci_thu_start 		= $s->ci_thu_start;
		$ci_thu_end 		= $s->ci_thu_end;
		$ci_fri_start 		= $s->ci_fri_start;
		$ci_fri_end 		= $s->ci_fri_end;
		$ci_sat_start 		= $s->ci_sat_start;
		$ci_sat_end 		= $s->ci_sat_end;
		$ci_sun_start 		= $s->ci_sun_start;
		$ci_sun_end 		= $s->ci_sun_end;
		
		$ci_email_description = $s->ci_email_description;
		$ci_email_display 	= $s->ci_email_display;
		$ci_email_link 		= $s->ci_email_link;
		$ci_address_description = $s->ci_address_description;
		$ci_address_display = $s->ci_address_display;
		$ci_address_link 	= $s->ci_address_link;
		
		$ci_browser_tab 	= $s->ci_browser_tab;
		
		if (  $s->include_jquery  == 'yes'){
			echo '
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>';
		}
		if (  $s->buttonType  == 'type_wa'){
			echo '
			<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.40/moment-timezone-with-data.js"></script>
			<script src="'.$SITEURL.'plugins/EasyContactButton/assets/js/easy-chat-support.min.js"></script>
			
			<script>
				$(\'#whatsapp-us-button\').easyChatSupport({
					button: {
						position: "' . $wa_position . '", // left, right
						speechBubble: "' . $wa_speechBubble . '",
					},
					popup: {
						persons: [{
							avatar: {
								src: \'<img src="'.$SITEURL.'plugins/EasyContactButton/assets/img/'.$wa_avatar.'" alt="">\', 
								backgroundColor: "#ffffff",
							},
							text: {
								title: "' . $wa_popup_title . '",
								description: "' . $wa_popup_description . '",
								message: "' . $wa_popup_message . '",
								textbox: "' . $wa_popup_textbox . '",
								button: !1
							},
							link: {
								desktop: "https://web.whatsapp.com/send?phone=' . $wa_phone . '&text=Hi",
								mobile: "https://wa.me/' . $wa_phone . '/?text=Hi"
							},
							onlineDay: {
								sunday: "' . $wa_sun_start . '-' . $wa_sun_end . '",
								monday: "' . $wa_mon_start . '-' . $wa_mon_end . '",
								tuesday: "' . $wa_tue_start . '-' . $wa_tue_end . '",
								wednesday: "' . $wa_wed_start . '-' . $wa_wed_end . '",
								thursday: "' . $wa_thu_start . '-' . $wa_thu_end . '",
								friday: "' . $wa_fri_start . '-' . $wa_fri_end . '",
								saturday: "' . $wa_sat_start . '-' . $wa_sat_end . '"
							}
						}]
					},
					changeBrowserTitle: "' . $wa_browser_tab . '",
				});
			</script>
				';
		}
		
		if (  $s->buttonType  == 'type_contact'){
			echo '
			<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.40/moment-timezone-with-data.js"></script>
			<script src="'.$SITEURL.'plugins/EasyContactButton/assets/js/easy-chat-support.min.js"></script>
			<script>
				$(\'#contact-us-button\').easyChatSupport({
					button: {
						position: "' . $ci_position . '", // left, right
						style: 3,
						src: \'<i class="fas fa-info-circle"></i>\',
						backgroundColor: "' . $ci_button_color . '",
						effect: 5,
						notificationNumber: !1,
						speechBubble: !1,
						pulseEffect: !1,
						text: {
							title: "' . $ci_button_title . '",
						}
					},
					popup: {
						automaticOpen: ' . $ci_automaticOpen . ', // true, false
						effect: 8,
						header: {
							backgroundColor: "' . $ci_button_color . '",
							title: "' . $ci_popup_title . '",
							description: "' . $ci_popup_description . '"
						},
						persons: [{
							avatar: {
								src: \'<i class="fas fa-phone-alt"></i>\',
								backgroundColor: "' . $ci_button_color . '",
								onlineCircle: !1
							},
							text: {
								title: "' . $ci_phone_display . '", // display-as
								description: "' . $ci_phone_description . '",
								online: !1,
								offline: !1
							},
							link: {
								desktop: "tel:' . $ci_phone_link . '", // number-no-spaces
								mobile: !1
							},
							onlineDay: {
								sunday: "' . $ci_sun_start . '-' . $ci_sun_end . '",
								monday: "' . $ci_mon_start . '-' . $ci_mon_end . '",
								tuesday: "' . $ci_tue_start . '-' . $ci_tue_end . '",
								wednesday: "' . $ci_wed_start . '-' . $ci_wed_end . '",
								thursday: "' . $ci_thu_start . '-' . $ci_thu_end . '",
								friday: "' . $ci_fri_start . '-' . $ci_fri_end . '",
								saturday: "' . $ci_sat_start . '-' . $ci_sat_end . '"
							}
						}, {
							avatar: {
								src: \'<i class="fas fa-envelope"></i>\',
								backgroundColor: "' . $ci_button_color . '",
								onlineCircle: !1
							},
							text: {
								title: "' . $ci_email_display . '", // display-as
								description: "' . $ci_email_description . '",
								online: !1,
								offline: !1
							},
							link: {
								desktop: "' . $ci_email_link . '",
								mobile: !1
							},
							onlineDay: {
								sunday: "00:00-23:59", monday: "00:00-23:59", tuesday: "00:00-23:59", wednesday: "00:00-23:59", thursday: "00:00-23:59", friday: "00:00-23:59", saturday: "00:00-23:59"
							}
						}, {
							avatar: {
								src: \'<i class="fas fa-map-marker-alt"></i>\',
								backgroundColor: "' . $ci_button_color . '",
								onlineCircle: !1
							},
							text: {
								title: "' . $ci_address_display . '",
								description: "' . $ci_address_description . '",
								online: !1,
								offline: !1
							},
							link: {
								desktop: "' . $ci_address_link . '",
								mobile: !1
							},
							onlineDay: {
								sunday: "00:00-23:59", monday: "00:00-23:59", tuesday: "00:00-23:59", wednesday: "00:00-23:59", thursday: "00:00-23:59", friday: "00:00-23:59", saturday: "00:00-23:59"
							}
						}]
					},
					changeBrowserTitle: !1,
				});
			</script>
				';
		}
	
		return true;
	} else {
		return false;
	}  
}

# ===== # ===== # ===== # =====

# ===== functions Admin =====

function EasyContactButton_form_process () { 
	global $EasyContactButton_data_setting;
}

function easy_button_settings() {
	
	global $SITEURL;
	global $EasyContactButton_data;
	
	if(isset($_POST['submit'])) {
		$EasyContactButton_submitted_data['buttonType'] 		= $_POST['buttonType'];
		$EasyContactButton_submitted_data['include_jquery'] 	= $_POST['include_jquery'];
		$EasyContactButton_submitted_data['include_fa'] 		= $_POST['include_fa'];
		$EasyContactButton_submitted_data['include_div_man'] 	= $_POST['include_div_man'];
		
		$EasyContactButton_submitted_data['wa_position'] 		= $_POST['wa_position'];
		$EasyContactButton_submitted_data['wa_speechBubble'] 	= $_POST['wa_speechBubble'];
		$EasyContactButton_submitted_data['wa_avatar'] 			= $_POST['wa_avatar'];
		$EasyContactButton_submitted_data['wa_popup_title'] 	= $_POST['wa_popup_title'];
		$EasyContactButton_submitted_data['wa_popup_description'] = $_POST['wa_popup_description'];
		$EasyContactButton_submitted_data['wa_popup_message'] 	= $_POST['wa_popup_message'];
		$EasyContactButton_submitted_data['wa_popup_textbox'] 	= $_POST['wa_popup_textbox'];
		$EasyContactButton_submitted_data['wa_phone'] 			= $_POST['wa_phone'];
		
		$EasyContactButton_submitted_data['wa_mon_start'] 		= $_POST['wa_mon_start'];
		$EasyContactButton_submitted_data['wa_mon_end'] 		= $_POST['wa_mon_end'];
		$EasyContactButton_submitted_data['wa_tue_start'] 		= $_POST['wa_tue_start'];
		$EasyContactButton_submitted_data['wa_tue_end'] 		= $_POST['wa_tue_end'];
		$EasyContactButton_submitted_data['wa_wed_start'] 		= $_POST['wa_wed_start'];
		$EasyContactButton_submitted_data['wa_wed_end'] 		= $_POST['wa_wed_end'];
		$EasyContactButton_submitted_data['wa_thu_start'] 		= $_POST['wa_thu_start'];
		$EasyContactButton_submitted_data['wa_thu_end'] 		= $_POST['wa_thu_end'];
		$EasyContactButton_submitted_data['wa_fri_start'] 		= $_POST['wa_fri_start'];
		$EasyContactButton_submitted_data['wa_fri_end'] 		= $_POST['wa_fri_end'];
		$EasyContactButton_submitted_data['wa_sat_start'] 		= $_POST['wa_sat_start'];
		$EasyContactButton_submitted_data['wa_sat_end'] 		= $_POST['wa_sat_end'];
		$EasyContactButton_submitted_data['wa_sun_start'] 		= $_POST['wa_sun_start'];
		$EasyContactButton_submitted_data['wa_sun_end'] 		= $_POST['wa_sun_end'];
		
		$EasyContactButton_submitted_data['wa_browser_tab'] 	= $_POST['wa_browser_tab'];
		
		$EasyContactButton_submitted_data['ci_position'] 		= $_POST['ci_position'];
		$EasyContactButton_submitted_data['ci_button_title'] 	= $_POST['ci_button_title'];
		$EasyContactButton_submitted_data['ci_button_color'] 	= $_POST['ci_button_color'];
		$EasyContactButton_submitted_data['ci_automaticOpen'] 	= $_POST['ci_automaticOpen'];
		$EasyContactButton_submitted_data['ci_popup_title'] 	= $_POST['ci_popup_title'];
		$EasyContactButton_submitted_data['ci_popup_description'] = $_POST['ci_popup_description'];
		$EasyContactButton_submitted_data['ci_phone_description'] = $_POST['ci_phone_description'];
		$EasyContactButton_submitted_data['ci_phone_display'] 	= $_POST['ci_phone_display'];
		$EasyContactButton_submitted_data['ci_phone_link'] 		= $_POST['ci_phone_link'];
		
		$EasyContactButton_submitted_data['ci_mon_start'] 		= $_POST['ci_mon_start'];
		$EasyContactButton_submitted_data['ci_mon_end'] 		= $_POST['ci_mon_end'];
		$EasyContactButton_submitted_data['ci_tue_start'] 		= $_POST['ci_tue_start'];
		$EasyContactButton_submitted_data['ci_tue_end'] 		= $_POST['ci_tue_end'];
		$EasyContactButton_submitted_data['ci_wed_start'] 		= $_POST['ci_wed_start'];
		$EasyContactButton_submitted_data['ci_wed_end'] 		= $_POST['ci_wed_end'];
		$EasyContactButton_submitted_data['ci_thu_start'] 		= $_POST['ci_thu_start'];
		$EasyContactButton_submitted_data['ci_thu_end'] 		= $_POST['ci_thu_end'];
		$EasyContactButton_submitted_data['ci_fri_start'] 		= $_POST['ci_fri_start'];
		$EasyContactButton_submitted_data['ci_fri_end'] 		= $_POST['ci_fri_end'];
		$EasyContactButton_submitted_data['ci_sat_start'] 		= $_POST['ci_sat_start'];
		$EasyContactButton_submitted_data['ci_sat_end'] 		= $_POST['ci_sat_end'];
		$EasyContactButton_submitted_data['ci_sun_start'] 		= $_POST['ci_sun_start'];
		$EasyContactButton_submitted_data['ci_sun_end'] 		= $_POST['ci_sun_end'];
		
		$EasyContactButton_submitted_data['ci_email_description'] = $_POST['ci_email_description'];
		$EasyContactButton_submitted_data['ci_email_display'] 	= $_POST['ci_email_display'];
		$EasyContactButton_submitted_data['ci_email_link'] 		= $_POST['ci_email_link'];
		$EasyContactButton_submitted_data['ci_address_description'] = $_POST['ci_address_description'];
		$EasyContactButton_submitted_data['ci_address_display'] 	= $_POST['ci_address_display'];
		$EasyContactButton_submitted_data['ci_address_link'] 	= $_POST['ci_address_link'];
		
		$EasyContactButton_submitted_data['ci_browser_tab'] 	= $_POST['ci_browser_tab'];
		
		$result = EasyContactButton_save_settings($EasyContactButton_submitted_data);
    }
    
	$EasyContactButton_data_setting = EasyContactButton_read_settings();

	if(isset($result)) {
		if($result == true) { 
			echo '<p class="updated" style="background: #DFF8D9; border: solid 1px #ccc; padding: 10px; color: #777; border-radius: 7px; display: block;">✔️ Settings saved.</p>';

			echo "<meta http-equiv='refresh' content='6.75;url=". $_SERVER ['REQUEST_URI']. "'>";
		} elseif($result == false) { 
			echo '<p class="error">❌ Error saving data. Check permissions.</p>';
		}
	}
	
# ===== Start Form  =====

	echo '<link href="'.$SITEURL.'plugins/EasyContactButton/style.min.css" rel="stylesheet">';
	
	echo '
	<div class="my-plugin">
		<div class="container">
			<div class="row">
				<div class="col-12 center">
	';
	echo '<h3>'.i18n_r("EasyContactButton/PAGE_TITLE").'</h3>
	<p>'.i18n_r("EasyContactButton/DESCRIPTION").'</p>';
	echo '
				</div>
			</div>
		</div>
	';

?> 

	<form method="post" id="EasyContact-admin" action="<?php echo $_SERVER ['REQUEST_URI']; ?>" ononSubmit="window.location.reload()" value="refresh">
		<hr class="style-one">
		<div class="container">
		
			<div class="row">
				<div class="col-2"></div>
				<div class="col-4">
					<legend>Include WhatsApp or Contact button method:</legend>
					<fieldset>
							<label for="buttonType">
								<input type="radio" id="buttonType" name="buttonType" value="type_wa" <?php echo ($EasyContactButton_data_setting['buttonType'] == 'type_wa' ? 'checked' : ''); ?> /> Auto-Include WhatsApp
							</label>
							<label for="buttonType">
								<input type="radio" id="buttonType" name="buttonType" value="type_contact" <?php echo ($EasyContactButton_data_setting['buttonType'] == 'type_contact' ? 'checked' : ''); ?> /> Auto-Include Contact
							</label>
					</fieldset>
				</div>
				
				<div class="col-4">
					<legend>Include the required elements in your theme?</legend>
					<fieldset>
						<label for="include_jquery">
							<input type="checkbox" id="include_jquery" name="include_jquery" value="yes" <?php echo ($EasyContactButton_data_setting['include_jquery'] == 'yes' ? 'checked' : ''); ?> /> Jquery
						</label>
						<label for="include_fa">
							<input type="checkbox" id="include_fa" name="include_fa"  value="yes" <?php echo ($EasyContactButton_data_setting['include_fa'] == 'yes' ? 'checked' : ''); ?> /> FontAwesome
						</label>
					</fieldset>
				</div>
				<div class="col-2"></div>
			</div>	
			<hr class="style-three">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-4">
					<legend>Manually add place-holders &#60;div&#62; into body of your theme?</legend>
					<label for="include_div_man">
						<input type="checkbox" id="include_div_man" name="include_div_man"  value="yes" <?php echo ($EasyContactButton_data_setting['include_div_man'] == 'yes' ? 'checked' : ''); ?> /> Yes, I am a profesional.
					</label>
				</div>
				<div class="col-6 center">
					<legend></legend>
					<div class="code"><span>&#60;div id="whatsapp-us-button"&#62;&#60;/div&#62;</span><br><br> or  <br><br><span>&#60;div id="contact-us-button"&#62;&#60;/div&#62;</span></div>
				</div>	
			</div>
			
			<hr class="style-three">
			
			<div class="row">
				<div class="col-6 highlight">
					<fieldset class="whatsapp-info">
						<div class="container ">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/whatsapp.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> WhatsApp</legend>
							<hr class="style-two">
							<p>
								<label for="wa_position">Button Position</label>
								<select id="wa_position" name="wa_position">
									<option value="right">Right</option>
									<option value="left">Left</option>
								</select>
							</p>
							<p>
								<label for="wa_speechBubble">Mini Speech Bubble</label>   
								<input type="text" id="wa_speechBubble" name="wa_speechBubble" value="<?php echo $EasyContactButton_data_setting['wa_speechBubble']; ?>"> 
								<div class="code">ex.: <span>How can we help you?</span></div> 
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="wa_avatar">Popup Avatar</label>
								<select id="wa_avatar" name="wa_avatar">
									<option value="male.svg">Man</option>
									<option value="female.svg">Woman</option>
									<option value="whatsapp.svg">Icon</option>
								</select>
							</p>
							<p>
								<label for="wa_popup_title">Popup Title</label>   
								<input type="text" id="wa_popup_title" name="wa_popup_title" value="<?php echo $EasyContactButton_data_setting['wa_popup_title']; ?>">
								<div class="code">ex.: <span>Need help? Chat with us</span></div>
							</p>
							<p>
								<label for="wa_popup_description">Popup Description</label>   
								<input type="text" id="wa_popup_description" name="wa_popup_description" value="<?php echo $EasyContactButton_data_setting['wa_popup_description']; ?>">
								<div class="code">ex.: <span>Customer Support</span></div>
							</p>
							<p>
								<label for="wa_popup_message">Popup Message</label>   
								<input type="text" id="wa_popup_message" name="wa_popup_message" value="<?php echo $EasyContactButton_data_setting['wa_popup_message']; ?>">
								<div class="code">ex.: <span>Hi there &amp#128578; &lt;br&gt;How can I help you?</span></div>
							</p>
							<p>
								<label for="wa_popup_textbox">Popup Textbox</label>   
								<input type="text" id="wa_popup_textbox" name="wa_popup_textbox" value="<?php echo $EasyContactButton_data_setting['wa_popup_textbox']; ?>">
								<div class="code">ex.: <span>Ask us anything!</span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="wa_phone">Phone Number</label>   
								<input type="text" id="wa_phone" name="wa_phone" value="<?php echo $EasyContactButton_data_setting['wa_phone']; ?>">
								<div class="code">Including country code, no spaces. ex.: <span>123456789</span></div>
							</p>
							
							<div class="">
								<label for="time">Message Availability:</label>
								
								<div class="col-12 right">
									<span class="smaller">Monday</span>
									<input type="time" name="wa_mon_start" value="<?php echo $EasyContactButton_data_setting['wa_mon_start']; ?>"></input>-
									<input type="time" name="wa_mon_end" value="<?php echo $EasyContactButton_data_setting['wa_mon_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Tuesday</span>
									<input type="time" name="wa_tue_start" value="<?php echo $EasyContactButton_data_setting['wa_tue_start']; ?>"></input>-
									<input type="time" name="wa_tue_end" value="<?php echo $EasyContactButton_data_setting['wa_tue_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Wednesday</span>
									<input type="time" name="wa_wed_start" value="<?php echo $EasyContactButton_data_setting['wa_wed_start']; ?>"></input>-
									<input type="time" name="wa_wed_end" value="<?php echo $EasyContactButton_data_setting['wa_wed_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Thursday</span>
									<input type="time" name="wa_thu_start" value="<?php echo $EasyContactButton_data_setting['wa_thu_start']; ?>"></input>-
									<input type="time" name="wa_thu_end" value="<?php echo $EasyContactButton_data_setting['wa_thu_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Friday</span>
									<input type="time" name="wa_fri_start" value="<?php echo $EasyContactButton_data_setting['wa_fri_start']; ?>"></input>-
									<input type="time" name="wa_fri_end" value="<?php echo $EasyContactButton_data_setting['wa_fri_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Saturday</span>
									<input type="time" name="wa_sat_start" value="<?php echo $EasyContactButton_data_setting['wa_sat_start']; ?>"></input>-
									<input type="time" name="wa_sat_end" value="<?php echo $EasyContactButton_data_setting['wa_sat_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Sunday</span>
									<input type="time" name="wa_sun_start" value="<?php echo $EasyContactButton_data_setting['wa_sun_start']; ?>"></input>-
									<input type="time" name="wa_sun_end" value="<?php echo $EasyContactButton_data_setting['wa_sun_end']; ?>"></input>
								</div>
							</div>
							
							<div class="col-12"><hr class="style-three"></div>
							
							<p>
								<label for="wa_browser_tab">Browser Tab Title</label>   
								<input type="text" id="wa_browser_tab" name="wa_browser_tab" value="<?php echo $EasyContactButton_data_setting['wa_browser_tab']; ?>">
								<div class="code">ex.: <span>(1) New Message!</span></div>
							</p>
							
						</div>
					</fieldset>
				</div>
				
				<div class="col-6 highlight" style="margin-left 10px;">
					
					<fieldset class="contact-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/info.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> Contact Info</legend>
							<hr class="style-two">
							<p>
								<label for="ci_position">Button Position</label>
								<select id="ci_position" name="ci_position">
									<option value="left">Left</option>
									<option value="right">Right</option>
								</select>
							</p>
							<p>
								<label for="ci_button_title">Button Title</label>   
								<input type="text" id="ci_button_title" name="ci_button_title" value="<?php echo $EasyContactButton_data_setting['ci_button_title']; ?>"> 
								<div class="code">ex.: <span>Contact Us</span></div> 
							</p>
							
							<p>
								<label for="ci_button_color">Button/Popup Color</label>
								<input type="color" id="ci_button_color" name="ci_button_color" value="<?php echo $EasyContactButton_data_setting['ci_button_color']; ?>">
								<div class="code">ex.: <span>#ed2d34</span> = <span>rbg: 237 45 52</span></div>
							</p>
							
							<p>
								<label for="ci_automaticOpen">Automatic Open?</label>
								<select id="ci_automaticOpen" name="ci_automaticOpen">
									<option value="true">True</option>
									<option value="false">False</option>
								</select>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_popup_title">Popup Title</label>   
								<input type="text" id="ci_popup_title" name="ci_popup_title" value="<?php echo $EasyContactButton_data_setting['ci_popup_title']; ?>">
								<div class="code">ex.: <span>Our Contact Information</span></div>
							</p>
							<p>
								<label for="ci_popup_description">Popup Description</label>   
								<input type="text" id="ci_popup_description" name="ci_popup_description" value="<?php echo $EasyContactButton_data_setting['ci_popup_description']; ?>">
								<div class="code">ex.: <span>You can contact us 24/7 at any time</span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_phone_description">Phone Description</label>   
								<input type="text" id="ci_phone_description" name="ci_phone_description" value="<?php echo $EasyContactButton_data_setting['ci_phone_description']; ?>">
								<div class="code">ex.: <span>Phone Number</span></div>
							</p>
							
							<p>
								<label for="ci_phone_display">Phone Number Display</label>   
								<input type="text" id="ci_phone_display" name="ci_phone_display" value="<?php echo $EasyContactButton_data_setting['ci_phone_display']; ?>">
								<div class="code">ex.: <span>+12 345 6789</span></div>
							</p>
							
							<p>
								<label for="ci_phone_link">Phone Number Link</label>   
								<input type="text" id="ci_phone_link" name="ci_phone_link" value="<?php echo $EasyContactButton_data_setting['ci_phone_link']; ?>">
								<div class="code">Including country code, no spaces. ex.: <span>123456789</span></div>
							</p>
							
							<div class="">
								<label for="time">Phone Availability:</label>
								
								<div class="col-12 right">
									<span class="smaller">Monday</span>
									<input type="time" name="ci_mon_start" value="<?php echo $EasyContactButton_data_setting['ci_mon_start']; ?>"></input>-
									<input type="time" name="ci_mon_end" value="<?php echo $EasyContactButton_data_setting['ci_mon_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Tuesday</span>
									<input type="time" name="ci_tue_start" value="<?php echo $EasyContactButton_data_setting['ci_tue_start']; ?>"></input>-
									<input type="time" name="ci_tue_end" value="<?php echo $EasyContactButton_data_setting['ci_tue_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Wednesday</span>
									<input type="time" name="ci_wed_start" value="<?php echo $EasyContactButton_data_setting['ci_wed_start']; ?>"></input>-
									<input type="time" name="ci_wed_end" value="<?php echo $EasyContactButton_data_setting['ci_wed_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Thursday</span>
									<input type="time" name="ci_thu_start" value="<?php echo $EasyContactButton_data_setting['ci_thu_start']; ?>"></input>-
									<input type="time" name="ci_thu_end" value="<?php echo $EasyContactButton_data_setting['ci_thu_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Friday</span>
									<input type="time" name="ci_fri_start" value="<?php echo $EasyContactButton_data_setting['ci_fri_start']; ?>"></input>-
									<input type="time" name="ci_fri_end" value="<?php echo $EasyContactButton_data_setting['ci_fri_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Saturday</span>
									<input type="time" name="ci_sat_start" value="<?php echo $EasyContactButton_data_setting['ci_sat_start']; ?>"></input>-
									<input type="time" name="ci_sat_end" value="<?php echo $EasyContactButton_data_setting['ci_sat_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller">Sunday</span>
									<input type="time" name="ci_sun_start" value="<?php echo $EasyContactButton_data_setting['ci_sun_start']; ?>"></input>-
									<input type="time" name="ci_sun_end" value="<?php echo $EasyContactButton_data_setting['ci_sun_end']; ?>"></input>
								</div>
							</div>
							
							<div class="col-12"><hr class="style-three"></div>
							
							<p>
								<label for="ci_email_description">Email Description</label>   
								<input type="text" id="ci_email_description" name="ci_email_description" value="<?php echo $EasyContactButton_data_setting['ci_email_description']; ?>">
								<div class="code">ex.: <span>Email Address</span></div>
							</p>
							
							<p>
								<label for="ci_email_display">Email Display</label>   
								<input type="text" id="ci_email_display" name="ci_email_display" value="<?php echo $EasyContactButton_data_setting['ci_email_display']; ?>">
								<div class="code">ex.: <span>Email@WebSite.com</span></div>
							</p>
							
							<p>
								<label for="ci_email_link">Email Link</label>   
								<input type="text" id="ci_email_link" name="ci_email_link" value="<?php echo $EasyContactButton_data_setting['ci_email_link']; ?>">
								<div class="code">ex.: <span>email@website.com</span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_address_description">Address Description</label>   
								<input type="text" id="ci_address_description" name="ci_address_description" value="<?php echo $EasyContactButton_data_setting['ci_address_description']; ?>">
								<div class="code">ex.: <span>Office Address</span></div>
							</p>
							
							<p>
								<label for="ci_address_display">Address</label>   
								<input type="text" id="ci_address_display" name="ci_address_display" value="<?php echo $EasyContactButton_data_setting['ci_address_display']; ?>">
								<div class="code">ex.: <span>240 Libety Road, New York</span></div>
							</p>
							
							<p>
								<label for="ci_address_link">Google Map Link</label>   
								<input type="text" id="ci_address_link" name="ci_address_link" value="<?php echo $EasyContactButton_data_setting['ci_address_link']; ?>">
								<div class="code">ex.: <span>https://www.google.com/maps/@40.7558962,-73.9889626,16z</span></div>
							</p>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		
		<div class="col-12 center">
			<input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" />
		</div>
		
		<hr class="style-three" style="margin:30px 0;">
		
		<div class="col-12 center">
			<p class="small" style="margin-bottom:0;">Created by: <img src=" data:image/gif;base64,R0lGODlhIAAgAKIHAGBgYO/v7yAgIL+/v39/fxAQEAAAAP///yH5BAEAAAcALAAAAAAgACAAAAP/eHox+zDKFUARhsy9BQiMoHEL4EiD8KTkYhQnNACQsQG4k9EBASqBwu/AmlgIHkNAiYkdAALQAMaBBjAHg0fg7BW0zsiAcBkYzCYVqbhIiZAAQo46kX4ZA8eFwO8/tX1DClMzBBkvBQd9fAAGZ4sFQjKRXIB0FAWNmlAmEUGGoAZxgjgWMzBhD1B9b6aDXDAwVhJfjme2tjQHInFEmRcoBYyNZzJRSkB+Ej0CwqLIJYwtD2RTZDgPSr3TRFpUcVyvu6kceT9ycgqjNtzLtyAmM+1GKTo9uvMQi0GKI/kreQY0UESO2z0Mtjz4+JciUx6AFsJNc1OQmgCJG6zNIzQhNU+ngUAEhhySZ8RDIlBgVNjjxYOiCyrcKJnRLI8wIhh8TMkUIIgHB1DCpfQnY0hPCk6SCkoAADs="> islander</p><p class="smaller" style="margin-bottom:0;">Special thanks to: multicolor</p>
		</div>
	</form>
</div><!-- end my-plugin-->

<?php
} 
function EasyContactButton_read_settings() {
	global $EasyContactButton_data;

	if(file_exists($EasyContactButton_data)) {
		$data = getXML($EasyContactButton_data);
		$EasyContactButton_data_setting['buttonType'] 		= $data->buttonType;
		$EasyContactButton_data_setting['include_jquery'] 	= $data->include_jquery;
		$EasyContactButton_data_setting['include_fa'] 		= $data->include_fa;
		$EasyContactButton_data_setting['include_div_man'] 	= $data->include_div_man;
		
		$EasyContactButton_data_setting['wa_position'] 		= $data->wa_position;
		$EasyContactButton_data_setting['wa_speechBubble'] 	= $data->wa_speechBubble;
		$EasyContactButton_data_setting['wa_avatar'] 		= $data->wa_avatar;
		$EasyContactButton_data_setting['wa_popup_title'] 	= $data->wa_popup_title;
		$EasyContactButton_data_setting['wa_popup_description'] = $data->wa_popup_description;
		$EasyContactButton_data_setting['wa_popup_message'] = $data->wa_popup_message;
		$EasyContactButton_data_setting['wa_popup_textbox'] = $data->wa_popup_textbox;
		$EasyContactButton_data_setting['wa_phone'] 		= $data->wa_phone;
		
		$EasyContactButton_data_setting['wa_mon_start'] 	= $data->wa_mon_start;
		$EasyContactButton_data_setting['wa_mon_end'] 		= $data->wa_mon_end;
		$EasyContactButton_data_setting['wa_tue_start'] 	= $data->wa_tue_start;
		$EasyContactButton_data_setting['wa_tue_end'] 		= $data->wa_tue_end;
		$EasyContactButton_data_setting['wa_wed_start'] 	= $data->wa_wed_start;
		$EasyContactButton_data_setting['wa_wed_end'] 		= $data->wa_wed_end;
		$EasyContactButton_data_setting['wa_thu_start'] 	= $data->wa_thu_start;
		$EasyContactButton_data_setting['wa_thu_end'] 		= $data->wa_thu_end;
		$EasyContactButton_data_setting['wa_fri_start'] 	= $data->wa_fri_start;
		$EasyContactButton_data_setting['wa_fri_end'] 		= $data->wa_fri_end;
		$EasyContactButton_data_setting['wa_sat_start'] 	= $data->wa_sat_start;
		$EasyContactButton_data_setting['wa_sat_end'] 		= $data->wa_sat_end;
		$EasyContactButton_data_setting['wa_sun_start'] 	= $data->wa_sun_start;
		$EasyContactButton_data_setting['wa_sun_end'] 		= $data->wa_sun_end;
		
		$EasyContactButton_data_setting['wa_browser_tab'] 	= $data->wa_browser_tab;
		
		$EasyContactButton_data_setting['ci_position'] 		= $data->ci_position;
		$EasyContactButton_data_setting['ci_button_title'] 	= $data->ci_button_title;
		$EasyContactButton_data_setting['ci_button_color'] 	= $data->ci_button_color;
		$EasyContactButton_data_setting['ci_automaticOpen'] = $data->ci_automaticOpen;
		$EasyContactButton_data_setting['ci_popup_title'] 	= $data->ci_popup_title;
		$EasyContactButton_data_setting['ci_popup_description'] = $data->ci_popup_description;
		$EasyContactButton_data_setting['ci_phone_description'] = $data->ci_phone_description;
		$EasyContactButton_data_setting['ci_phone_display'] = $data->ci_phone_display;
		$EasyContactButton_data_setting['ci_phone_link'] 	= $data->ci_phone_link;
		
		$EasyContactButton_data_setting['ci_mon_start'] 	= $data->ci_mon_start;
		$EasyContactButton_data_setting['ci_mon_end'] 		= $data->ci_mon_end;
		$EasyContactButton_data_setting['ci_tue_start'] 	= $data->ci_tue_start;
		$EasyContactButton_data_setting['ci_tue_end'] 		= $data->ci_tue_end;
		$EasyContactButton_data_setting['ci_wed_start'] 	= $data->ci_wed_start;
		$EasyContactButton_data_setting['ci_wed_end'] 		= $data->ci_wed_end;
		$EasyContactButton_data_setting['ci_thu_start'] 	= $data->ci_thu_start;
		$EasyContactButton_data_setting['ci_thu_end'] 		= $data->ci_thu_end;
		$EasyContactButton_data_setting['ci_fri_start'] 	= $data->ci_fri_start;
		$EasyContactButton_data_setting['ci_fri_end'] 		= $data->ci_fri_end;
		$EasyContactButton_data_setting['ci_sat_start'] 	= $data->ci_sat_start;
		$EasyContactButton_data_setting['ci_sat_end'] 		= $data->ci_sat_end;
		$EasyContactButton_data_setting['ci_sun_start'] 	= $data->ci_sun_start;
		$EasyContactButton_data_setting['ci_sun_end'] 		= $data->ci_sun_end;
		
		$EasyContactButton_data_setting['ci_email_description'] = $data->ci_email_description;
		$EasyContactButton_data_setting['ci_email_display'] = $data->ci_email_display;
		$EasyContactButton_data_setting['ci_email_link'] 	= $data->ci_email_link;
		$EasyContactButton_data_setting['ci_address_description'] = $data->ci_address_description;
		$EasyContactButton_data_setting['ci_address_display'] = $data->ci_address_display;
		$EasyContactButton_data_setting['ci_address_link'] 	= $data->ci_address_link;
		
		$EasyContactButton_data_setting['ci_browser_tab'] 	= $data->ci_browser_tab; 
	}
	
	$EasyContactButton_data_setting['site_root'] = '/';
	return $EasyContactButton_data_setting;
}

function EasyContactButton_save_settings($settings) {
	global $EasyContactButton_data;

	$xml = @new simpleXMLElement('<EasyContactButton_settings></EasyContactButton_settings>');
	
	$xml->addChild('buttonType', $settings['buttonType']);
	$xml->addChild('include_jquery', $settings['include_jquery']);
	$xml->addChild('include_fa', $settings['include_fa']);
	$xml->addChild('include_div_man', $settings['include_div_man']);
	
	$xml->addChild('wa_position', $settings['wa_position']);
	$xml->addChild('wa_speechBubble', $settings['wa_speechBubble']);
	$xml->addChild('wa_avatar', $settings['wa_avatar']);
	$xml->addChild('wa_popup_title', $settings['wa_popup_title']);
	$xml->addChild('wa_popup_description', $settings['wa_popup_description']);
	$xml->addChild('wa_popup_message', $settings['wa_popup_message']);
	$xml->addChild('wa_popup_textbox', $settings['wa_popup_textbox']);
	$xml->addChild('wa_phone', $settings['wa_phone']);
	
	$xml->addChild('wa_mon_start', $settings['wa_mon_start']);
	$xml->addChild('wa_mon_end', $settings['wa_mon_end']);
	$xml->addChild('wa_tue_start', $settings['wa_tue_start']);
	$xml->addChild('wa_tue_end', $settings['wa_tue_end']);
	$xml->addChild('wa_wed_start', $settings['wa_wed_start']);
	$xml->addChild('wa_wed_end', $settings['wa_wed_end']);
	$xml->addChild('wa_thu_start', $settings['wa_thu_start']);
	$xml->addChild('wa_thu_end', $settings['wa_thu_end']);
	$xml->addChild('wa_fri_start', $settings['wa_fri_start']);
	$xml->addChild('wa_fri_end', $settings['wa_fri_end']);
	$xml->addChild('wa_sat_start', $settings['wa_sat_start']);
	$xml->addChild('wa_sat_end', $settings['wa_sat_end']);
	$xml->addChild('wa_sun_start', $settings['wa_sun_start']);
	$xml->addChild('wa_sun_end', $settings['wa_sun_end']);
	
	$xml->addChild('wa_browser_tab', $settings['wa_browser_tab']);
	
	$xml->addChild('ci_position', $settings['ci_position']);
	$xml->addChild('ci_button_title', $settings['ci_button_title']);
	$xml->addChild('ci_button_color', $settings['ci_button_color']);
	$xml->addChild('ci_automaticOpen', $settings['ci_automaticOpen']);
	$xml->addChild('ci_popup_title', $settings['ci_popup_title']);
	$xml->addChild('ci_popup_description', $settings['ci_popup_description']);
	$xml->addChild('ci_phone_description', $settings['ci_phone_description']);
	$xml->addChild('ci_phone_display', $settings['ci_phone_display']);
	$xml->addChild('ci_phone_link', $settings['ci_phone_link']);
	
	$xml->addChild('ci_mon_start', $settings['ci_mon_start']);
	$xml->addChild('ci_mon_end', $settings['ci_mon_end']);
	$xml->addChild('ci_tue_start', $settings['ci_tue_start']);
	$xml->addChild('ci_tue_end', $settings['ci_tue_end']);
	$xml->addChild('ci_wed_start', $settings['ci_wed_start']);
	$xml->addChild('ci_wed_end', $settings['ci_wed_end']);
	$xml->addChild('ci_thu_start', $settings['ci_thu_start']);
	$xml->addChild('ci_thu_end', $settings['ci_thu_end']);
	$xml->addChild('ci_fri_start', $settings['ci_fri_start']);
	$xml->addChild('ci_fri_end', $settings['ci_fri_end']);
	$xml->addChild('ci_sat_start', $settings['ci_sat_start']);
	$xml->addChild('ci_sat_end', $settings['ci_sat_end']);
	$xml->addChild('ci_sun_start', $settings['ci_sun_start']);
	$xml->addChild('ci_sun_end', $settings['ci_sun_end']);
	
	$xml->addChild('ci_email_description', $settings['ci_email_description']);
	$xml->addChild('ci_email_display', $settings['ci_email_display']);
	$xml->addChild('ci_email_link', $settings['ci_email_link']);
	$xml->addChild('ci_address_description', $settings['ci_address_description']);
	$xml->addChild('ci_address_display', $settings['ci_address_display']);
	$xml->addChild('ci_address_link', $settings['ci_address_link']);
	
	$xml->addChild('ci_browser_tab', $settings['ci_browser_tab']);

	return $xml->asXML($EasyContactButton_data);
};

?>