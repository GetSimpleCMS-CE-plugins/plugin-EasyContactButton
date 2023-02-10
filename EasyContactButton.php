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
	i18n_r($EasyContactButton.'/lang_Menu_Title'), 		# Title of plugin
	'1.2',											# Version of plugin
	'islander',										# Author of plugin
	'https://tinyurl.com/gs-islander',	# Author URL
	i18n_r($EasyContactButton.'/lang_Description'), # Plugin Description
	'plugins',										# Page type of plugin
	'easy_button_settings'							# Function that displays content
);

# Front-End Hooks
add_action('theme-header','easy_button_css');
add_action('theme-footer','easy_button_js'); 

# Back-End Hooks
add_action('plugins-sidebar','createSideMenu', array($EasyContactButton, i18n_r($EasyContactButton.'/lang_Menu_Title')));

# ===== # ===== # ===== # =====

# ===== functions Header =====
function easy_button_css() {
	global $EasyContactButton_data;
	global $SITEURL;
	$file= $EasyContactButton_data;
	$s = getXML($file);
	if (  $s->include_fa  == 'yes'){
		echo '
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">';
	}
	
	echo '
		<link href="'.$SITEURL.'plugins/EasyContactButton/assets/css/easy-chat-support.min.css" rel="stylesheet">';
		
	if (  $s->wa_hide_mobile  == 'yes'){
		echo '
		<style>@media(max-width: 768px){#whatsapp-us-button{display:none;}}</style>
';
	}
	if (  $s->ci_hide_mobile  == 'yes'){
		echo '
		<style>@media(max-width: 768px){#contact-us-button{display:none;}}</style>
';
	}
}

# ===== functions Content =====

	global $EasyContactButton_data;
	$file= $EasyContactButton_data;
	$s = getXML($file); 
	
	if (  @$s->include_div_man  == 'yes'){
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
		$wa_hide_mobile 	= false;
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
		$ci_button_shape 	= false;
		$ci_automaticOpen 	= false;
		$ci_hide_mobile 	= false;
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
		$wa_hide_mobile 	= $s->wa_hide_mobile;
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
		$ci_button_shape 	= $s->ci_button_shape;
		$ci_automaticOpen 	= $s->ci_automaticOpen;
		$ci_hide_mobile 	= $s->ci_hide_mobile;
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
						position: "' . $wa_position . '",
						speechBubble: "' . $wa_speechBubble . '",
					},
					popup: {
						effect: 15,
						persons: [{
							avatar: {
								src: \'<img src="'.$SITEURL.'plugins/EasyContactButton/assets/img/'.$wa_avatar.'" alt="">\', 
								backgroundColor: "#ffffff",
								onlineCircle: !0
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
						position: "' . $ci_position . '",
						style: ' .$ci_button_shape. ',
						src: \'<i class="fas fa-info-circle"></i>\',
						backgroundColor: "' . $ci_button_color . '",
						notificationNumber: !1,
						speechBubble: !1,
						pulseEffect: !0,
						text: {
							title: "' . $ci_button_title . '",
						}
					},
					popup: {
						automaticOpen: ' . $ci_automaticOpen . ',
						effect: 15,
						header: {
							backgroundColor: "' . $ci_button_color . '",
							title: "' . $ci_popup_title . '",
							description: "' . $ci_popup_description . '"
						},
						persons: [{
							avatar: {
								src: \'<i class="fas fa-phone-alt"></i>\',
								backgroundColor: "' . $ci_button_color . '",
								onlineCircle: !0
							},
							text: {
								title: "' . $ci_phone_display . '",
								description: "' . $ci_phone_description . '",
								online: !1,
								offline: !1
							},
							link: {
								desktop: "tel:' . $ci_phone_link . '",
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
								title: "' . $ci_email_display . '",
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
		$EasyContactButton_submitted_data['wa_hide_mobile'] 	= $_POST['wa_hide_mobile'];
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
		$EasyContactButton_submitted_data['ci_button_shape'] 	= $_POST['ci_button_shape'];
		$EasyContactButton_submitted_data['ci_automaticOpen'] 	= $_POST['ci_automaticOpen'];
		$EasyContactButton_submitted_data['ci_hide_mobile'] 	= $_POST['ci_hide_mobile'];
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
			echo '<p class="updated" style="background: #DFF8D9; border: solid 1px #ccc; padding: 10px; color: #777; border-radius: 7px; display: block;">✔️ '.i18n_r("EasyContactButton/lang_Status_Saved").'</p>';

			echo "<meta http-equiv='refresh' content='6.75;url=". $_SERVER ['REQUEST_URI']. "'>";
		} elseif($result == false) { 
			echo '<p class="error">❌ '.i18n_r("EasyContactButton/lang_Status_Error").'</p>';
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
	echo '<h3>'.i18n_r("EasyContactButton/lang_Page_Title").'</h3>
	<p>'.i18n_r("EasyContactButton/lang_Description").'</p>';
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
					<legend><?php echo i18n_r("EasyContactButton/lang_Include_method");?>:</legend>
					<fieldset>
							<label for="buttonType">
								<input type="radio" id="buttonType" name="buttonType" value="type_wa" <?php echo (@$EasyContactButton_data_setting['buttonType'] == 'type_wa' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Include_WhatsApp");?>
							</label>
							<label for="buttonType">
								<input type="radio" id="buttonType" name="buttonType" value="type_contact" <?php echo (@$EasyContactButton_data_setting['buttonType'] == 'type_contact' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Include_Contact");?>
							</label>
					</fieldset>
				</div>
				
				<div class="col-4">
					<legend><?php echo i18n_r("EasyContactButton/lang_Include_Elements");?></legend>
					<fieldset>
						<label for="include_jquery">
							<input type="checkbox" id="include_jquery" name="include_jquery" value="yes" <?php echo (@$EasyContactButton_data_setting['include_jquery'] == 'yes' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Jquery");?>
						</label>
						<label for="include_fa">
							<input type="checkbox" id="include_fa" name="include_fa"  value="yes" <?php echo (@$EasyContactButton_data_setting['include_fa'] == 'yes' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_FontAwesome");?>
						</label>
					</fieldset>
				</div>
				<div class="col-2"></div>
			</div>	
			<hr class="style-three">
			<div class="row">
				<div class="col-1"></div>
				<div class="col-5">
					<legend><?php echo i18n_r("EasyContactButton/lang_Manually_Add");?></legend>
					<label for="include_div_man">
						<input type="checkbox" id="include_div_man" name="include_div_man"  value="yes" <?php echo (@$EasyContactButton_data_setting['include_div_man'] == 'yes' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Am_Professional");?>
					</label>
				</div>
				<div class="col-5 center">
					<legend></legend>
					<div class="code"><span>&#60;div id="whatsapp-us-button"&#62;&#60;/div&#62;</span><br><br> <?php echo i18n_r("EasyContactButton/lang_Or");?> <br><br><span>&#60;div id="contact-us-button"&#62;&#60;/div&#62;</span></div>
				</div>	
			</div>
			
			<hr class="style-three">
			
			<div class="row">
				<div class="col-12 center">
					<p class="small"><a href="#" id="populate-link" style="text-decoration:none;"><img src=" data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAB2AAAAdgFOeyYIAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAUFJREFUOI190z1LXEEUBuBndaNGSKGFpAiCRfQPLG4pKIKFINiki1UWOyvJD0jrX5AEBNnKpAgpIlgoNoJFLOLHkhAMBAmSZQMWSROLO3cZx9m8cJjzMe+58945U3EfdcygEuVusIVOurkv8h/hKXYSMtSwgfGE08VLfMMeLjL1efzAPs4wFRcf4xxDIT7El8Su8DzUl9CMG9TxLvniSGLDUe0JjsqgGtZ/SYN2RkYW1SQeVPzEgSS/jdfB/xss2+AP1vEwyX+N/J+YK4PclZRX2MFxsFjSM3zAWpmo420k4T128SbTvIHPmMYpJnISFjPEkryqkF1TyGynJ+iFRpAyigXFaM/mJOSwEpEn0YrJFGN58J8GLYz1IleCfcSlu+/gu2Jkr7GJZbxQvJcu+sPaxAPF2Jb4jZNgv/AKn9Lj3QKU/EIJlsrdEgAAAABJRU5ErkJggg==" style="vertical-align:middle;"> <?php echo i18n_r("EasyContactButton/lang_Populate_Data");?></a></p>
				</div>
			</div>
			
			<hr class="style-two">
			
			<div class="row">
				<div class="col-12" id="hiddenDivWa">
					<fieldset class="whatsapp-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/whatsapp.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> <?php echo i18n_r("EasyContactButton/lang_WhatsApp");?></legend>
							
							<hr class="style-three">
							
							<p>
								<label for="wa_position"><?php echo i18n_r("EasyContactButton/lang_Button_Position");?>:</label>
								<select class="center-block" id="wa_position" name="wa_position">
									<option value="right" <?php echo (@$EasyContactButton_data_setting['wa_position'] == 'right' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Right");?></option>
									<option value="left" <?php echo (@$EasyContactButton_data_setting['wa_position'] == 'left' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Left");?></option>
								</select>
							</p>
							
							<p>
								<label for="wa_speechBubble"><?php echo i18n_r("EasyContactButton/lang_Mini_Bubble");?>:</label>   
								<input class="center-block" type="text" id="wa_speechBubble" name="wa_speechBubble" value="<?php echo @$EasyContactButton_data_setting['wa_speechBubble']; ?>"> 
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_How_Help");?></span></div> 
							</p>
							
							<p class="center">
								<label for="wa_hide_mobile">
									<input type="checkbox" id="wa_hide_mobile" name="wa_hide_mobile"  value="true" <?php echo (@$EasyContactButton_data_setting['wa_hide_mobile'] == 'true' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Hide_Mobile");?>
								</label> 
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="wa_avatar"><?php echo i18n_r("EasyContactButton/lang_Popup_Avatar");?>:</label>
								<select class="center-block" id="wa_avatar" name="wa_avatar">
									<option value="male.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'male.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Man");?></option>
									<option value="female.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'female.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Woman");?></option>
									<option value="whatsapp.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'whatsapp.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Icon");?></option>
								</select>
							</p>
							
							<p>
								<label for="wa_popup_title"><?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label>   
								<input class="center-block" type="text" id="wa_popup_title" name="wa_popup_title" value="<?php echo @$EasyContactButton_data_setting['wa_popup_title']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Need_Help");?></span></div>
							</p>
							
							<p>
								<label for="wa_popup_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>   
								<input class="center-block" type="text" id="wa_popup_description" name="wa_popup_description" value="<?php echo @$EasyContactButton_data_setting['wa_popup_description']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Customer_Support");?></span></div>
							</p>
							
							<p>
								<label for="wa_popup_message"><?php echo i18n_r("EasyContactButton/lang_Popup_Message");?>:</label>   
								<input class="center-block" type="text" id="wa_popup_message" name="wa_popup_message" value="<?php echo @$EasyContactButton_data_setting['wa_popup_message']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Hi_There");?></span></div>
							</p>
							
							<p>
								<label for="wa_popup_textbox"><?php echo i18n_r("EasyContactButton/lang_Popup_Textbox");?>:</label>   
								<input class="center-block" type="text" id="wa_popup_textbox" name="wa_popup_textbox" value="<?php echo @$EasyContactButton_data_setting['wa_popup_textbox']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Ask_Us");?></span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="wa_phone"><?php echo i18n_r("EasyContactButton/lang_Phone_Number");?>:</label>   
								<input class="center-block" type="text" id="wa_phone" name="wa_phone" value="<?php echo @$EasyContactButton_data_setting['wa_phone']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Including_Country");?> <br><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>123456789</span></div>
							</p>
							
							<div class="row col-10">
								<label for="time"><?php echo i18n_r("EasyContactButton/lang_Availability");?>:</label>
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Monday");?></span>
									<input type="time" name="wa_mon_start" id="wa_mon_start" value="<?php echo @$EasyContactButton_data_setting['wa_mon_start']; ?>"></input>-
									<input type="time" name="wa_mon_end" id="wa_mon_end" value="<?php echo $EasyContactButton_data_setting['wa_mon_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Tuesday");?></span>
									<input type="time" name="wa_tue_start" id="wa_tue_start" value="<?php echo @$EasyContactButton_data_setting['wa_tue_start']; ?>"></input>-
									<input type="time" name="wa_tue_end" id="wa_tue_end" value="<?php echo @$EasyContactButton_data_setting['wa_tue_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Wednesday");?></span>
									<input type="time" name="wa_wed_start" id="wa_wed_start" value="<?php echo @$EasyContactButton_data_setting['wa_wed_start']; ?>"></input>-
									<input type="time" name="wa_wed_end" id="wa_wed_end" value="<?php echo $EasyContactButton_data_setting['wa_wed_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Thursday");?></span>
									<input type="time" name="wa_thu_start" id="wa_thu_start" value="<?php echo @$EasyContactButton_data_setting['wa_thu_start']; ?>"></input>-
									<input type="time" name="wa_thu_end" id="wa_thu_end" value="<?php echo $EasyContactButton_data_setting['wa_thu_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Friday");?></span>
									<input type="time" name="wa_fri_start" id="wa_fri_start" value="<?php echo @$EasyContactButton_data_setting['wa_fri_start']; ?>"></input>-
									<input type="time" name="wa_fri_end" id="wa_fri_end" value="<?php echo $EasyContactButton_data_setting['wa_fri_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Saturday");?></span>
									<input type="time" name="wa_sat_start" id="wa_sat_start" value="<?php echo @$EasyContactButton_data_setting['wa_sat_start']; ?>"></input>-
									<input type="time" name="wa_sat_end" id="wa_sat_end" value="<?php echo $EasyContactButton_data_setting['wa_sat_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Sunday");?></span>
									<input type="time" name="wa_sun_start" id="wa_sun_start" value="<?php echo @$EasyContactButton_data_setting['wa_sun_start']; ?>"></input>-
									<input type="time" name="wa_sun_end" id="wa_sun_end" value="<?php echo $EasyContactButton_data_setting['wa_sun_end']; ?>"></input>
								</div>
							</div>
							
							<div class="col-12"><hr class="style-three"></div>
							
							<p>
								<label for="wa_browser_tab"><?php echo i18n_r("EasyContactButton/lang_Tab_Title");?>:</label>   
								<input class="center-block" type="text" id="wa_browser_tab" name="wa_browser_tab" value="<?php echo @$EasyContactButton_data_setting['wa_browser_tab']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_New_Message");?></span></div>
							</p>
							
						</div>
					</fieldset>
				</div>
				
				<div class="col-12" id="hiddenDivContact">
					
					<fieldset class="contact-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/info.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> <?php echo i18n_r("EasyContactButton/lang_Contact_Info");?></legend>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_button_shape"><?php echo i18n_r("EasyContactButton/lang_Button_Shape");?>:</label>
								<select class="center-block" id="ci_button_shape" name="ci_button_shape">
									<option value="3" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '3' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Round_Rec");?></option>
									<option value="5" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '5' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Rectangle");?></option>
									<option value="1" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '1' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Round");?></option>
								</select>
							</p>
							
							<p>
								<label for="ci_position"><?php echo i18n_r("EasyContactButton/lang_Button_Position");?>:</label>
								<select class="center-block" id="ci_position" name="ci_position">
									<option value="left" <?php echo (@$EasyContactButton_data_setting['ci_position'] == 'left' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Left");?></option>
									<option value="right" <?php echo (@$EasyContactButton_data_setting['ci_position'] == 'right' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Right");?></option>
								</select>
							</p>
							
							<p>
								<label for="ci_button_title"><?php echo i18n_r("EasyContactButton/lang_Button_Title");?>:</label>   
								<input class="center-block" type="text" id="ci_button_title" name="ci_button_title" value="<?php echo @$EasyContactButton_data_setting['ci_button_title']; ?>"> 
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us");?></span></div> 
							</p>
							
							<p>
								<label for="ci_button_color"><?php echo i18n_r("EasyContactButton/lang_Button_Color");?>:</label>
								<input class="center-block" type="color" id="ci_button_color" name="ci_button_color" value="<?php echo @$EasyContactButton_data_setting['ci_button_color']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>#ed2d34</span> = <span>rbg: 237 45 52</span></div>
							</p>
							
							<p>
								<label for="ci_automaticOpen"><?php echo i18n_r("EasyContactButton/lang_Automatic_Open");?>:</label>
								<select class="center-block" id="ci_automaticOpen" name="ci_automaticOpen">
									<option value="true" <?php echo (@$EasyContactButton_data_setting['ci_automaticOpen'] == 'true' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_True");?></option>
									<option value="false" <?php echo (@$EasyContactButton_data_setting['ci_automaticOpen'] == 'false' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_False");?></option>
								</select>
							</p>
							
							<p class="center">
								<label for="ci_hide_mobile">
									<input type="checkbox" id="ci_hide_mobile" name="ci_hide_mobile"  value="true" <?php echo (@$EasyContactButton_data_setting['ci_hide_mobile'] == 'true' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Hide_Mobile");?>
								</label> 
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_popup_title"><?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label>   
								<input class="center-block" type="text" id="ci_popup_title" name="ci_popup_title" value="<?php echo @$EasyContactButton_data_setting['ci_popup_title']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Our_Info");?></span></div>
							</p>
							<p>
								<label for="ci_popup_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>   
								<input class="center-block" type="text" id="ci_popup_description" name="ci_popup_description" value="<?php echo @$EasyContactButton_data_setting['ci_popup_description']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us_247");?></span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_phone_description"><?php echo i18n_r("EasyContactButton/lang_Phone_Description");?>:</label>
								<input class="center-block" type="text" id="ci_phone_description" name="ci_phone_description" value="<?php echo @$EasyContactButton_data_setting['ci_phone_description']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Phone_Number");?></span></div>
							</p>
							
							<p>
								<label for="ci_phone_display"><?php echo i18n_r("EasyContactButton/lang_Phone_Display");?>:</label>   
								<input class="center-block" type="text" id="ci_phone_display" name="ci_phone_display" value="<?php echo @$EasyContactButton_data_setting['ci_phone_display']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>+12 345 6789</span></div>
							</p>
							
							<p>
								<label for="ci_phone_link"><?php echo i18n_r("EasyContactButton/lang_Phone_Link");?>:</label>   
								<input class="center-block" type="text" id="ci_phone_link" name="ci_phone_link" value="<?php echo @$EasyContactButton_data_setting['ci_phone_link']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex_Include_Code");?> <br><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>123456789</span></div>
							</p>
							
							<div class="row col-10">
								<label for="time"><?php echo i18n_r("EasyContactButton/lang_Phone_Avail");?>:</label>
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Monday");?></span>
									<input type="time" name="ci_mon_start" id="ci_mon_start" value="<?php echo @$EasyContactButton_data_setting['ci_mon_start']; ?>"></input>-
									<input type="time" name="ci_mon_end" id="ci_mon_end" value="<?php echo @$EasyContactButton_data_setting['ci_mon_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Tuesday");?></span>
									<input type="time" name="ci_tue_start" id="ci_tue_start" value="<?php echo @$EasyContactButton_data_setting['ci_tue_start']; ?>"></input>-
									<input type="time" name="ci_tue_end" id="ci_tue_end" value="<?php echo @$EasyContactButton_data_setting['ci_tue_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Wednesday");?></span>
									<input type="time" name="ci_wed_start" id="ci_wed_start" value="<?php echo @$EasyContactButton_data_setting['ci_wed_start']; ?>"></input>-
									<input type="time" name="ci_wed_end" id="ci_wed_end" value="<?php echo @$EasyContactButton_data_setting['ci_wed_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Thursday");?></span>
									<input type="time" name="ci_thu_start" id="ci_thu_start" value="<?php echo @$EasyContactButton_data_setting['ci_thu_start']; ?>"></input>-
									<input type="time" name="ci_thu_end" id="ci_thu_end" value="<?php echo @$EasyContactButton_data_setting['ci_thu_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Friday");?></span>
									<input type="time" name="ci_fri_start" id="ci_fri_start" value="<?php echo @$EasyContactButton_data_setting['ci_fri_start']; ?>"></input>-
									<input type="time" name="ci_fri_end" id="ci_fri_end" value="<?php echo @$EasyContactButton_data_setting['ci_fri_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Saturday");?></span>
									<input type="time" name="ci_sat_start" id="ci_sat_start" value="<?php echo @$EasyContactButton_data_setting['ci_sat_start']; ?>"></input>-
									<input type="time" name="ci_sat_end" id="ci_sat_end" value="<?php echo @$EasyContactButton_data_setting['ci_sat_end']; ?>"></input>
								</div>
								
								<div class="col-12 right">
									<span class="smaller"><?php echo i18n_r("EasyContactButton/lang_Sunday");?></span>
									<input type="time" name="ci_sun_start" id="ci_sun_start" value="<?php echo @$EasyContactButton_data_setting['ci_sun_start']; ?>"></input>-
									<input type="time" name="ci_sun_end" id="ci_sun_end" value="<?php echo @$EasyContactButton_data_setting['ci_sun_end']; ?>"></input>
								</div>
							</div>
							
							<div class="col-12"><hr class="style-three"></div>
							
							<p>
								<label for="ci_email_description"><?php echo i18n_r("EasyContactButton/lang_Email_Description");?>:</label>   
								<input class="center-block" type="text" id="ci_email_description" name="ci_email_description" value="<?php echo @$EasyContactButton_data_setting['ci_email_description']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Email_Address");?></span></div>
							</p>
							
							<p>
								<label for="ci_email_display"><?php echo i18n_r("EasyContactButton/lang_Email_Display");?>:</label>   
								<input class="center-block" type="text" id="ci_email_display" name="ci_email_display" value="<?php echo @$EasyContactButton_data_setting['ci_email_display']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Email_Show");?></span></div>
							</p>
							
							<p>
								<label for="ci_email_link"><?php echo i18n_r("EasyContactButton/lang_Email_Link");?>:</label>   
								<input class="center-block" type="text" id="ci_email_link" name="ci_email_link" value="<?php echo @$EasyContactButton_data_setting['ci_email_link']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Email_Real");?></span></div>
							</p>
							
							<hr class="style-three">
							
							<p>
								<label for="ci_address_description"><?php echo i18n_r("EasyContactButton/lang_Address_Description");?>:</label>   
								<input class="center-block" type="text" id="ci_address_description" name="ci_address_description" value="<?php echo @$EasyContactButton_data_setting['ci_address_description']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Office_Address");?></span></div>
							</p>
							
							<p>
								<label for="ci_address_display"><?php echo i18n_r("EasyContactButton/lang_Address");?>:</label>   
								<input class="center-block" type="text" id="ci_address_display" name="ci_address_display" value="<?php echo @$EasyContactButton_data_setting['ci_address_display']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_Address");?></span></div>
							</p>
							
							<p>
								<label for="ci_address_link"><?php echo i18n_r("EasyContactButton/lang_Google_Link");?>:</label>   
								<input class="center-block" type="text" id="ci_address_link" name="ci_address_link" value="<?php echo @$EasyContactButton_data_setting['ci_address_link']; ?>">
								<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>https://www.google.com/maps/@40.7558962,-73.9889626,16z</span></div>
							</p>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		
		<div class="col-12 center">
			<input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" />
		</div>
		
		<hr class="style-one" style="margin:30px 0;">
		<div class="row">
			<div class="col-6 center">
				<p class="small" style="margin-bottom:0;">Created by: <img src=" data:image/gif;base64,R0lGODlhIAAgAKIHAGBgYO/v7yAgIL+/v39/fxAQEAAAAP///yH5BAEAAAcALAAAAAAgACAAAAP/eHox+zDKFUARhsy9BQiMoHEL4EiD8KTkYhQnNACQsQG4k9EBASqBwu/AmlgIHkNAiYkdAALQAMaBBjAHg0fg7BW0zsiAcBkYzCYVqbhIiZAAQo46kX4ZA8eFwO8/tX1DClMzBBkvBQd9fAAGZ4sFQjKRXIB0FAWNmlAmEUGGoAZxgjgWMzBhD1B9b6aDXDAwVhJfjme2tjQHInFEmRcoBYyNZzJRSkB+Ej0CwqLIJYwtD2RTZDgPSr3TRFpUcVyvu6kceT9ycgqjNtzLtyAmM+1GKTo9uvMQi0GKI/kreQY0UESO2z0Mtjz4+JciUx6AFsJNc1OQmgCJG6zNIzQhNU+ngUAEhhySZ8RDIlBgVNjjxYOiCyrcKJnRLI8wIhh8TMkUIIgHB1DCpfQnY0hPCk6SCkoAADs="> islander</p><p class="smaller" style="margin-bottom:0;">Special thanks to: multicolor</p>
			</div>
			<div class="col-6 center">
				<p><a href="https://www.paypal.com/donate/?hosted_button_id=C3FTNQ78HH8BE" target="_blank"><img src="        data:image/gif;base64,R0lGODlhyAAgAMQfAMnJzK6urpOTk/FCNHNycvWOAQep8/b291xcXJfb+qGn0FkoF5IfHOXl5sAlIS0ZE2FssalhBdjZ3WPK+DdJn8br/XJCF+rs8Sg1k+f3/m1iXxF/0TK59oWEg0ZGRv///yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDUgNzkuMTYzNDk5LCAyMDE4LzA4LzEzLTE2OjQwOjIyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDQ5MzZGNjhBNkZDMTFFREJDRjZEMjRCMDFDRTdFRUIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDQ5MzZGNjlBNkZDMTFFREJDRjZEMjRCMDFDRTdFRUIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo0NDkzNkY2NkE2RkMxMUVEQkNGNkQyNEIwMUNFN0VFQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo0NDkzNkY2N0E2RkMxMUVEQkNGNkQyNEIwMUNFN0VFQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAB8ALAAAAADIACAAAAX/4CeOZGmeaKqubOu+cCzPdG3feK7vfO//wKBwSCwaj8jkJ4AgEQIugodgCnhWAmori7J6EADlRUEuKxo/wUNzODKdUFbDE65eVVzbHCpAtJEKGIKDggo+CwURd0VvI09LHl8SHw1SHlCRlwhxm1YkVlIIk1ybIqVeUh+kBAhTS02UdEkQhIQUfy4SAWgpHRYFC3VEjSJPB5cfHVQEBBcSm3NhpR+di5ACqk2knAEAdAeppAgNErIj3rhGtLWDFy8aDwtrAPLNJAcI8rzDsMUBx48+HKsjoEM0atw8jbDSZo6EbaYCFBThTRWVadMgxTlygB0hAwm2LHDAwIG8kQwe/2AjIWEfPzgfAFgSMCfTlIMYAygUQYwORIQEVlb8Oc2bsCMSPAqiYIBDiwcmPTyIF6/kAg/uSKhZMEkIuhEZVXko11UEzoTWGMZ6eDHOpYkxrxCNQwyQUgwbDBhIhwLqg2QLBjyIYAFqyrIfPCR6kBXIsZVWGuyJC47AgQYIaMoioEHjTo3ZLH7grNHoAQ1y20ZUUiKQR6ZNnzp4IOHBgAEmE6F8ICzArwhagsiMBIZnJiiVIlE5+GyKBp1pp0wZRaU5MyheNGhTjZBnPyTrasE2MKEFAQYpA99eEAGYg/e8RRx4UKDAX9Y7AnSOoQx/CwqDULBBXnrpVUEL86V02/+C9FlQEnx1IGKffzrUpQIArdxEoQoXEEJggSC9MIdtC+L2gIPvkfQAORLet+GLMJ7joV4J1JhBDAGoV+JsD76XkgftTRjjkBu6Johe5dEgAAM7lpRSj/IECQyRVOIHAARYEhiSkkzeFo886E0VT5DtAZefTQS4VCVFkTDhwUowTlDggTQE0OWJhE1lopTsAYPYDY2c1sGaJXDxWZwcJMoXDPTgtkBhVC34aJSJWIMDMQFQkUcWGIK1URatENBcHB0oV0JykhQayUqlTnGAFxl60AAqvLQanA8H5KrCARksuug9tjFwkgUL6DhASkGuyEOgGoSxKRWylIOLAGPN0Qz/Q0xIAA6couEz6Dl0GNVHA+AMaugV3vDRRLbb/nAAgBSoSQIHBpKQgQFYjDQVsSSWOIA88S1rEzLPqoJNHiLkUYo3lUD2nVa39kFCUR4ccK5oawXl3Q8S3ALBUfYakMEEW4qQgFMreGDSVMaWWKy8l/ZDrcVacPFMdyMoDAXDsUZSaB+u5HwrMnE1cDG1NmE48A+BUECBAk5TIAEEFiuQQIEnJ1oBySxc4EGX/t42mwC/xgyu0Vr0N9rMEK/GcEAmMCMAAGon/B3FNEOiyreObAQEBApo+3QgB2BJwQUTTFDBARzUKDIHdK5wGlRNrgFzhTJrM84zWljBd8JaLCxrhLaj3ZoZJLcahZlE45ymKRWepAvJq6KU/oPUIhhOwQeBTAK5CBMkKvJeL0hAgJgPvNkYEF4oNwk4X6hdjt8Yi46GrS6hIrFWpiajXBsXQ/JFHdivzUNZBwAQ+AUU1LG4fBUksHjkhOLoR/0wpo+/D62AvP//AAygAAdIwAIa8IAIlEEIAAA7" title="Donate?" alt="Donate?"></a></p>
			</div>
		</div>
	</form>
	
	<script>
	  $(document).ready(function() {
		// Check if a value is stored in localStorage
		if (localStorage.getItem("selectedRadioButton") !== null) {
		  // If a value is stored, set the selected radio button to the stored value
		  $("input[name='buttonType'][value='" + localStorage.getItem("selectedRadioButton") + "']").prop("checked", true);
		}

		// Show/hide the corresponding <div> based on the selected radio button
		if ($("input[name='buttonType']:checked").val() == "type_wa") {
		  $("#hiddenDivWa").show();
		  $("#hiddenDivContact").hide();
		} else if ($("input[name='buttonType']:checked").val() == "type_contact") {
		  $("#hiddenDivContact").show();
		  $("#hiddenDivWa").hide();
		}

		$("input[name='buttonType']").change(function() {
		  if ($("input[name='buttonType']:checked").val() == "type_wa") {
			$("#hiddenDivWa").show();
			$("#hiddenDivContact").hide();
		  } else if ($("input[name='buttonType']:checked").val() == "type_contact") {
			$("#hiddenDivContact").show();
			$("#hiddenDivWa").hide();
		  }

		  // Store the selected radio button value in localStorage
		  localStorage.setItem("selectedRadioButton", $("input[name='buttonType']:checked").val());
		});
	  });
	</script>
	
	<script>
	  const populateLink = document.getElementById("populate-link");
	  
	  const wa_speechBubble_Field 	= document.getElementById("wa_speechBubble");
	  const wa_popup_title_Field 	= document.getElementById("wa_popup_title");
	  const wa_popup_description_Field = document.getElementById("wa_popup_description");
	  const wa_popup_message_Field 	= document.getElementById("wa_popup_message");
	  const wa_popup_textbox_Field 	= document.getElementById("wa_popup_textbox");
	  const wa_phone_Field 			= document.getElementById("wa_phone");
	  
	  const wa_mon_start_Field 	= document.getElementById("wa_mon_start");
	  const wa_mon_end_Field 	= document.getElementById("wa_mon_end");
	  const wa_tue_start_Field 	= document.getElementById("wa_tue_start");
	  const wa_tue_end_Field 	= document.getElementById("wa_tue_end");
	  const wa_wed_start_Field 	= document.getElementById("wa_wed_start");
	  const wa_wed_end_Field 	= document.getElementById("wa_wed_end");
	  const wa_thu_start_Field 	= document.getElementById("wa_thu_start");
	  const wa_thu_end_Field 	= document.getElementById("wa_thu_end");
	  const wa_fri_start_Field 	= document.getElementById("wa_fri_start");
	  const wa_fri_end_Field 	= document.getElementById("wa_fri_end");
	  const wa_sat_start_Field 	= document.getElementById("wa_sat_start");
	  const wa_sat_end_Field 	= document.getElementById("wa_sat_end");
	  const wa_sun_start_Field 	= document.getElementById("wa_sun_start");
	  const wa_sun_end_Field 	= document.getElementById("wa_sun_end");
	  
	  const wa_browser_tab_Field 	= document.getElementById("wa_browser_tab");
	  
	  const ci_button_title_Field 	= document.getElementById("ci_button_title");
	  const ci_button_color_Field 	= document.getElementById("ci_button_color");
	  const ci_popup_title_Field 	= document.getElementById("ci_popup_title");
	  const ci_popup_description_Field = document.getElementById("ci_popup_description");
	  const ci_phone_description_Field = document.getElementById("ci_phone_description");
	  const ci_phone_display_Field 	= document.getElementById("ci_phone_display");
	  const ci_phone_link_Field 	= document.getElementById("ci_phone_link");
	  
	  const ci_mon_start_Field 	= document.getElementById("ci_mon_start");
	  const ci_mon_end_Field 	= document.getElementById("ci_mon_end");
	  const ci_tue_start_Field 	= document.getElementById("ci_tue_start");
	  const ci_tue_end_Field 	= document.getElementById("ci_tue_end");
	  const ci_wed_start_Field 	= document.getElementById("ci_wed_start");
	  const ci_wed_end_Field 	= document.getElementById("ci_wed_end");
	  const ci_thu_start_Field 	= document.getElementById("ci_thu_start");
	  const ci_thu_end_Field 	= document.getElementById("ci_thu_end");
	  const ci_fri_start_Field 	= document.getElementById("ci_fri_start");
	  const ci_fri_end_Field 	= document.getElementById("ci_fri_end");
	  const ci_sat_start_Field 	= document.getElementById("ci_sat_start");
	  const ci_sat_end_Field 	= document.getElementById("ci_sat_end");
	  const ci_sun_start_Field 	= document.getElementById("ci_sun_start");
	  const ci_sun_end_Field 	= document.getElementById("ci_sun_end");
	  
	  const ci_email_description_Field 	= document.getElementById("ci_email_description");
	  const ci_email_display_Field 		= document.getElementById("ci_email_display");
	  const ci_email_link_Field 		= document.getElementById("ci_email_link");
	  const ci_address_description_Field = document.getElementById("ci_address_description");
	  const ci_address_display_Field 	= document.getElementById("ci_address_display");
	  const ci_address_link_Field 		= document.getElementById("ci_address_link");

	  populateLink.addEventListener("click", function(event) {
		event.preventDefault();
		
		wa_speechBubble_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_How_Help");?>";
		wa_popup_title_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Need_Help");?>";
		wa_popup_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Customer_Support");?>";
		wa_popup_message_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Hi_There_Data");?>";
		wa_popup_textbox_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Ask_Us");?>";
		wa_phone_Field.value 		= "0123456789";
		
		wa_mon_start_Field.value 	= "08:30";
		wa_mon_end_Field.value 		= "17:30";
		wa_tue_start_Field.value 	= "08:00";
		wa_tue_end_Field.value 		= "17:00";
		wa_wed_start_Field.value 	= "08:00";
		wa_wed_end_Field.value 		= "17:00";
		wa_thu_start_Field.value 	= "08:00";
		wa_thu_end_Field.value 		= "17:00";
		wa_fri_start_Field.value 	= "09:00";
		wa_fri_end_Field.value 		= "15:00";
		wa_sat_start_Field.value 	= "00:00";
		wa_sat_end_Field.value 		= "00:00";
		wa_sun_start_Field.value 	= "00:00";
		wa_sun_end_Field.value 		= "00:00";
		
		wa_browser_tab_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_New_Message");?>";
		
		ci_button_title_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us");?>";
		ci_button_color_Field.value = "#ed2d34";
		ci_popup_title_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Our_Info");?>";
		ci_popup_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us_247");?>";
		ci_phone_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Phone_Number");?>";
		ci_phone_display_Field.value = "+12 345 6789";
		ci_phone_link_Field.value 	= "123456789";
		
		ci_mon_start_Field.value 	= "08:30";
		ci_mon_end_Field.value 		= "17:30";
		ci_tue_start_Field.value 	= "08:00";
		ci_tue_end_Field.value 		= "17:00";
		ci_wed_start_Field.value 	= "08:00";
		ci_wed_end_Field.value 		= "17:00";
		ci_thu_start_Field.value 	= "08:00";
		ci_thu_end_Field.value 		= "17:00";
		ci_fri_start_Field.value 	= "09:00";
		ci_fri_end_Field.value 		= "15:00";
		ci_sat_start_Field.value 	= "00:00";
		ci_sat_end_Field.value 		= "00:00";
		ci_sun_start_Field.value 	= "00:00";
		ci_sun_end_Field.value 		= "00:00";
		
		ci_email_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Address");?>";
		ci_email_display_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Show");?>";
		ci_email_link_Field.value 		= "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Real");?>";
		ci_address_description_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Office_Address");?>";
		ci_address_display_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Address");?>";
		ci_address_link_Field.value 	= "https://www.google.com/maps/@40.7558962,-73.9889626,16z";
		
	  });
	</script>
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
		$EasyContactButton_data_setting['wa_hide_mobile'] 	= $data->wa_hide_mobile;
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
		$EasyContactButton_data_setting['ci_button_shape'] 	= $data->ci_button_shape;
		$EasyContactButton_data_setting['ci_automaticOpen'] = $data->ci_automaticOpen;
		$EasyContactButton_data_setting['ci_hide_mobile'] 	= $data->ci_hide_mobile;
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
	$xml->addChild('wa_hide_mobile', $settings['wa_hide_mobile']);
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
	$xml->addChild('ci_button_shape', $settings['ci_button_shape']);
	$xml->addChild('ci_automaticOpen', $settings['ci_automaticOpen']);
	$xml->addChild('ci_hide_mobile', $settings['ci_hide_mobile']);
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