<?php

# get correct id for plugin
$EasyContactButton = basename(__FILE__, ".php");

$EasyContactButton_data = GSDATAOTHERPATH . 'EasyContactButton-settings.xml';
$site_url = $SITEURL;
$plugin_folder = basename(GSPLUGINPATH);
$plugin_url = $site_url . $plugin_folder;

# add in this plugin's language file
i18n_merge($EasyContactButton) || i18n_merge($EasyContactButton, 'en_US');

# register plugin
register_plugin(
	$EasyContactButton,								# ID of plugin, should be filename minus php
	i18n_r($EasyContactButton.'/lang_Menu_Title'), 	# Title of plugin
	'1.8',											# Version of plugin
	'risingisland',									# Author of plugin
	'https://www.paypal.com/donate/?hosted_button_id=QTTWMSSQDYB82',	# Author URL
	i18n_r($EasyContactButton.'/lang_Description'), # Plugin Description
	'plugins',										# Page type of plugin
	'easy_button_settings'							# Function that displays content
);

# Direct AJAX intercept before admin template renders
if (isset($_GET['id']) && $_GET['id'] === $EasyContactButton && (isset($_GET['ecb_action']) || isset($_POST['ecb_action']))) {
	easy_button_ajax_handler();
}

function easy_button_ajax_handler() {
	if (function_exists('cookie_check')) {
		cookie_check();
	}

	while (ob_get_level()) {
		ob_end_clean();
	}

	$action = $_POST['ecb_action'] ?? $_GET['ecb_action'] ?? '';

	if ($action === 'list_uploads') {
		header('Content-Type: application/json; charset=UTF-8');
		global $SITEURL;

		$sub = trim(str_replace(['..', '\\'], ['', '/'], $_GET['folder'] ?? ''), '/');
		$baseDir = rtrim(GSDATAUPLOADPATH, '/') . '/';
		$targetDir = $sub !== '' ? $baseDir . $sub . '/' : $baseDir;

		if (!file_exists($targetDir) || !is_dir($targetDir)) {
			echo json_encode(['folders' => [], 'images' => []]);
			exit;
		}

		$folders = [];
		$images = [];
		$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

		$items = @scandir($targetDir);
		if ($items) {
			foreach ($items as $item) {
				if ($item === '.' || $item === '..' || $item === '.htaccess') continue;
				$full = $targetDir . $item;
				if (is_dir($full)) {
					$rel = $sub !== '' ? $sub . '/' . $item : $item;
					$folders[] = ['name' => $item, 'path' => $rel];
				} else {
					$ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
					if (in_array($ext, $allowedExts, true)) {
						$rel = $sub !== '' ? $sub . '/' . $item : $item;
						$imgUrl = $SITEURL . 'data/uploads/' . $rel;
						$thumbUrl = $imgUrl;

						$thumbRel = ($sub !== '' ? $sub . '/' : '') . 'thumbsm.' . $item;
						if (file_exists(GSTHUMBNAILPATH . $thumbRel)) {
							$thumbUrl = $SITEURL . 'data/thumbs/' . $thumbRel;
						}

						$images[] = [
							'filename' => $rel,
							'name'     => $item,
							'url'      => $imgUrl,
							'thumb'    => $thumbUrl
						];
					}
				}
			}
		}

		echo json_encode(['folders' => $folders, 'images' => $images]);
		exit;
	}

	if ($action === 'upload_image') {
		header('Content-Type: application/json; charset=UTF-8');
		global $SITEURL;

		$sub = trim(str_replace(['..', '\\'], ['', '/'], $_POST['folder'] ?? ''), '/');
		$baseDir = rtrim(GSDATAUPLOADPATH, '/') . '/';
		$targetDir = $sub !== '' ? $baseDir . $sub . '/' : $baseDir;

		if (!file_exists($targetDir) || !is_dir($targetDir)) {
			echo json_encode(['ok' => false, 'msg' => 'Invalid directory target.']);
			exit;
		}

		if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			echo json_encode(['ok' => false, 'msg' => 'Upload error.']);
			exit;
		}

		$origName = $_FILES['image']['name'];
		$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
		$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
		if (!in_array($ext, $allowedExts, true)) {
			echo json_encode(['ok' => false, 'msg' => 'Unsupported image type.']);
			exit;
		}

		$base = preg_replace('/[^A-Za-z0-9_\-\.]+/', '-', pathinfo($origName, PATHINFO_FILENAME));
		$base = trim($base, " -_.");
		if ($base === '') $base = 'image';
		$safeName = $base . '.' . $ext;

		$i = 1;
		while (file_exists($targetDir . $safeName)) {
			$safeName = $base . '-' . $i . '.' . $ext;
			$i++;
		}

		if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $safeName)) {
			echo json_encode(['ok' => false, 'msg' => 'Failed to save uploaded file.']);
			exit;
		}

		@chmod($targetDir . $safeName, 0644);
		$rel = $sub !== '' ? $sub . '/' . $safeName : $safeName;
		$imgUrl = $SITEURL . 'data/uploads/' . $rel;

		echo json_encode([
			'ok' => true,
			'image' => [
				'filename' => $rel,
				'name'     => $safeName,
				'url'      => $imgUrl,
				'thumb'    => $imgUrl
			]
		]);
		exit;
	}
}

# Front-End Hooks
add_action('theme-header','easy_button_css');
add_action('theme-footer','easy_button_js');

# Back-End Hooks
add_action('plugins-sidebar','createSideMenu', array($EasyContactButton, i18n_r($EasyContactButton.'/lang_Menu_Title')));

# ===== functions Header =====
function easy_button_css() {
	global $EasyContactButton_data;
	global $SITEURL;
	$file = $EasyContactButton_data;
	$s = getXML($file);
	
	echo '	<link href="'.$SITEURL.'plugins/EasyContactButton/assets/css/easy-chat-support.min.css" rel="stylesheet">' . PHP_EOL;
		
	if ($s->wa_hide_mobile == 'yes') {
		echo '	<style>@media(max-width: 768px){#whatsapp-us-button{display:none;}}</style>' . PHP_EOL;
	}
	if ($s->ci_hide_mobile == 'yes') {
		echo '	<style>@media(max-width: 768px){#contact-us-button{display:none;}}</style>' . PHP_EOL;
	}
	if (isset($s->ma_hide_mobile) && $s->ma_hide_mobile == 'yes') {
		echo '	<style>@media(max-width: 768px){#multi-account-button{display:none;}}</style>' . PHP_EOL;
	}
}

# ===== functions Content =====

	global $EasyContactButton_data;
	$file = $EasyContactButton_data;
	$s = getXML($file);
	
	if (@$s->include_div_man == 'yes') {
		add_action('content-bottom','easy_button_div_own');

		function easy_button_div_own() {
			echo '';
		};		
	} else {
		add_action('content-bottom','easy_button_div_wa');

		function easy_button_div_wa() {
			global $EasyContactButton_data;
			$file = $EasyContactButton_data;
			$s = getXML($file);
			
			if ($s->buttonType == 'type_wa') {
				echo '<div id="whatsapp-us-button"></div>' . PHP_EOL;
			}
		}

		add_action('content-bottom','easy_button_div_multi');

		function easy_button_div_multi() {
			global $EasyContactButton_data;
			$file = $EasyContactButton_data;
			$s = getXML($file);
			
			if ($s->buttonType == 'type_multi') {
				echo '<div id="multi-account-button"></div>' . PHP_EOL;
			}
		}

		add_action('content-bottom','easy_button_div_contact');

		function easy_button_div_contact() {
			global $EasyContactButton_data;
			$file = $EasyContactButton_data;
			$s = getXML($file); 
			
			if ($s->buttonType == 'type_contact') {
				echo '<div id="contact-us-button"></div>' . PHP_EOL;
			}
		}
	}

# ===== functions Footer =====

function easy_button_js() {
	global $SITEURL;
	$file = GSDATAOTHERPATH . 'EasyContactButton-settings.xml'; 
	if (file_exists($file)) {
		$s = getXML($file);  
		
		$buttonType 		= (string)$s->buttonType;
		$include_div_man 	= (string)$s->include_div_man;
		
		// WA variables
		$wa_position 		= (string)$s->wa_position;
		$wa_speechBubble 	= trim((string)$s->wa_speechBubble);
		$wa_notification 	= isset($s->wa_notification) && (string)$s->wa_notification === '1';
		$wa_hide_mobile 	= (string)$s->wa_hide_mobile;
		$wa_avatar 			= isset($s->wa_avatar) ? trim((string)$s->wa_avatar) : '';
		$wa_custom_avatar 	= isset($s->wa_custom_avatar) ? trim((string)$s->wa_custom_avatar) : '';
		$wa_popup_title 	= (string)$s->wa_popup_title;
		$wa_popup_description = (string)$s->wa_popup_description;
		$wa_popup_message 	= (string)$s->wa_popup_message;
		$wa_popup_textbox 	= (string)$s->wa_popup_textbox;
		$wa_phone 			= (string)$s->wa_phone;
		$wa_sound 			= (string)$s->wa_sound;
		$wa_mon_start 		= (string)$s->wa_mon_start;
		$wa_mon_end 		= (string)$s->wa_mon_end;
		$wa_tue_start 		= (string)$s->wa_tue_start;
		$wa_tue_end			= (string)$s->wa_tue_end;
		$wa_wed_start 		= (string)$s->wa_wed_start;
		$wa_wed_end			= (string)$s->wa_wed_end;
		$wa_thu_start 		= (string)$s->wa_thu_start;
		$wa_thu_end 		= (string)$s->wa_thu_end;
		$wa_fri_start 		= (string)$s->wa_fri_start;
		$wa_fri_end 		= (string)$s->wa_fri_end;
		$wa_sat_start 		= (string)$s->wa_sat_start;
		$wa_sat_end 		= (string)$s->wa_sat_end;
		$wa_sun_start 		= (string)$s->wa_sun_start;
		$wa_sun_end 		= (string)$s->wa_sun_end;
		$wa_browser_tab 	= (string)$s->wa_browser_tab;

		// Multi variables ($ma_)
		$ma_position        = isset($s->ma_position) ? (string)$s->ma_position : 'right';
		$ma_speechBubble    = isset($s->ma_speechBubble) ? trim((string)$s->ma_speechBubble) : '';
		$ma_notification    = isset($s->ma_notification) && (string)$s->ma_notification === '1';
		$ma_popup_title     = isset($s->ma_popup_title) ? (string)$s->ma_popup_title : '';
		$ma_popup_description = isset($s->ma_popup_description) ? (string)$s->ma_popup_description : '';
		$ma_sound           = isset($s->ma_sound) ? (string)$s->ma_sound : 'true';
		$ma_browser_tab     = isset($s->ma_browser_tab) ? (string)$s->ma_browser_tab : '';
		$ma_persons_json    = isset($s->ma_persons_json) ? (string)$s->ma_persons_json : '[]';
		
		// Contact variables ($ci_)
		$ci_position 		= (string)$s->ci_position;
		$ci_button_title 	= (string)$s->ci_button_title;
		$ci_button_color 	= (string)$s->ci_button_color;
		$ci_button_shape 	= (string)$s->ci_button_shape;
		$ci_automaticOpen 	= (string)$s->ci_automaticOpen;
		$ci_hide_mobile 	= (string)$s->ci_hide_mobile;
		$ci_popup_title 	= (string)$s->ci_popup_title;
		$ci_popup_description = (string)$s->ci_popup_description;
		$ci_phone_description = (string)$s->ci_phone_description;
		$ci_phone_display 	= (string)$s->ci_phone_display;
		$ci_phone_link 		= (string)$s->ci_phone_link;
		$ci_wa_description 	= isset($s->ci_wa_description) ? (string)$s->ci_wa_description : '';
		$ci_wa_display 		= isset($s->ci_wa_display) ? (string)$s->ci_wa_display : '';
		$ci_wa_link 		= isset($s->ci_wa_link) ? (string)$s->ci_wa_link : '';
		$ci_wa_avatar 		= isset($s->ci_wa_avatar) ? trim((string)$s->ci_wa_avatar) : '';
		$ci_wa_custom_avatar = isset($s->ci_wa_custom_avatar) ? trim((string)$s->ci_wa_custom_avatar) : '';
		$ci_sound 			= (string)$s->ci_sound;
		$ci_mon_start 		= (string)$s->ci_mon_start;
		$ci_mon_end 		= (string)$s->ci_mon_end;
		$ci_tue_start 		= (string)$s->ci_tue_start;
		$ci_tue_end			= (string)$s->ci_tue_end;
		$ci_wed_start 		= (string)$s->ci_wed_start;
		$ci_wed_end			= (string)$s->ci_wed_end;
		$ci_thu_start 		= (string)$s->ci_thu_start;
		$ci_thu_end 		= (string)$s->ci_thu_end;
		$ci_fri_start 		= (string)$s->ci_fri_start;
		$ci_fri_end 		= (string)$s->ci_fri_end;
		$ci_sat_start 		= (string)$s->ci_sat_start;
		$ci_sat_end 		= (string)$s->ci_sat_end;
		$ci_sun_start 		= (string)$s->ci_sun_start;
		$ci_sun_end 		= (string)$s->ci_sun_end;
		$ci_email_description = (string)$s->ci_email_description;
		$ci_email_display 	= (string)$s->ci_email_display;
		$ci_email_link 		= (string)$s->ci_email_link;
		$ci_address_description = (string)$s->ci_address_description;
		$ci_address_display = (string)$s->ci_address_display;
		$ci_address_link 	= (string)$s->ci_address_link;
		$ci_browser_tab 	= (string)$s->ci_browser_tab;
		
		if ($s->buttonType == 'type_wa') {
			$wa_avatar_image_src = ($wa_avatar === 'custom' && !empty($wa_custom_avatar))
				? $SITEURL . 'data/uploads/' . ltrim($wa_custom_avatar, '/')
				: $SITEURL . 'plugins/EasyContactButton/assets/img/' . ($wa_avatar ?: 'whatsapp.svg');

			$wa_notification_val = $wa_notification ? '"1"' : 'false';
			$wa_browser_title_val = trim($wa_browser_tab) !== '' ? '"' . addslashes($wa_browser_tab) . '"' : 'false';
			$wa_speech_bubble_val = $wa_speechBubble !== '' ? '"' . addslashes($wa_speechBubble) . '"' : 'false';

			echo '
			<script src="'.$SITEURL.'plugins/EasyContactButton/assets/js/easy-chat-support.min.js"></script>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					easyChatSupport("#whatsapp-us-button", {
						sound: ' . $wa_sound . ',
						button: {
							position: "' . $wa_position . '",
							speechBubble: ' . $wa_speech_bubble_val . ',
							notificationNumber: ' . $wa_notification_val . ',
						},
						popup: {
							effect: 15,
							persons: [{
								avatar: {
									src: \'<img src="'.$wa_avatar_image_src.'" alt="">\', 
									backgroundColor: "#ffffff",
									onlineCircle: true
								},
								text: {
									title: "' . addslashes($wa_popup_title) . '",
									description: "' . addslashes($wa_popup_description) . '",
									message: "' . addslashes($wa_popup_message) . '",
									textbox: "' . addslashes($wa_popup_textbox) . '",
									button: false
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
						changeBrowserTitle: ' . $wa_browser_title_val . ',
					});
				});
			</script>';
		}

		if ($s->buttonType == 'type_multi') {
			$ma_persons = json_decode($ma_persons_json, true) ?: [];
			$js_persons = [];

			foreach ($ma_persons as $p) {
				$p_avatar = trim($p['avatar'] ?? 'whatsapp.svg');
				$p_custom = trim($p['custom_avatar'] ?? '');
				$avatar_src = ($p_avatar === 'custom' && !empty($p_custom))
					? $SITEURL . 'data/uploads/' . ltrim($p_custom, '/')
					: $SITEURL . 'plugins/EasyContactButton/assets/img/' . ($p_avatar ?: 'whatsapp.svg');

				$phone = preg_replace('/[^0-9]/', '', $p['phone'] ?? '');

				$js_persons[] = [
					'avatar' => [
						'src' => '<img src="' . $avatar_src . '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">',
						'backgroundColor' => '#ffffff',
						'onlineCircle' => true
					],
					'text' => [
						'title' => $p['title'] ?? '',
						'description' => $p['description'] ?? '',
						'online' => "I'm Online",
						'offline' => "I will be back soon"
					],
					'link' => [
						'desktop' => 'https://web.whatsapp.com/send?phone=' . $phone . '&text=Hi',
						'mobile'  => 'https://wa.me/' . $phone . '/?text=Hi'
					],
					'onlineDay' => [
						'sunday'    => !empty($p['sun_start']) ? ($p['sun_start'] . '-' . $p['sun_end']) : false,
						'monday'    => !empty($p['mon_start']) ? ($p['mon_start'] . '-' . $p['mon_end']) : false,
						'tuesday'   => !empty($p['tue_start']) ? ($p['tue_start'] . '-' . $p['tue_end']) : false,
						'wednesday' => !empty($p['wed_start']) ? ($p['wed_start'] . '-' . $p['wed_end']) : false,
						'thursday'  => !empty($p['thu_start']) ? ($p['thu_start'] . '-' . $p['thu_end']) : false,
						'friday'    => !empty($p['fri_start']) ? ($p['fri_start'] . '-' . $p['fri_end']) : false,
						'saturday'  => !empty($p['sat_start']) ? ($p['sat_start'] . '-' . $p['sat_end']) : false
					]
				];
			}

			$ma_notification_val = $ma_notification ? '"1"' : 'false';
			$ma_browser_title_val = trim($ma_browser_tab) !== '' ? '"' . addslashes($ma_browser_tab) . '"' : 'false';
			$ma_speech_bubble_val = $ma_speechBubble !== '' ? '"' . addslashes($ma_speechBubble) . '"' : 'false';
			$json_persons_out = json_encode($js_persons, JSON_UNESCAPED_SLASHES);

			echo '
			<script src="'.$SITEURL.'plugins/EasyContactButton/assets/js/easy-chat-support.min.js"></script>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					easyChatSupport("#multi-account-button", {
						sound: ' . $ma_sound . ',
						button: {
							position: "' . $ma_position . '",
							style: 1,
							src: \'<img src="'.$SITEURL.'plugins/EasyContactButton/assets/img/whatsapp.svg" alt="" style="width:34px;height:34px;display:inline-block;vertical-align:middle;">\',
							backgroundColor: "#26c281",
							speechBubble: ' . $ma_speech_bubble_val . ',
							notificationNumber: ' . $ma_notification_val . ',
							pulseEffect: true,
						},
						popup: {
							effect: 1,
							header: {
								backgroundColor: "#26c281",
								title: "' . addslashes($ma_popup_title) . '",
								description: "' . addslashes($ma_popup_description) . '"
							},
							persons: ' . $json_persons_out . '
						},
						changeBrowserTitle: ' . $ma_browser_title_val . ',
					});
				});
			</script>';
		}
		
		if ($s->buttonType == 'type_contact') {
			$svgInfoCircle = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" y1=\"16\" x2=\"12\" y2=\"12\"/><line x1=\"12\" y1=\"8\" x2=\"12.01\" y2=\"8\"/></svg>';
			$svgPhoneAlt   = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"22\" height=\"22\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z\"/></svg>';
			$svgEnvelope   = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"22\" height=\"22\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z\"/><polyline points=\"22,6 12,13 2,6\"/></svg>';
			$svgMapMarker  = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"22\" height=\"22\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z\"/><circle cx=\"12\" cy=\"10\" r=\"3\"/></svg>';

			$ci_wa_avatar_image_src = ($ci_wa_avatar === 'custom' && !empty($ci_wa_custom_avatar))
				? $SITEURL . 'data/uploads/' . ltrim($ci_wa_custom_avatar, '/')
				: $SITEURL . 'plugins/EasyContactButton/assets/img/' . ($ci_wa_avatar ?: 'whatsapp.svg');

			echo '
			<script src="'.$SITEURL.'plugins/EasyContactButton/assets/js/easy-chat-support.min.js"></script>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					easyChatSupport("#contact-us-button", {
						sound: ' . $ci_sound . ',
						button: {
							position: "' . $ci_position . '",
							style: ' .$ci_button_shape. ',
							src: \'' . $svgInfoCircle . '\',
							backgroundColor: "' . $ci_button_color . '",
							notificationNumber: false,
							speechBubble: false,
							pulseEffect: true,
							text: {
								title: "' . addslashes($ci_button_title) . '",
							}
						},
						popup: {
							automaticOpen: ' . $ci_automaticOpen . ',
							effect: 15,
							header: {
								backgroundColor: "' . $ci_button_color . '",
								title: "' . addslashes($ci_popup_title) . '",
								description: "' . addslashes($ci_popup_description) . '"
							},
							persons: [{
								avatar: {
									src: \'' . $svgPhoneAlt . '\',
									backgroundColor: "' . $ci_button_color . '",
									onlineCircle: true
								},
								text: {
									title: "' . addslashes($ci_phone_display) . '",
									description: "' . addslashes($ci_phone_description) . '",
									online: false,
									offline: false
								},
								link: {
									desktop: "tel:' . $ci_phone_link . '",
									mobile: false
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
							},
							'; if (!empty($s->ci_wa_description) || !empty($s->ci_wa_display)) { echo '
							{
								avatar: {
									src: \'<img src="'.$ci_wa_avatar_image_src.'" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">\',
									backgroundColor: "#ffffff",
									onlineCircle: true
								},
								text: {
									title: "' . addslashes($ci_wa_display) . '",
									description: "' . addslashes($ci_wa_description) . '",
									online: false,
									offline: false
								},
								link: {
									desktop: "https://web.whatsapp.com/send?phone=' . $ci_wa_link . '&text=Hi",
									mobile: "https://wa.me/' . $ci_wa_link . '/?text=Hi"
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
							},
							';} if (!empty($s->ci_email_description)) { echo '
							{
								avatar: {
									src: \'' . $svgEnvelope . '\',
									backgroundColor: "' . $ci_button_color . '",
									onlineCircle: false
								},
								text: {
									title: "' . addslashes($ci_email_display) . '",
									description: "' . addslashes($ci_email_description) . '",
									online: false,
									offline: false
								},
								link: {
									desktop: "' . addslashes($ci_email_link) . '",
									mobile: false
								},
								onlineDay: {
									sunday: "00:00-23:59", monday: "00:00-23:59", tuesday: "00:00-23:59", wednesday: "00:00-23:59", thursday: "00:00-23:59", friday: "00:00-23:59", saturday: "00:00-23:59"
								}
							},
							';} if (!empty($s->ci_address_description)) { echo '
							{
								avatar: {
									src: \'' . $svgMapMarker . '\',
									backgroundColor: "' . $ci_button_color . '",
									onlineCircle: false
								},
								text: {
									title: "' . addslashes($ci_address_display) . '",
									description: "' . addslashes($ci_address_description) . '",
									online: false,
									offline: false
								},
								link: {
									desktop: "' . addslashes($ci_address_link) . '",
									mobile: false
								},
								onlineDay: {
									sunday: "00:00-23:59", monday: "00:00-23:59", tuesday: "00:00-23:59", wednesday: "00:00-23:59", thursday: "00:00-23:59", friday: "00:00-23:59", saturday: "00:00-23:59"
								}
							},
							';} echo '
							]
						},
						changeBrowserTitle: false,
					});
				});
			</script>';
		}
	
		return true;
	} else {
		return false;
	}  
}

# ===== functions Admin =====

function EasyContactButton_form_process () { 
	global $EasyContactButton_data_setting;
}

function easy_button_settings() {
	global $SITEURL;
	global $EasyContactButton_data;
	global $EasyContactButton;
	
	if (isset($_POST['submit'])) {
		$EasyContactButton_submitted_data['buttonType'] 		= $_POST['buttonType'];
		$EasyContactButton_submitted_data['include_div_man'] 	= $_POST['include_div_man'] ?? 'no';
		
		// WA
		$EasyContactButton_submitted_data['wa_position'] 		= $_POST['wa_position'];
		$EasyContactButton_submitted_data['wa_speechBubble'] 	= $_POST['wa_speechBubble'];
		$EasyContactButton_submitted_data['wa_notification'] 	= isset($_POST['wa_notification']) ? '1' : '0';
		$EasyContactButton_submitted_data['wa_hide_mobile'] 	= $_POST['wa_hide_mobile'] ?? 'false';
		$EasyContactButton_submitted_data['wa_avatar'] 			= $_POST['wa_avatar'];
		$EasyContactButton_submitted_data['wa_custom_avatar'] 	= $_POST['wa_custom_avatar'];
		$EasyContactButton_submitted_data['wa_popup_title'] 	= $_POST['wa_popup_title'];
		$EasyContactButton_submitted_data['wa_popup_description'] = $_POST['wa_popup_description'];
		$EasyContactButton_submitted_data['wa_popup_message'] 	= $_POST['wa_popup_message'];
		$EasyContactButton_submitted_data['wa_popup_textbox'] 	= $_POST['wa_popup_textbox'];
		$EasyContactButton_submitted_data['wa_phone'] 			= $_POST['wa_phone'];
		$EasyContactButton_submitted_data['wa_sound'] 			= isset($_POST['wa_sound']) ? 'true' : 'false';
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

		// Multi ($ma_)
		$EasyContactButton_submitted_data['ma_position'] 		= $_POST['ma_position'];
		$EasyContactButton_submitted_data['ma_speechBubble'] 	= $_POST['ma_speechBubble'];
		$EasyContactButton_submitted_data['ma_notification'] 	= isset($_POST['ma_notification']) ? '1' : '0';
		$EasyContactButton_submitted_data['ma_hide_mobile'] 	= $_POST['ma_hide_mobile'] ?? 'false';
		$EasyContactButton_submitted_data['ma_popup_title'] 	= $_POST['ma_popup_title'];
		$EasyContactButton_submitted_data['ma_popup_description'] = $_POST['ma_popup_description'];
		$EasyContactButton_submitted_data['ma_sound'] 			= isset($_POST['ma_sound']) ? 'true' : 'false';
		$EasyContactButton_submitted_data['ma_browser_tab'] 	= $_POST['ma_browser_tab'];
		$EasyContactButton_submitted_data['ma_persons_json'] 	= $_POST['ma_persons_json'];
		
		// Contact ($ci_)
		$EasyContactButton_submitted_data['ci_position'] 		= $_POST['ci_position'];
		$EasyContactButton_submitted_data['ci_button_title'] 	= $_POST['ci_button_title'];
		$EasyContactButton_submitted_data['ci_button_color'] 	= $_POST['ci_button_color'];
		$EasyContactButton_submitted_data['ci_button_shape'] 	= $_POST['ci_button_shape'];
		$EasyContactButton_submitted_data['ci_automaticOpen'] 	= $_POST['ci_automaticOpen'];
		$EasyContactButton_submitted_data['ci_hide_mobile'] 	= $_POST['ci_hide_mobile'] ?? 'false';
		$EasyContactButton_submitted_data['ci_popup_title'] 	= $_POST['ci_popup_title'];
		$EasyContactButton_submitted_data['ci_popup_description'] = $_POST['ci_popup_description'];
		$EasyContactButton_submitted_data['ci_phone_description'] = $_POST['ci_phone_description'];
		$EasyContactButton_submitted_data['ci_phone_display'] 	= $_POST['ci_phone_display'];
		$EasyContactButton_submitted_data['ci_phone_link'] 		= $_POST['ci_phone_link'];
		$EasyContactButton_submitted_data['ci_wa_description'] 	= $_POST['ci_wa_description'];
		$EasyContactButton_submitted_data['ci_wa_display'] 		= $_POST['ci_wa_display'];
		$EasyContactButton_submitted_data['ci_wa_link'] 		= $_POST['ci_wa_link'];
		$EasyContactButton_submitted_data['ci_wa_avatar'] 		= $_POST['ci_wa_avatar'];
		$EasyContactButton_submitted_data['ci_wa_custom_avatar'] = $_POST['ci_wa_custom_avatar'];
		$EasyContactButton_submitted_data['ci_sound'] 			= isset($_POST['ci_sound']) ? 'true' : 'false';
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

	if (isset($result)) {
		if ($result == true) { 
			echo '<p class="updated" style="background: #DFF8D9; border: solid 1px #ccc; padding: 10px; color: #777; border-radius: 7px; display: block;">✔️ '.i18n_r("EasyContactButton/lang_Status_Saved").'</p>';
			echo "<meta http-equiv='refresh' content='6.75;url=". $_SERVER ['REQUEST_URI']. "'>";
		} elseif ($result == false) { 
			echo '<p class="error">❌ '.i18n_r("EasyContactButton/lang_Status_Error").'</p>';
		}
	}
	
# ===== Start Form =====

	echo '<link href="'.$SITEURL.'plugins/EasyContactButton/assets/css/style.min.css" rel="stylesheet">';
	
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

	<style>
		/* Layout & Column Framework */
		.ecb-grid-2col { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; margin-bottom: 15px; }
		.ecb-form-col { flex: 1 1 320px; min-width: 280px; box-sizing: border-box; }
		.ecb-full-col { flex: 1 1 100%; width: 100%; box-sizing: border-box; }
		.ecb-field-card { background: #fafbfc; border: 1px solid #edf0f5; border-radius: 8px; padding: 16px; margin-bottom: 15px; }
		.ecb-field-card h4 { margin: 0 0 12px; font-size: 0.95rem; color: #2764e7; display: flex; align-items: center; gap: 8px; }
		.ecb-time-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 0.85rem; }
		.ecb-time-row span { min-width: 85px; font-weight: 500; }
		.ecb-time-row input[type="time"] { padding: 3px 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; font-size: 0.85rem; }

		/* Modal Styles */
		.ecb-modal { position:fixed; inset:0; background:rgba(0,0,0,.6); display:flex; align-items:center; justify-content:center; z-index:99999; }
		.ecb-modal-inner { background:#fff; border-radius:10px; width:min(640px,94vw); max-height:82vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.25); }
		.ecb-modal-head { display:flex; justify-content:space-between; align-items:center; padding:.85rem 1.1rem; border-bottom:1px solid #eef0f3; background:#fafbfc; }
		.ecb-modal-head h4 { margin:0; font-size:1.05rem; }
		.ecb-modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; line-height:1; color:#6b7280; padding:0; }
		.ecb-modal-nav { padding:.6rem 1.1rem 0; font-size:.85rem; color:#4b5563; white-space:nowrap; overflow-x:auto; }
		.ecb-breadcrumb a { color:#2764e7; text-decoration:none; cursor:pointer; font-weight:600; }
		.ecb-breadcrumb a:hover { text-decoration:underline; }
		.ecb-breadcrumb span.sep { margin:0 .35rem; color:#9ca3af; }
		.ecb-modal-tabs { display:flex; gap:.4rem; padding:.6rem 1.1rem 0; border-bottom:1px solid #eef0f3; }
		.ecb-tab { background:#f4f6f9; border:none; border-radius:6px 6px 0 0; padding:.45rem .9rem; cursor:pointer; font-size:.875rem; font-weight:600; color:#555; }
		.ecb-tab.active { background:#2764e7; color:#fff; }
		#ecb-tab-browse, #ecb-tab-upload { padding:.85rem 1.1rem 1.1rem; overflow-y:auto; flex:1; }
		#ecb-search { width:100%; box-sizing:border-box; padding:.45rem .65rem; border:1px solid #d0d5dd; border-radius:5px; margin-bottom:.75rem; }
		.ecb-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(90px,1fr)); gap:.6rem; }
		.ecb-grid-img { cursor:pointer; border:1px solid #eef0f3; border-radius:6px; overflow:hidden; aspect-ratio:1/1; position:relative; background:#f9fafb; transition:transform .12s, border-color .12s; }
		.ecb-grid-img:hover { transform:translateY(-2px); border-color:#2764e7; }
		.ecb-grid-img img { width:100%; height:100%; object-fit:cover; display:block; }
		.ecb-grid-folder { cursor:pointer; border:1px dashed #cbd5e1; border-radius:6px; aspect-ratio:1/1; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; padding:.4rem; text-align:center; box-sizing:border-box; }
		.ecb-grid-folder:hover { background:#e2e8f0; border-color:#94a3b8; }
		.ecb-grid-folder svg { width:32px; height:32px; fill:#f59e0b; margin-bottom:.25rem; flex-shrink:0; }
		.ecb-grid-folder-name { font-size:.72rem; word-break:break-word; line-height:1.2; color:#334155; font-weight:500; max-height:2.4em; overflow:hidden; }
		.ecb-upload-target-info { margin-bottom:.75rem; font-size:.85rem; color:#4b5563; }

		/* Person Repeater Items */
		.ma-person-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; position: relative; }
		.ma-person-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid #f1f5f9; padding-bottom:5px; }
		.ma-person-title { font-weight: bold; color: #2764e7; }
		.ma-remove-person { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 3px 8px; border-radius: 4px; cursor: pointer; }
	</style>

	<form method="post" id="EasyContact-admin" action="<?php echo $_SERVER ['REQUEST_URI']; ?>" onsubmit="window.location.reload()" value="refresh">
		<hr class="style-one">
		<div class="container">
			<div class="row">
				<div class="col-1"></div>
				<div class="col-5">
					<legend><?php echo i18n_r("EasyContactButton/lang_Include_method");?>:</legend>
					<fieldset>
						<label for="buttonType_wa">
							<input type="radio" id="buttonType_wa" name="buttonType" value="type_wa" <?php echo (@$EasyContactButton_data_setting['buttonType'] == 'type_wa' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Include_WhatsApp");?>
						</label>
						<label for="buttonType_multi">
							<input type="radio" id="buttonType_multi" name="buttonType" value="type_multi" <?php echo (@$EasyContactButton_data_setting['buttonType'] == 'type_multi' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Include_Multi");?>
						</label>
						<label for="buttonType_contact">
							<input type="radio" id="buttonType_contact" name="buttonType" value="type_contact" <?php echo (@$EasyContactButton_data_setting['buttonType'] == 'type_contact' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Include_Contact");?>
						</label>
					</fieldset>
				</div>
				<div class="col-5">
					<legend><?php echo i18n_r("EasyContactButton/lang_Manually_Add");?></legend>
					<label for="include_div_man">
						<input type="checkbox" id="include_div_man" name="include_div_man" value="yes" <?php echo (@$EasyContactButton_data_setting['include_div_man'] == 'yes' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Am_Professional");?>
					</label>
					
					<div class="code">
						<span>&#60;div id="whatsapp-us-button"&#62;&#60;/div&#62;</span>
						
						<span>&#60;div id="multi-account-button"&#62;&#60;/div&#62;</span>
						
						<span>&#60;div id="contact-us-button"&#62;&#60;/div&#62;</span>
					</div>
				</div>
				<div class="col-1"></div>
			</div>
			
			<hr class="style-three">
			
			<div class="row">
				<div class="col-12 center">
					<p class="small"><a href="#" id="populate-link" style="text-decoration:none;"><img src=" data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAB2AAAAdgFOeyYIAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAUFJREFUOI190z1LXEEUBuBndaNGSKGFpAiCRfQPLG4pKIKFINiki1UWOyvJD0jrX5AEBNnKpAgpIlgoNoJFLOLHkhAMBAmSZQMWSROLO3cZx9m8cJjzMe+58945U3EfdcygEuVusIVOurkv8h/hKXYSMtSwgfGE08VLfMMeLjL1efzAPs4wFRcf4xxDIT7El8Su8DzUl9CMG9TxLvniSGLDUe0JjsqgGtZ/SYN2RkYW1SQeVPzEgSS/jdfB/xss2+AP1vEwyX+N/J+YK4PclZRX2MFxsFjSM3zAWpmo420k4T128SbTvIHPmMYpJnISFjPEkryqkF1TyGynJ+iFRpAyigXFaM/mJOSwEpEn0YrJFGN58J8GLYz1IleCfcSlu+/gu2Jkr7GJZbxQvJcu+sPaxAPF2Jb4jZNgv/AKn9Lj3QKU/EIJlsrdEgAAAABJRU5ErkJggg==" style="vertical-align:middle;"> <?php echo i18n_r("EasyContactButton/lang_Populate_Data");?></a></p>
				</div>
			</div>
			
			<hr class="style-two">
			
			<div class="row">
				<!-- 1. Single WhatsApp Div ($wa_) -->
				<div class="col-12" id="hiddenDivWa">
					<fieldset class="whatsapp-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/whatsapp.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> <?php echo i18n_r("EasyContactButton/lang_WhatsApp");?></legend>
							<hr class="style-three">

							<div class="ecb-grid-2col">
								<!-- Left Column: Button & Avatar Settings -->
								<div class="ecb-form-col">
									<div class="ecb-field-card">
										<h4>General Button Configuration</h4>
										<p>
											<label for="wa_position"><img src="../plugins/EasyContactButton/assets/img/side.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Position");?>:</label>
											<select class="center-block" id="wa_position" name="wa_position">
												<option value="right" <?php echo (@$EasyContactButton_data_setting['wa_position'] == 'right' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Right");?></option>
												<option value="left" <?php echo (@$EasyContactButton_data_setting['wa_position'] == 'left' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Left");?></option>
											</select>
										</p>

										<p>
											<label for="wa_speechBubble"><img src="../plugins/EasyContactButton/assets/img/mini.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Mini_Bubble");?>:</label>   
											<input class="center-block" type="text" id="wa_speechBubble" name="wa_speechBubble" value="<?php echo @$EasyContactButton_data_setting['wa_speechBubble']; ?>"> 
											<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_How_Help");?></span></div> 
										</p>

										<p>
											<label for="wa_browser_tab"><img src="../plugins/EasyContactButton/assets/img/tab.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Tab_Title");?>:</label>    
											<input class="center-block" type="text" id="wa_browser_tab" name="wa_browser_tab" value="<?php echo @$EasyContactButton_data_setting['wa_browser_tab']; ?>">
											<div class="code"><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span><?php echo i18n_r("EasyContactButton/lang_Ex_New_Message");?></span></div>
										</p>

										<p class="center">
											<label for="wa_notification">
												<input type="checkbox" id="wa_notification" name="wa_notification" value="1" <?php echo (@$EasyContactButton_data_setting['wa_notification'] == '1' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Notification_Number");?>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<label for="wa_hide_mobile">
												<input type="checkbox" id="wa_hide_mobile" name="wa_hide_mobile" value="true" <?php echo (@$EasyContactButton_data_setting['wa_hide_mobile'] == 'true' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Hide_Mobile");?>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<label for="wa_sound">
												<input type="checkbox" id="wa_sound" name="wa_sound" value="true" <?php if (!isset($EasyContactButton_data_setting['wa_sound']) || $EasyContactButton_data_setting['wa_sound'] === 'true') { echo 'checked'; } ?> /> <?php echo i18n_r("EasyContactButton/lang_Enable_Sound");?>
											</label>
										</p>
									</div>

									<div class="ecb-field-card">
										<h4>Avatar & Representative</h4>
										<p>
											<label for="wa_avatar"><img src="../plugins/EasyContactButton/assets/img/user.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Popup_Avatar");?>:</label>
											<select class="center-block" id="wa_avatar" name="wa_avatar">
												<option value="male.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'male.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Man");?></option>
												<option value="female.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'female.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Woman");?></option>
												<option value="whatsapp.svg" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'whatsapp.svg' || empty($EasyContactButton_data_setting['wa_avatar']) ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Icon");?></option>
												<option value="custom" <?php echo (@$EasyContactButton_data_setting['wa_avatar'] == 'custom' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Custom");?></option>
											</select>
										</p>

										<?php
											$current_avatar = isset($EasyContactButton_data_setting['wa_avatar']) ? trim((string)$EasyContactButton_data_setting['wa_avatar']) : 'whatsapp.svg';
											$custom_avatar_file = isset($EasyContactButton_data_setting['wa_custom_avatar']) ? trim((string)$EasyContactButton_data_setting['wa_custom_avatar']) : '';

											if ($current_avatar === 'custom' && $custom_avatar_file !== '') {
												$init_preview = $SITEURL . 'data/uploads/' . ltrim($custom_avatar_file, '/');
											} elseif ($current_avatar === 'custom') {
												$init_preview = $SITEURL . 'plugins/EasyContactButton/assets/img/whatsapp.svg';
											} else {
												$init_preview = $SITEURL . 'plugins/EasyContactButton/assets/img/' . ($current_avatar ?: 'whatsapp.svg');
											}
										?>

										<div id="wa_avatar_preview_container" style="text-align: center; margin-bottom: 10px;">
											<input type="hidden" id="wa_custom_avatar" name="wa_custom_avatar" value="<?php echo htmlspecialchars($custom_avatar_file); ?>">
											<div style="margin: 8px 0;">
												<img id="wa_avatar_preview" 
													 src="<?php echo htmlspecialchars($init_preview); ?>" 
													 style="max-width: 65px; max-height: 65px; border-radius: 50%; border: 2px solid #ddd; object-fit: cover; background: #fff;" 
													 alt="Avatar Preview">
											</div>
											<div id="wa_custom_browse_btn_wrap" style="<?php echo ($current_avatar === 'custom' ? '' : 'display:none;'); ?>">
												<button type="button" class="button browse_avatar_trigger" data-target-input="#wa_custom_avatar" data-target-preview="#wa_avatar_preview" style="padding: 5px 12px; cursor: pointer;"><?php echo i18n_r("EasyContactButton/lang_Browse_Upload");?></button>
											</div>
										</div>

										<p>
											<label for="wa_phone"><img src="../plugins/EasyContactButton/assets/img/phone.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Phone_Number");?>:</label>   
											<input class="center-block" type="text" id="wa_phone" name="wa_phone" value="<?php echo @$EasyContactButton_data_setting['wa_phone']; ?>">
											<div class="code"><?php echo i18n_r("EasyContactButton/lang_Including_Country");?> <br><?php echo i18n_r("EasyContactButton/lang_Ex");?>: <span>123456789</span></div>
										</p>
									</div>
								</div>

								<!-- Right Column: Popup Text & Timetable Schedule -->
								<div class="ecb-form-col">
									<div class="ecb-field-card">
										<h4>Popup Content & Text</h4>
										<p>
											<label for="wa_popup_title"><img src="../plugins/EasyContactButton/assets/img/title.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label>   
											<input class="center-block" type="text" id="wa_popup_title" name="wa_popup_title" value="<?php echo @$EasyContactButton_data_setting['wa_popup_title']; ?>">
										</p>
										<p>
											<label for="wa_popup_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>    
											<input class="center-block" type="text" id="wa_popup_description" name="wa_popup_description" value="<?php echo @$EasyContactButton_data_setting['wa_popup_description']; ?>">
										</p>
										<p>
											<label for="wa_popup_message"><?php echo i18n_r("EasyContactButton/lang_Popup_Message");?>:</label>   
											<input class="center-block" type="text" id="wa_popup_message" name="wa_popup_message" value="<?php echo @$EasyContactButton_data_setting['wa_popup_message']; ?>">
										</p>
										<p>
											<label for="wa_popup_textbox"><?php echo i18n_r("EasyContactButton/lang_Popup_Textbox");?>:</label>   
											<input class="center-block" type="text" id="wa_popup_textbox" name="wa_popup_textbox" value="<?php echo @$EasyContactButton_data_setting['wa_popup_textbox']; ?>">
										</p>
									</div>

									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/time.png" style="vertical-align:middle;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Availability");?></h4>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Monday");?>:</span>
											<div><input type="time" name="wa_mon_start" id="wa_mon_start" value="<?php echo @$EasyContactButton_data_setting['wa_mon_start']; ?>"> - <input type="time" name="wa_mon_end" id="wa_mon_end" value="<?php echo @$EasyContactButton_data_setting['wa_mon_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Tuesday");?>:</span>
											<div><input type="time" name="wa_tue_start" id="wa_tue_start" value="<?php echo @$EasyContactButton_data_setting['wa_tue_start']; ?>"> - <input type="time" name="wa_tue_end" id="wa_tue_end" value="<?php echo @$EasyContactButton_data_setting['wa_tue_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Wednesday");?>:</span>
											<div><input type="time" name="wa_wed_start" id="wa_wed_start" value="<?php echo @$EasyContactButton_data_setting['wa_wed_start']; ?>"> - <input type="time" name="wa_wed_end" id="wa_wed_end" value="<?php echo @$EasyContactButton_data_setting['wa_wed_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Thursday");?>:</span>
											<div><input type="time" name="wa_thu_start" id="wa_thu_start" value="<?php echo @$EasyContactButton_data_setting['wa_thu_start']; ?>"> - <input type="time" name="wa_thu_end" id="wa_thu_end" value="<?php echo @$EasyContactButton_data_setting['wa_thu_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Friday");?>:</span>
											<div><input type="time" name="wa_fri_start" id="wa_fri_start" value="<?php echo @$EasyContactButton_data_setting['wa_fri_end']; ?>"> - <input type="time" name="wa_fri_end" id="wa_fri_end" value="<?php echo @$EasyContactButton_data_setting['wa_fri_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Saturday");?>:</span>
											<div><input type="time" name="wa_sat_start" id="wa_sat_start" value="<?php echo @$EasyContactButton_data_setting['wa_sat_start']; ?>"> - <input type="time" name="wa_sat_end" id="wa_sat_end" value="<?php echo @$EasyContactButton_data_setting['wa_sat_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Sunday");?>:</span>
											<div><input type="time" name="wa_sun_start" id="wa_sun_start" value="<?php echo @$EasyContactButton_data_setting['wa_sun_start']; ?>"> - <input type="time" name="wa_sun_end" id="wa_sun_end" value="<?php echo @$EasyContactButton_data_setting['wa_sun_end']; ?>"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>

				<!-- 2. Multi-Account WhatsApp Div ($ma_) -->
				<div class="col-12" id="hiddenDivMulti" style="display:none;">
					<fieldset class="whatsapp-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/whatsapp.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> <?php echo i18n_r("EasyContactButton/lang_Include_Multi");?></legend>
							<hr class="style-three">

							<div class="ecb-grid-2col">
								<div class="ecb-form-col">
									<div class="ecb-field-card">
										<h4>Multi-Account General Settings</h4>
										<p>
											<label for="ma_position"><img src="../plugins/EasyContactButton/assets/img/side.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Position");?>:</label>
											<select class="center-block" id="ma_position" name="ma_position">
												<option value="right" <?php echo (@$EasyContactButton_data_setting['ma_position'] == 'right' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Right");?></option>
												<option value="left" <?php echo (@$EasyContactButton_data_setting['ma_position'] == 'left' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Left");?></option>
											</select>
										</p>
										<p>
											<label for="ma_speechBubble"><img src="../plugins/EasyContactButton/assets/img/mini.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Mini_Bubble");?>:</label>
											<input class="center-block" type="text" id="ma_speechBubble" name="ma_speechBubble" value="<?php echo @$EasyContactButton_data_setting['ma_speechBubble']; ?>">
										</p>
										<p>
											<label for="ma_browser_tab"><img src="../plugins/EasyContactButton/assets/img/tab.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Tab_Title");?>:</label>
											<input class="center-block" type="text" id="ma_browser_tab" name="ma_browser_tab" value="<?php echo @$EasyContactButton_data_setting['ma_browser_tab']; ?>">
										</p>
										<p class="center">
											<label for="ma_notification">
												<input type="checkbox" id="ma_notification" name="ma_notification" value="1" <?php echo (@$EasyContactButton_data_setting['ma_notification'] == '1' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Notification_Number");?>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<label for="ma_hide_mobile">
												<input type="checkbox" id="ma_hide_mobile" name="ma_hide_mobile" value="yes" <?php echo (@$EasyContactButton_data_setting['ma_hide_mobile'] == 'yes' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Hide_Mobile");?>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<label for="ma_sound">
												<input type="checkbox" id="ma_sound" name="ma_sound" value="true" <?php if (!isset($EasyContactButton_data_setting['ma_sound']) || $EasyContactButton_data_setting['ma_sound'] === 'true') { echo 'checked'; } ?> /> <?php echo i18n_r("EasyContactButton/lang_Enable_Sound");?>
											</label>
										</p>
									</div>
								</div>

								<div class="ecb-form-col">
									<div class="ecb-field-card">
										<h4>Popup Header Details</h4>
										<p>
											<label for="ma_popup_title"><img src="../plugins/EasyContactButton/assets/img/title.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label>
											<input class="center-block" type="text" id="ma_popup_title" name="ma_popup_title" value="<?php echo @$EasyContactButton_data_setting['ma_popup_title']; ?>">
										</p>
										<p>
											<label for="ma_popup_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>
											<input class="center-block" type="text" id="ma_popup_description" name="ma_popup_description" value="<?php echo @$EasyContactButton_data_setting['ma_popup_description']; ?>">
										</p>
									</div>
								</div>

								<div class="ecb-full-col">
									<h4 style="text-align:center;"><?php echo i18n_r("EasyContactButton/lang_Representatives");?></h4>
									<input type="hidden" name="ma_persons_json" id="ma_persons_json" value="<?php echo htmlspecialchars(@$EasyContactButton_data_setting['ma_persons_json'] ?: '[]'); ?>">
									<div id="ma_persons_container"></div>

									<div style="text-align:center; margin-top:15px;">
										<button type="button" class="button" id="ma_add_person_btn" style="padding: 7px 18px; cursor: pointer; border-radius:4px;">+ <?php echo i18n_r("EasyContactButton/lang_Add_Person");?></button>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- 3. Contact Info Div ($ci_) -->
				<div class="col-12" id="hiddenDivContact">
					<fieldset class="contact-info">
						<div class="container">
							<legend class="center"><img src="../plugins/EasyContactButton/assets/img/info.png" style="width:24px;vertical-align:middle;margin-right:10px" alt=""> <?php echo i18n_r("EasyContactButton/lang_Contact_Info");?></legend>
							<hr class="style-three">
							
							<div class="ecb-grid-2col">
								<!-- Left Column: Button Style & Phone & WA -->
								<div class="ecb-form-col">
									<div class="ecb-field-card">
										<h4>General Button Setup</h4>
										<p>
											<label for="ci_button_shape"><img src="../plugins/EasyContactButton/assets/img/shape.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Shape");?>:</label>
											<select class="center-block" id="ci_button_shape" name="ci_button_shape">
												<option value="3" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '3' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Round_Rec");?></option>
												<option value="5" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '5' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Rectangle");?></option>
												<option value="1" <?php echo (@$EasyContactButton_data_setting['ci_button_shape'] == '1' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Button_Round");?></option>
											</select>
										</p>
										
										<p>
											<label for="ci_position"><img src="../plugins/EasyContactButton/assets/img/side.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Position");?>:</label>
											<select class="center-block" id="ci_position" name="ci_position">
												<option value="left" <?php echo (@$EasyContactButton_data_setting['ci_position'] == 'left' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Left");?></option>
												<option value="right" <?php echo (@$EasyContactButton_data_setting['ci_position'] == 'right' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Right");?></option>
											</select>
										</p>
										
										<p>
											<label for="ci_button_title"><img src="../plugins/EasyContactButton/assets/img/title.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Title");?>:</label>   
											<input class="center-block" type="text" id="ci_button_title" name="ci_button_title" value="<?php echo @$EasyContactButton_data_setting['ci_button_title']; ?>">
										</p>
										
										<p>
											<label for="ci_button_color"><img src="../plugins/EasyContactButton/assets/img/color.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Button_Color");?>:</label>
											<div style="display:flex; justify-content:center; align-items:center; gap:8px;">
												<input type="color" id="ci_button_color_picker" value="<?php echo (!empty($EasyContactButton_data_setting['ci_button_color']) ? $EasyContactButton_data_setting['ci_button_color'] : '#ed2d34'); ?>" style="height:34px; width:45px; padding:0; cursor:pointer; border:1px solid #ccc; border-radius:4px;">
												<input class="center-block" type="text" id="ci_button_color" name="ci_button_color" maxlength="7" placeholder="#ed2d34" value="<?php echo @$EasyContactButton_data_setting['ci_button_color']; ?>" style="width:120px; font-family:monospace; text-transform:lowercase; margin:0;">
											</div>
										</p>
										
										<p>
											<label for="ci_automaticOpen"><img src="../plugins/EasyContactButton/assets/img/auto.png" style="vertical-align:middle;margin-left:-16px;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Automatic_Open");?>:</label>
											<select class="center-block" id="ci_automaticOpen" name="ci_automaticOpen">
												<option value="true" <?php echo (@$EasyContactButton_data_setting['ci_automaticOpen'] == 'true' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_True");?></option>
												<option value="false" <?php echo (@$EasyContactButton_data_setting['ci_automaticOpen'] == 'false' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_False");?></option>
											</select>
										</p>
										
										<p class="center">
											<label for="ci_hide_mobile">
												<input type="checkbox" id="ci_hide_mobile" name="ci_hide_mobile" value="true" <?php echo (@$EasyContactButton_data_setting['ci_hide_mobile'] == 'true' ? 'checked' : ''); ?> /> <?php echo i18n_r("EasyContactButton/lang_Hide_Mobile");?>
											</label>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<label for="ci_sound">
												<input type="checkbox" id="ci_sound" name="ci_sound" value="true" <?php if (!isset($EasyContactButton_data_setting['ci_sound']) || $EasyContactButton_data_setting['ci_sound'] === 'true') { echo 'checked'; } ?> /> <?php echo i18n_r("EasyContactButton/lang_Enable_Sound");?>
											</label>
										</p>
									</div>
									
									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/title.png" style="vertical-align:middle;margin-left:-16px;" alt=""> Popup Header Info</h4>
										<p>
											<label for="ci_popup_title"><?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label>   
											<input class="center-block" type="text" id="ci_popup_title" name="ci_popup_title" value="<?php echo @$EasyContactButton_data_setting['ci_popup_title']; ?>">
										</p>
										<p>
											<label for="ci_popup_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>    
											<input class="center-block" type="text" id="ci_popup_description" name="ci_popup_description" value="<?php echo @$EasyContactButton_data_setting['ci_popup_description']; ?>">
										</p>
									</div>

									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/time.png" style="vertical-align:middle;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Phone_Avail");?></h4>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Monday");?>:</span>
											<div><input type="time" name="ci_mon_start" id="ci_mon_start" value="<?php echo @$EasyContactButton_data_setting['ci_mon_start']; ?>"> - <input type="time" name="ci_mon_end" id="ci_mon_end" value="<?php echo @$EasyContactButton_data_setting['ci_mon_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Tuesday");?>:</span>
											<div><input type="time" name="ci_tue_start" id="ci_tue_start" value="<?php echo @$EasyContactButton_data_setting['ci_tue_start']; ?>"> - <input type="time" name="ci_tue_end" id="ci_tue_end" value="<?php echo @$EasyContactButton_data_setting['ci_tue_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Wednesday");?>:</span>
											<div><input type="time" name="ci_wed_start" id="ci_wed_start" value="<?php echo @$EasyContactButton_data_setting['ci_wed_start']; ?>"> - <input type="time" name="ci_wed_end" id="ci_wed_end" value="<?php echo @$EasyContactButton_data_setting['ci_wed_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Thursday");?>:</span>
											<div><input type="time" name="ci_thu_start" id="ci_thu_start" value="<?php echo @$EasyContactButton_data_setting['ci_thu_start']; ?>"> - <input type="time" name="ci_thu_end" id="ci_thu_end" value="<?php echo @$EasyContactButton_data_setting['ci_thu_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Friday");?>:</span>
											<div><input type="time" name="ci_fri_start" id="ci_fri_start" value="<?php echo @$EasyContactButton_data_setting['ci_fri_start']; ?>"> - <input type="time" name="ci_fri_end" id="ci_fri_end" value="<?php echo @$EasyContactButton_data_setting['ci_fri_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Saturday");?>:</span>
											<div><input type="time" name="ci_sat_start" id="ci_sat_start" value="<?php echo @$EasyContactButton_data_setting['ci_sat_start']; ?>"> - <input type="time" name="ci_sat_end" id="ci_sat_end" value="<?php echo @$EasyContactButton_data_setting['ci_sat_end']; ?>"></div>
										</div>
										<div class="ecb-time-row">
											<span><?php echo i18n_r("EasyContactButton/lang_Sunday");?>:</span>
											<div><input type="time" name="ci_sun_start" id="ci_sun_start" value="<?php echo @$EasyContactButton_data_setting['ci_sun_start']; ?>"> - <input type="time" name="ci_sun_end" id="ci_sun_end" value="<?php echo @$EasyContactButton_data_setting['ci_sun_end']; ?>"></div>
										</div>
									</div>
								</div>

								<!-- Right Column: Popup Text, Schedule, Email & Address -->
								<div class="ecb-form-col">

									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/phone.png" style="vertical-align:middle;" alt=""> Phone Details</h4>
										<p>
											<label for="ci_phone_description"><?php echo i18n_r("EasyContactButton/lang_Phone_Description");?>:</label>
											<input class="center-block" type="text" id="ci_phone_description" name="ci_phone_description" value="<?php echo @$EasyContactButton_data_setting['ci_phone_description']; ?>">
										</p>
										<p>
											<label for="ci_phone_display"><?php echo i18n_r("EasyContactButton/lang_Phone_Display");?>:</label>   
											<input class="center-block" type="text" id="ci_phone_display" name="ci_phone_display" value="<?php echo @$EasyContactButton_data_setting['ci_phone_display']; ?>">
										</p>
										<p>
											<label for="ci_phone_link"><?php echo i18n_r("EasyContactButton/lang_Phone_Link");?>:</label>   
											<input class="center-block" type="text" id="ci_phone_link" name="ci_phone_link" value="<?php echo @$EasyContactButton_data_setting['ci_phone_link']; ?>">
										</p>
									</div>

									<!-- WhatsApp block in Contact Info -->
									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/whatsapp.png" style="width:20px;vertical-align:middle;" alt=""> WhatsApp Representative</h4>
										<p>
											<label for="ci_wa_avatar"><?php echo i18n_r("EasyContactButton/lang_Popup_Avatar");?>:</label>
											<select class="center-block" id="ci_wa_avatar" name="ci_wa_avatar">
												<option value="whatsapp.svg" <?php echo (@$EasyContactButton_data_setting['ci_wa_avatar'] == 'whatsapp.svg' || empty($EasyContactButton_data_setting['ci_wa_avatar']) ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Icon");?></option>
												<option value="male.svg" <?php echo (@$EasyContactButton_data_setting['ci_wa_avatar'] == 'male.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Man");?></option>
												<option value="female.svg" <?php echo (@$EasyContactButton_data_setting['ci_wa_avatar'] == 'female.svg' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Woman");?></option>
												<option value="custom" <?php echo (@$EasyContactButton_data_setting['ci_wa_avatar'] == 'custom' ? 'selected' : ''); ?>><?php echo i18n_r("EasyContactButton/lang_Custom");?></option>
											</select>
										</p>

										<?php
											$ci_current_wa_avatar = isset($EasyContactButton_data_setting['ci_wa_avatar']) ? trim((string)$EasyContactButton_data_setting['ci_wa_avatar']) : 'whatsapp.svg';
											$ci_custom_wa_avatar_file = isset($EasyContactButton_data_setting['ci_wa_custom_avatar']) ? trim((string)$EasyContactButton_data_setting['ci_wa_custom_avatar']) : '';

											if ($ci_current_wa_avatar === 'custom' && $ci_custom_wa_avatar_file !== '') {
												$ci_init_wa_preview = $SITEURL . 'data/uploads/' . ltrim($ci_custom_wa_avatar_file, '/');
											} elseif ($ci_current_wa_avatar === 'custom') {
												$ci_init_wa_preview = $SITEURL . 'plugins/EasyContactButton/assets/img/whatsapp.svg';
											} else {
												$ci_init_wa_preview = $SITEURL . 'plugins/EasyContactButton/assets/img/' . ($ci_current_wa_avatar ?: 'whatsapp.svg');
											}
										?>

										<div id="ci_wa_avatar_preview_container" style="text-align: center; margin-bottom: 12px;">
											<input type="hidden" id="ci_wa_custom_avatar" name="ci_wa_custom_avatar" value="<?php echo htmlspecialchars($ci_custom_wa_avatar_file); ?>">
											<div style="margin: 8px 0;">
												<img id="ci_wa_avatar_preview" 
													 src="<?php echo htmlspecialchars($ci_init_wa_preview); ?>" 
													 style="max-width: 55px; max-height: 55px; border-radius: 50%; border: 2px solid #ddd; object-fit: cover; background: #fff;" 
													 alt="WA Avatar Preview">
											</div>
											<div id="ci_wa_custom_browse_btn_wrap" style="<?php echo ($ci_current_wa_avatar === 'custom' ? '' : 'display:none;'); ?>">
												<button type="button" class="button browse_avatar_trigger" data-target-input="#ci_wa_custom_avatar" data-target-preview="#ci_wa_avatar_preview" style="padding: 4px 10px; font-size:11px; cursor: pointer;"><?php echo i18n_r("EasyContactButton/lang_Browse_Upload");?></button>
											</div>
										</div>

										<p>
											<label for="ci_wa_description"><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label>
											<input class="center-block" type="text" id="ci_wa_description" name="ci_wa_description" value="<?php echo @$EasyContactButton_data_setting['ci_wa_description']; ?>">
										</p>
										<p>
											<label for="ci_wa_display"><?php echo i18n_r("EasyContactButton/lang_Phone_Display");?>:</label>
											<input class="center-block" type="text" id="ci_wa_display" name="ci_wa_display" value="<?php echo @$EasyContactButton_data_setting['ci_wa_display']; ?>">
										</p>
										<p>
											<label for="ci_wa_link"><?php echo i18n_r("EasyContactButton/lang_Phone_Number");?>:</label>
											<input class="center-block" type="text" id="ci_wa_link" name="ci_wa_link" value="<?php echo @$EasyContactButton_data_setting['ci_wa_link']; ?>">
										</p>
									</div>

									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/email.png" style="vertical-align:middle;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Email_Description");?></h4>
										<p>
											<label for="ci_email_description"><?php echo i18n_r("EasyContactButton/lang_Email_Description");?>:</label>  
											<input class="center-block" type="text" id="ci_email_description" name="ci_email_description" value="<?php echo @$EasyContactButton_data_setting['ci_email_description']; ?>">
										</p>
										<p>
											<label for="ci_email_display"><?php echo i18n_r("EasyContactButton/lang_Email_Display");?>:</label>   
											<input class="center-block" type="text" id="ci_email_display" name="ci_email_display" value="<?php echo @$EasyContactButton_data_setting['ci_email_display']; ?>">
										</p>
										<p>
											<label for="ci_email_link"><?php echo i18n_r("EasyContactButton/lang_Email_Link");?>:</label>  
											<input class="center-block" type="text" id="ci_email_link" name="ci_email_link" value="<?php echo @$EasyContactButton_data_setting['ci_email_link']; ?>">
										</p>
									</div>

									<div class="ecb-field-card">
										<h4><img src="../plugins/EasyContactButton/assets/img/pin.png" style="vertical-align:middle;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Address_Description");?></h4>
										<p>
											<label for="ci_address_description"><?php echo i18n_r("EasyContactButton/lang_Address_Description");?>:</label>  
											<input class="center-block" type="text" id="ci_address_description" name="ci_address_description" value="<?php echo @$EasyContactButton_data_setting['ci_address_description']; ?>">
										</p>
										<p>
											<label for="ci_address_display"><?php echo i18n_r("EasyContactButton/lang_Address");?>:</label>   
											<input class="center-block" type="text" id="ci_address_display" name="ci_address_display" value="<?php echo @$EasyContactButton_data_setting['ci_address_display']; ?>">
										</p>
										<p>
											<label for="ci_address_link"><?php echo i18n_r("EasyContactButton/lang_Google_Link");?>:</label>  
											<input class="center-block" type="text" id="ci_address_link" name="ci_address_link" value="<?php echo @$EasyContactButton_data_setting['ci_address_link']; ?>">
										</p>
									</div>
								</div>
							</div>
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
			<div class="col-12 center">
				<p class="small" style="margin-bottom:0;">Created by: <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 36 36"><path d="M0 0h36v36H0z" fill="none"/><ellipse cx="18" cy="30.54" fill="#3b88c3" rx="18" ry="5.36"/><path fill="#88c9f9" d="M33.812 28.538c0 1.616-2.5 2.587-14.482 2.587c-10.925 0-13.612-.971-13.612-2.587s5.683-2.926 13.612-2.926s14.482 1.31 14.482 2.926"/><path fill="#f4900c" d="M7 28.25c0-1 1-5 11-5c12 0 15 4 15 5s0 2-14 2c-12 0-12-1-12-2"/><circle cx="7" cy="6.25" r="5" fill="#ffcc4d"/><circle cx="7" cy="6.25" r="4" fill="#ffac33"/><path fill="#c1694f" d="M26.949 14.467a21 21 0 0 0-.524-3.459a22 22 0 0 0-1.001-3.158c-.377-.929-.674-1.46-.674-1.46l-2.583.333s.298.622.621 1.841c.131.495.265 1.09.386 1.784a26 26 0 0 1 .343 2.948q.036.555.053 1.158c.004.146.011.287.013.437c.007.469-.026.946-.084 1.422q-.081.644-.223 1.279a19 19 0 0 1-.479 1.77c-.149.46-.308.901-.468 1.316a35 35 0 0 1-.707 1.687c-.358.798-.622 1.33-.622 1.441c0 .351.25 1.007 2 1.444c2.736.684 3-.444 3-.444s.174-.528.376-1.567c.1-.513.206-1.147.303-1.915q.088-.689.158-1.513a44 44 0 0 0 .157-3.478c.002-.176.006-.346.006-.527c0-.459-.021-.903-.051-1.339"/><path fill="#d99e82" d="M24.324 21.362c-1.003-.175-1.643-.467-1.994-.686a35 35 0 0 1-.707 1.687c.655.356 1.487.64 2.389.796a9 9 0 0 0 1.523.136c.296 0 .574-.023.841-.057c.1-.513.206-1.147.303-1.915c-.318.113-1.107.257-2.355.039m.758-3.342c-.782-.077-1.383-.249-1.805-.43a19 19 0 0 1-.479 1.77a8.4 8.4 0 0 0 2.106.476q.462.045.908.045c.359 0 .7-.028 1.026-.071q.074-.857.118-1.863c-.45.098-1.08.152-1.874.073m.221-1.524c.588 0 1.165-.059 1.692-.163c.001-.176.005-.346.005-.527c0-.458-.021-.902-.051-1.339c-.378.099-.919.194-1.682.205c-.775-.003-1.32-.111-1.698-.219c.004.146.011.287.013.437c.007.469-.026.946-.084 1.422a9 9 0 0 0 1.76.184zm-.178-3.273a8.7 8.7 0 0 0 1.648-.418a22 22 0 0 0-.347-1.797a6.4 6.4 0 0 1-1.56.409a6.5 6.5 0 0 1-1.522.055q.109.838.173 1.823c.109.004.207.021.319.021c.42 0 .857-.031 1.289-.093m.909-3.658a22 22 0 0 0-.61-1.715a6.7 6.7 0 0 1-1.242.471a6.6 6.6 0 0 1-1.395.243c.131.495.265 1.09.386 1.784a9 9 0 0 0 1.497-.269a9 9 0 0 0 1.364-.514"/><path fill="#3e721d" d="M34.549 2.943c-.032-.042-3.202-4.283-7.313-2.423c-1.847.835-3.038 2.2-3.469 3.963c-.804-1.125-2.026-1.839-3.644-2.127c-4.421-.788-7.092 4.123-7.118 4.172a.174.174 0 0 0 .256.224c.02-.015 2.037-1.427 5.396-.828a97 97 0 0 1 5.864 1.217a.18.18 0 0 0 .172-.051c.022-.024 2.303-2.475 4.4-3.423c3.104-1.404 5.221-.472 5.24-.462a.176.176 0 0 0 .216-.262"/><path fill="#5c913b" d="M31.129 5.401c-3.134-1.655-5.57-.831-7.083.174c-1.813-.351-5.201-.357-8.072 3.324c-3.525 4.518-.433 10.152-.402 10.209c.031.056.09.089.153.089l.021-.001a.18.18 0 0 0 .147-.128c.011-.039 1.097-3.967 3.843-7.27c1.501-1.805 3.293-3.474 4.554-4.573c1.27.211 3.136.678 4.969 1.702c3.573 1.998 5.212 5.379 5.228 5.413c.032.066.101.112.177.099a.17.17 0 0 0 .151-.134c.014-.063 1.392-6.222-3.686-8.904"/></svg> risingisland</p><a href="https://ko-fi.com/ericmontgomery" target="_blank" class="smaller" style="text-decoration:none;">Show some support by buying me a coffee.</a>
			</div>
		</div>
	</form>

<!-- ===== Custom Image Picker Modal ===== -->
	<div class="ecb-modal" id="ecb-modal" style="display:none;">
		<div class="ecb-modal-inner">
			<div class="ecb-modal-head">
				<h4><?php echo i18n_r('EasyContactButton/lang_PickImage'); ?></h4>
				<button type="button" class="ecb-modal-close" id="ecb-modal-close">&times;</button>
			</div>

			<div class="ecb-modal-nav">
				<span class="ecb-breadcrumb" id="ecb-breadcrumb"></span>
			</div>

			<div class="ecb-modal-tabs">
				<button type="button" class="ecb-tab active" data-tab="browse"><?php echo i18n_r('EasyContactButton/lang_BrowseUploads'); ?></button>
				<button type="button" class="ecb-tab" data-tab="upload"><?php echo i18n_r('EasyContactButton/lang_UploadNew'); ?></button>
			</div>

			<div id="ecb-tab-browse">
				<input type="text" id="ecb-search" placeholder="<?php echo i18n_r('EasyContactButton/lang_FilterImages'); ?>">
				<div class="ecb-grid" id="ecb-grid"></div>
			</div>

			<div id="ecb-tab-upload" style="display:none;">
				<div class="ecb-upload-target-info">
					Target folder: <strong id="ecb-upload-target-folder">/</strong>
				</div>
				<input type="file" id="ecb-upload-input" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
				<p id="ecb-upload-status" style="margin-top:10px; font-weight:600;"></p>
			</div>
		</div>
	</div>

<!-- ===== Admin Vanilla Scripts ===== -->
	<script>
	  document.addEventListener("DOMContentLoaded", function() {
		// Radio toggle display logic
		function applySectionDisplay() {
			var checkedRadio = document.querySelector("input[name='buttonType']:checked");
			var val = checkedRadio ? checkedRadio.value : "";
			
			var divWa = document.getElementById("hiddenDivWa");
			var divMulti = document.getElementById("hiddenDivMulti");
			var divContact = document.getElementById("hiddenDivContact");

			if (divWa) divWa.style.display = "none";
			if (divMulti) divMulti.style.display = "none";
			if (divContact) divContact.style.display = "none";

			if (val === "type_wa" && divWa) {
				divWa.style.display = "block";
			} else if (val === "type_multi" && divMulti) {
				divMulti.style.display = "block";
			} else if (val === "type_contact" && divContact) {
				divContact.style.display = "block";
			}
		}

		var savedRadio = localStorage.getItem("selectedRadioButton");
		if (savedRadio) {
			var matchingInput = document.querySelector("input[name='buttonType'][value='" + savedRadio + "']");
			if (matchingInput) matchingInput.checked = true;
		}
		applySectionDisplay();

		document.querySelectorAll("input[name='buttonType']").forEach(function(radio) {
			radio.addEventListener("change", function() {
				applySectionDisplay();
				localStorage.setItem("selectedRadioButton", this.value);
			});
		});

		// Dynamic Avatar Preview Handling
		var pluginImgBase = '<?php echo $SITEURL; ?>plugins/EasyContactButton/assets/img/';
		var uploadsBase = '<?php echo $SITEURL; ?>data/uploads/';

		function updateAvatarPreview() {
			var select = document.getElementById('wa_avatar');
			var btnWrap = document.getElementById('wa_custom_browse_btn_wrap');
			var imgPreview = document.getElementById('wa_avatar_preview');
			var inputCustom = document.getElementById('wa_custom_avatar');
			if (!select || !imgPreview) return;

			var choice = select.value;
			if (choice === 'custom') {
				if (btnWrap) btnWrap.style.display = '';
				var customFile = inputCustom ? inputCustom.value.trim().replace(/^\/+/, '') : '';
				imgPreview.src = customFile ? (uploadsBase + customFile) : (pluginImgBase + 'whatsapp.svg');
			} else {
				if (btnWrap) btnWrap.style.display = 'none';
				imgPreview.src = pluginImgBase + choice;
			}
		}

		function updateCiWaAvatarPreview() {
			var select = document.getElementById('ci_wa_avatar');
			var btnWrap = document.getElementById('ci_wa_custom_browse_btn_wrap');
			var imgPreview = document.getElementById('ci_wa_avatar_preview');
			var inputCustom = document.getElementById('ci_wa_custom_avatar');
			if (!select || !imgPreview) return;

			var choice = select.value;
			if (choice === 'custom') {
				if (btnWrap) btnWrap.style.display = '';
				var customFile = inputCustom ? inputCustom.value.trim().replace(/^\/+/, '') : '';
				imgPreview.src = customFile ? (uploadsBase + customFile) : (pluginImgBase + 'whatsapp.svg');
			} else {
				if (btnWrap) btnWrap.style.display = 'none';
				imgPreview.src = pluginImgBase + choice;
			}
		}

		var waAvatarSelect = document.getElementById('wa_avatar');
		if (waAvatarSelect) {
			waAvatarSelect.addEventListener('change', updateAvatarPreview);
			updateAvatarPreview();
		}

		var ciWaAvatarSelect = document.getElementById('ci_wa_avatar');
		if (ciWaAvatarSelect) {
			ciWaAvatarSelect.addEventListener('change', updateCiWaAvatarPreview);
			updateCiWaAvatarPreview();
		}

		// Color Picker sync
		var colorPicker = document.getElementById('ci_button_color_picker');
		var colorInput = document.getElementById('ci_button_color');

		if (colorPicker && colorInput) {
			colorPicker.addEventListener('input', function() {
				colorInput.value = this.value;
			});
			colorInput.addEventListener('input', function() {
				var val = this.value.trim();
				if (!val.startsWith('#')) val = '#' + val;
				if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
					colorPicker.value = val;
				}
			});
		}

		// ── Image Picker Modal Logic ──
		var ajaxUrl = 'load.php?id=<?php echo $EasyContactButton; ?>';
		var currentFolder = '';
		var folders = [];
		var images = [];
		var activeInputTarget = '#wa_custom_avatar';
		var activePreviewTarget = '#wa_avatar_preview';

		var modal = document.getElementById('ecb-modal');
		var modalClose = document.getElementById('ecb-modal-close');
		var tabBrowse = document.getElementById('ecb-tab-browse');
		var tabUpload = document.getElementById('ecb-tab-upload');
		var breadcrumb = document.getElementById('ecb-breadcrumb');
		var grid = document.getElementById('ecb-grid');
		var searchInput = document.getElementById('ecb-search');
		var uploadInput = document.getElementById('ecb-upload-input');
		var uploadStatus = document.getElementById('ecb-upload-status');

		document.addEventListener('click', function(e) {
			var trigger = e.target.closest('.browse_avatar_trigger');
			if (trigger) {
				e.preventDefault();
				activeInputTarget = trigger.getAttribute('data-target-input') || '#wa_custom_avatar';
				activePreviewTarget = trigger.getAttribute('data-target-preview') || '#wa_avatar_preview';

				document.querySelectorAll('.ecb-tab').forEach(function(t, idx) {
					t.classList.toggle('active', idx === 0);
				});
				if (tabBrowse) tabBrowse.style.display = 'block';
				if (tabUpload) tabUpload.style.display = 'none';
				if (modal) modal.style.display = 'flex';
				loadFolder(currentFolder);
			}
		});

		if (modalClose) {
			modalClose.addEventListener('click', function() {
				if (modal) modal.style.display = 'none';
			});
		}

		if (modal) {
			modal.addEventListener('click', function(e) {
				if (e.target === modal) modal.style.display = 'none';
			});
		}

		document.querySelectorAll('.ecb-tab').forEach(function(tabBtn) {
			tabBtn.addEventListener('click', function() {
				document.querySelectorAll('.ecb-tab').forEach(function(t) { t.classList.remove('active'); });
				this.classList.add('active');
				var tabName = this.getAttribute('data-tab');
				if (tabName === 'browse') {
					if (tabBrowse) tabBrowse.style.display = 'block';
					if (tabUpload) tabUpload.style.display = 'none';
				} else {
					if (tabBrowse) tabBrowse.style.display = 'none';
					if (tabUpload) tabUpload.style.display = 'block';
				}
			});
		});

		function updateBreadcrumb() {
			var targetFolderEl = document.getElementById('ecb-upload-target-folder');
			if (targetFolderEl) targetFolderEl.textContent = '/' + (currentFolder ? currentFolder + '/' : '');

			var parts = currentFolder ? currentFolder.split('/') : [];
			var html = '<a data-path="">Home</a>';
			var accumulated = '';
			parts.forEach(function(p) {
				accumulated += (accumulated ? '/' : '') + p;
				html += '<span class="sep">/</span><a data-path="' + accumulated + '">' + p + '</a>';
			});
			if (breadcrumb) breadcrumb.innerHTML = html;
		}

		if (breadcrumb) {
			breadcrumb.addEventListener('click', function(e) {
				if (e.target.tagName === 'A') {
					loadFolder(e.target.getAttribute('data-path') || '');
				}
			});
		}

		function loadFolder(folderPath) {
			currentFolder = folderPath || '';
			updateBreadcrumb();
			if (grid) grid.innerHTML = '<p style="padding:1rem;">Loading...</p>';

			fetch(ajaxUrl + '&ecb_action=list_uploads&folder=' + encodeURIComponent(currentFolder))
				.then(function(res) { return res.json(); })
				.then(function(data) {
					folders = data.folders || [];
					images = data.images || [];
					renderGrid();
				})
				.catch(function(err) {
					console.error("ECB Fetch Error:", err);
					if (grid) grid.innerHTML = '<p style="padding:1rem;color:#b91c1c;">Failed to load files.</p>';
				});
		}

		function renderGrid() {
			var filter = (searchInput ? searchInput.value : '').toLowerCase().trim();
			var out = '';

			if (currentFolder && !filter) {
				var upParts = currentFolder.split('/');
				upParts.pop();
				var upPath = upParts.join('/');
				out += '<div class="ecb-grid-folder" data-folder="' + upPath + '">'
					+ '<svg viewBox="0 0 24 24"><path d="M19 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H19z"/></svg>'
					+ '<span class="ecb-grid-folder-name">..</span></div>';
			}

			folders.forEach(function(f) {
				if (filter && f.name.toLowerCase().indexOf(filter) === -1) return;
				out += '<div class="ecb-grid-folder" data-folder="' + f.path + '">'
					+ '<svg viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8z"/></svg>'
					+ '<span class="ecb-grid-folder-name">' + f.name + '</span></div>';
			});

			var filteredImgs = filter ? images.filter(function(img) { return img.filename.toLowerCase().indexOf(filter) !== -1; }) : images;
			filteredImgs.forEach(function(img) {
				out += '<div class="ecb-grid-img" data-filename="' + img.filename + '" title="' + img.filename + '">'
					+ '<img src="' + (img.thumb || img.url) + '" loading="lazy">'
					+ '</div>';
			});

			if (!out) {
				if (grid) grid.innerHTML = '<p style="padding:1rem;">No images found.</p>';
				return;
			}
			if (grid) grid.innerHTML = out;
		}

		if (searchInput) searchInput.addEventListener('input', renderGrid);

		if (grid) {
			grid.addEventListener('click', function(e) {
				var folderItem = e.target.closest('.ecb-grid-folder');
				if (folderItem) {
					loadFolder(folderItem.getAttribute('data-folder'));
					return;
				}
				var imgItem = e.target.closest('.ecb-grid-img');
				if (imgItem) {
					applyCustomAvatar(imgItem.getAttribute('data-filename'));
				}
			});
		}

		function applyCustomAvatar(filename) {
			var cleanName = (filename || '').trim().replace(/^\/+/, '');
			var inputEl = document.querySelector(activeInputTarget);
			var previewEl = document.querySelector(activePreviewTarget);
			if (inputEl) inputEl.value = cleanName;
			if (previewEl) previewEl.src = uploadsBase + cleanName;
			if (modal) modal.style.display = 'none';
		}

		if (uploadInput) {
			uploadInput.addEventListener('change', function() {
				var file = this.files && this.files[0];
				if (!file) return;

				var fd = new FormData();
				fd.append('ecb_action', 'upload_image');
				fd.append('image', file);
				fd.append('folder', currentFolder);

				if (uploadStatus) uploadStatus.textContent = 'Uploading...';

				fetch(ajaxUrl, {
					method: 'POST',
					body: fd
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					if (uploadStatus) uploadStatus.textContent = '';
					uploadInput.value = '';
					if (res.ok) {
						loadFolder(currentFolder);
						applyCustomAvatar(res.image.filename);
					} else {
						if (uploadStatus) uploadStatus.textContent = res.msg || 'Upload failed.';
					}
				})
				.catch(function(err) {
					console.error("ECB Upload Error:", err);
					if (uploadStatus) uploadStatus.textContent = 'Upload failed.';
				});
			});
		}

		// ── Repeatable Multi-Account Persons Handling ──
		var maPersonsJsonInput = document.getElementById('ma_persons_json');
		var persons = [];
		try {
			persons = JSON.parse(maPersonsJsonInput ? maPersonsJsonInput.value : '[]');
		} catch(e) {
			persons = [];
		}

		function renderPersonCard(p, index) {
			var curAvatar = p.avatar || 'whatsapp.svg';
			var customFile = (p.custom_avatar || '').trim().replace(/^\/+/, '');
			var imgSrc = (curAvatar === 'custom' && customFile) ? (uploadsBase + customFile) : (pluginImgBase + curAvatar);

			var days = ['mon','tue','wed','thu','fri','sat','sun'];
			var dayLabels = {
				mon: '<?php echo i18n_r("EasyContactButton/lang_Monday");?>',
				tue: '<?php echo i18n_r("EasyContactButton/lang_Tuesday");?>',
				wed: '<?php echo i18n_r("EasyContactButton/lang_Wednesday");?>',
				thu: '<?php echo i18n_r("EasyContactButton/lang_Thursday");?>',
				fri: '<?php echo i18n_r("EasyContactButton/lang_Friday");?>',
				sat: '<?php echo i18n_r("EasyContactButton/lang_Saturday");?>',
				sun: '<?php echo i18n_r("EasyContactButton/lang_Sunday");?>'
			};

			var html = '<div class="ma-person-card" data-idx="' + index + '">'
				+ '<div class="ma-person-head">'
				+ '<span class="ma-person-title"><?php echo i18n_r("EasyContactButton/lang_Representative");?> #' + (index + 1) + '</span>'
				+ '<button type="button" class="ma-remove-person">&times; <?php echo i18n_r("EasyContactButton/lang_Remove");?></button>'
				+ '</div>'
				+ '<div class="ecb-grid-2col">'
				+ '<div class="ecb-form-col" style="display:flex; gap:12px; align-items:flex-start;">'
				+ '<div style="flex:0 0 100px; text-align:center;">'
				+ '<img class="ma-p-preview" id="ma_prev_person_' + index + '" src="' + imgSrc + '" style="width:55px; height:55px; border-radius:50%; border:2px solid #ddd; object-fit:cover; display:block; margin:0 auto 8px;">'
				+ '<input type="hidden" class="ma-p-custom" id="ma_custom_person_' + index + '" value="' + customFile + '">'
				+ '<button type="button" class="button browse_avatar_trigger ma-p-browse-btn" data-target-input="#ma_custom_person_' + index + '" data-target-preview="#ma_prev_person_' + index + '" style="padding:2px 8px; font-size:11px;' + (curAvatar === 'custom' ? '' : 'display:none;') + '"><?php echo i18n_r("EasyContactButton/lang_Browse_Upload");?></button>'
				+ '</div>'
				+ '<div style="flex:1;">'
				+ '<label style="font-weight:600; font-size:0.85rem;"><?php echo i18n_r("EasyContactButton/lang_Popup_Avatar");?>:</label>'
				+ '<select class="ma-p-avatar" style="width:100%; margin-bottom:8px;">'
				+ '<option value="whatsapp.svg"' + (curAvatar === 'whatsapp.svg' ? ' selected' : '') + '><?php echo i18n_r("EasyContactButton/lang_Icon");?></option>'
				+ '<option value="male.svg"' + (curAvatar === 'male.svg' ? ' selected' : '') + '><?php echo i18n_r("EasyContactButton/lang_Man");?></option>'
				+ '<option value="female.svg"' + (curAvatar === 'female.svg' ? ' selected' : '') + '><?php echo i18n_r("EasyContactButton/lang_Woman");?></option>'
				+ '<option value="custom"' + (curAvatar === 'custom' ? ' selected' : '') + '><?php echo i18n_r("EasyContactButton/lang_Custom");?></option>'
				+ '</select>'
				+ '<p style="margin:0 0 8px;"><label><?php echo i18n_r("EasyContactButton/lang_Popup_Title");?>:</label><input type="text" class="ma-p-title" value="' + (p.title || '') + '" style="width:100%;"></p>'
				+ '<p style="margin:0 0 8px;"><label><?php echo i18n_r("EasyContactButton/lang_Popup_Description");?>:</label><input type="text" class="ma-p-description" value="' + (p.description || '') + '" style="width:100%;"></p>'
				+ '<p style="margin:0 0 8px;"><label><?php echo i18n_r("EasyContactButton/lang_Phone_Number");?>:</label><input type="text" class="ma-p-phone" value="' + (p.phone || '') + '" style="width:100%;"></p>'
				+ '</div>'
				+ '</div>'
				+ '<div class="ecb-form-col" style="background:#f8fafc; border:1px solid #f1f5f9; border-radius:6px; padding:12px;">'
				+ '<label style="font-weight:600; font-size:0.85rem; margin-bottom:8px; display:block;"><img src="../plugins/EasyContactButton/assets/img/time.png" style="vertical-align:middle;" alt=""> <?php echo i18n_r("EasyContactButton/lang_Availability");?>:</label>';

			days.forEach(function(d) {
				var st = p[d + '_start'] || '00:00';
				var en = p[d + '_end'] || '23:59';
				html += '<div class="ecb-time-row">'
					+ '<span>' + dayLabels[d] + '</span>'
					+ '<div><input type="time" class="ma-p-' + d + '-start" value="' + st + '"> - <input type="time" class="ma-p-' + d + '-end" value="' + en + '"></div>'
					+ '</div>';
			});

			html += '</div></div></div>';
			return html;
		}

		window.refreshPersonsList = function() {
			var wrap = document.getElementById('ma_persons_container');
			if (!wrap) return;
			wrap.innerHTML = '';
			persons.forEach(function(p, i) {
				wrap.insertAdjacentHTML('beforeend', renderPersonCard(p, i));
			});
		};

		refreshPersonsList();

		var addPersonBtn = document.getElementById('ma_add_person_btn');
		if (addPersonBtn) {
			addPersonBtn.addEventListener('click', function(e) {
				e.preventDefault();
				persons.push({
					avatar: 'whatsapp.svg',
					custom_avatar: '',
					title: 'Support Agent',
					description: 'Customer Support',
					phone: '0123456789',
					mon_start: '08:00', mon_end: '17:00',
					tue_start: '08:00', tue_end: '17:00',
					wed_start: '08:00', wed_end: '17:00',
					thu_start: '08:00', thu_end: '17:00',
					fri_start: '08:00', fri_end: '17:00',
					sat_start: '00:00', sat_end: '00:00',
					sun_start: '00:00', sun_end: '00:00'
				});
				refreshPersonsList();
			});
		}

		document.addEventListener('click', function(e) {
			var rmBtn = e.target.closest('.ma-remove-person');
			if (rmBtn) {
				var card = rmBtn.closest('.ma-person-card');
				var idx = parseInt(card.getAttribute('data-idx'), 10);
				persons.splice(idx, 1);
				refreshPersonsList();
			}
		});

		document.addEventListener('change', function(e) {
			if (e.target.classList.contains('ma-p-avatar')) {
				var card = e.target.closest('.ma-person-card');
				var choice = e.target.value;
				var btn = card.querySelector('.ma-p-browse-btn');
				var prev = card.querySelector('.ma-p-preview');
				var customInput = card.querySelector('.ma-p-custom');
				var customFile = customInput ? customInput.value.trim().replace(/^\/+/, '') : '';

				if (choice === 'custom') {
					if (btn) btn.style.display = '';
					if (prev) prev.src = customFile ? (uploadsBase + customFile) : (pluginImgBase + 'whatsapp.svg');
				} else {
					if (btn) btn.style.display = 'none';
					if (prev) prev.src = pluginImgBase + choice;
				}
			}
		});

		window.serializePersons = function() {
			var data = [];
			var days = ['mon','tue','wed','thu','fri','sat','sun'];
			document.querySelectorAll('#ma_persons_container .ma-person-card').forEach(function(c) {
				var obj = {
					avatar: c.querySelector('.ma-p-avatar').value,
					custom_avatar: c.querySelector('.ma-p-custom').value,
					title: c.querySelector('.ma-p-title').value,
					description: c.querySelector('.ma-p-description').value,
					phone: c.querySelector('.ma-p-phone').value
				};
				days.forEach(function(d) {
					var sEl = c.querySelector('.ma-p-' + d + '-start');
					var eEl = c.querySelector('.ma-p-' + d + '-end');
					obj[d + '_start'] = sEl ? sEl.value : '00:00';
					obj[d + '_end']   = eEl ? eEl.value : '23:59';
				});
				data.push(obj);
			});
			if (maPersonsJsonInput) {
				maPersonsJsonInput.value = JSON.stringify(data);
			}
		};

		var mainForm = document.getElementById('EasyContact-admin');
		if (mainForm) {
			mainForm.addEventListener('submit', function() {
				serializePersons();
			});
		}
	  });
	</script>

<!-- ===== Demo Data Script ===== -->
	<script>
	  const populateLink = document.getElementById("populate-link");
	  
	  const wa_speechBubble_Field 	= document.getElementById("wa_speechBubble");
	  const wa_popup_title_Field 	= document.getElementById("wa_popup_title");
	  const wa_popup_description_Field = document.getElementById("wa_popup_description");
	  const wa_popup_message_Field 	= document.getElementById("wa_popup_message");
	  const wa_popup_textbox_Field 	= document.getElementById("wa_popup_textbox");
	  const wa_phone_Field 			= document.getElementById("wa_phone");
	  const wa_sound_Field 			= document.getElementById("wa_sound");
	  
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

	  const ma_speechBubble_Field 	= document.getElementById("ma_speechBubble");
	  const ma_popup_title_Field 	= document.getElementById("ma_popup_title");
	  const ma_popup_description_Field = document.getElementById("ma_popup_description");
	  const ma_browser_tab_Field 	= document.getElementById("ma_browser_tab");
	  
	  const ci_button_title_Field 	= document.getElementById("ci_button_title");
	  const ci_button_color_Field 	= document.getElementById("ci_button_color");
	  const ci_button_color_Picker 	= document.getElementById("ci_button_color_picker");
	  const ci_popup_title_Field 	= document.getElementById("ci_popup_title");
	  const ci_popup_description_Field = document.getElementById("ci_popup_description");
	  const ci_phone_description_Field = document.getElementById("ci_phone_description");
	  const ci_phone_display_Field 	= document.getElementById("ci_phone_display");
	  const ci_phone_link_Field 	= document.getElementById("ci_phone_link");
	  
	  const ci_wa_description_Field = document.getElementById("ci_wa_description");
	  const ci_wa_display_Field 	= document.getElementById("ci_wa_display");
	  const ci_wa_link_Field 		= document.getElementById("ci_wa_link");

	  const ci_sound_Field 		= document.getElementById("ci_sound");
	  
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
		
		// 1. WhatsApp Single Form
		if (wa_speechBubble_Field) wa_speechBubble_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_How_Help");?>";
		if (wa_popup_title_Field) wa_popup_title_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Need_Help");?>";
		if (wa_popup_description_Field) wa_popup_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Customer_Support");?>";
		if (wa_popup_message_Field) wa_popup_message_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Hi_There_Data");?>";
		if (wa_popup_textbox_Field) wa_popup_textbox_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Ask_Us");?>";
		if (wa_phone_Field) wa_phone_Field.value 		= "0123456789";
		
		if (wa_mon_start_Field) wa_mon_start_Field.value 	= "08:30";
		if (wa_mon_end_Field) wa_mon_end_Field.value 		= "17:30";
		if (wa_tue_start_Field) wa_tue_start_Field.value 	= "08:00";
		if (wa_tue_end_Field) wa_tue_end_Field.value 		= "17:00";
		if (wa_wed_start_Field) wa_wed_start_Field.value 	= "08:00";
		if (wa_wed_end_Field) wa_wed_end_Field.value 		= "17:00";
		if (wa_thu_start_Field) wa_thu_start_Field.value 	= "08:00";
		if (wa_thu_end_Field) wa_thu_end_Field.value 		= "17:00";
		if (wa_fri_start_Field) wa_fri_start_Field.value 	= "09:00";
		if (wa_fri_end_Field) wa_fri_end_Field.value 		= "15:00";
		if (wa_sat_start_Field) wa_sat_start_Field.value 	= "00:00";
		if (wa_sat_end_Field) wa_sat_end_Field.value 		= "00:00";
		if (wa_sun_start_Field) wa_sun_start_Field.value 	= "00:00";
		if (wa_sun_end_Field) wa_sun_end_Field.value 		= "00:00";
		
		if (wa_browser_tab_Field) wa_browser_tab_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_New_Message");?>";
		
		// 2. Multi-Account WhatsApp Form
		if (ma_speechBubble_Field) ma_speechBubble_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_How_Help");?>";
		if (ma_popup_title_Field) ma_popup_title_Field.value = "Need help? Chat with us";
		if (ma_popup_description_Field) ma_popup_description_Field.value = "Click one of our representatives below";
		if (ma_browser_tab_Field) ma_browser_tab_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_New_Message");?>";

		persons = [
			{
				avatar: "female.svg",
				custom_avatar: "",
				title: "Lorna Hensley",
				description: "Sales Support",
				phone: "0123456789",
				mon_start: "08:30", mon_end: "17:30",
				tue_start: "08:00", tue_end: "17:00",
				wed_start: "08:00", wed_end: "17:00",
				thu_start: "08:00", thu_end: "17:00",
				fri_start: "09:00", fri_end: "15:00",
				sat_start: "00:00", sat_end: "00:00",
				sun_start: "00:00", sun_end: "00:00"
			},
			{
				avatar: "male.svg",
				custom_avatar: "",
				title: "Mattie Simmonds",
				description: "Customer Support",
				phone: "0123456789",
				mon_start: "08:30", mon_end: "17:30",
				tue_start: "08:00", tue_end: "17:00",
				wed_start: "08:00", wed_end: "17:00",
				thu_start: "08:00", thu_end: "17:00",
				fri_start: "09:00", fri_end: "15:00",
				sat_start: "00:00", sat_end: "00:00",
				sun_start: "00:00", sun_end: "00:00"
			},
			{
				avatar: "whatsapp.svg",
				custom_avatar: "",
				title: "Kole Cleg",
				description: "Technical Support",
				phone: "0123456789",
				mon_start: "00:00", mon_end: "23:59",
				tue_start: "00:00", tue_end: "23:59",
				wed_start: "00:00", wed_end: "23:59",
				thu_start: "00:00", thu_end: "23:59",
				fri_start: "00:00", fri_end: "23:59",
				sat_start: "00:00", sat_end: "00:00",
				sun_start: "00:00", sun_end: "00:00"
			}
		];
		if (typeof refreshPersonsList === "function") refreshPersonsList();
		if (typeof serializePersons === "function") serializePersons();

		// 3. Contact Form
		if (ci_button_title_Field) ci_button_title_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us");?>";
		if (ci_button_color_Field) ci_button_color_Field.value = "#ed2d34";
		if (ci_button_color_Picker) ci_button_color_Picker.value = "#ed2d34";
		if (ci_popup_title_Field) ci_popup_title_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Our_Info");?>";
		if (ci_popup_description_Field) ci_popup_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Contact_Us_247");?>";
		if (ci_phone_description_Field) ci_phone_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Phone_Number");?>";
		if (ci_phone_display_Field) ci_phone_display_Field.value = "+12 345 6789";
		if (ci_phone_link_Field) ci_phone_link_Field.value 	= "123456789";

		if (ci_wa_description_Field) ci_wa_description_Field.value = "WhatsApp Support";
		if (ci_wa_display_Field) ci_wa_display_Field.value 	= "+12 345 6789";
		if (ci_wa_link_Field) ci_wa_link_Field.value 		= "123456789";
		
		if (ci_mon_start_Field) ci_mon_start_Field.value 	= "08:30";
		if (ci_mon_end_Field) ci_mon_end_Field.value 		= "17:30";
		if (ci_tue_start_Field) ci_tue_start_Field.value 	= "08:00";
		if (ci_tue_end_Field) ci_tue_end_Field.value 		= "17:00";
		if (ci_wed_start_Field) ci_wed_start_Field.value 	= "08:00";
		if (ci_wed_end_Field) ci_wed_end_Field.value 		= "17:00";
		if (ci_thu_start_Field) ci_thu_start_Field.value 	= "08:00";
		if (ci_thu_end_Field) ci_thu_end_Field.value 		= "17:00";
		if (ci_fri_start_Field) ci_fri_start_Field.value 	= "09:00";
		if (ci_fri_end_Field) ci_fri_end_Field.value 		= "15:00";
		if (ci_sat_start_Field) ci_sat_start_Field.value 	= "00:00";
		if (ci_sat_end_Field) ci_sat_end_Field.value 		= "00:00";
		if (ci_sun_start_Field) ci_sun_start_Field.value 	= "00:00";
		if (ci_sun_end_Field) ci_sun_end_Field.value 		= "00:00";
		
		if (ci_email_description_Field) ci_email_description_Field.value = "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Address");?>";
		if (ci_email_display_Field) ci_email_display_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Show");?>";
		if (ci_email_link_Field) ci_email_link_Field.value 		= "<?php echo i18n_r("EasyContactButton/lang_Ex_Email_Real");?>";
		if (ci_address_description_Field) ci_address_description_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Office_Address");?>";
		if (ci_address_display_Field) ci_address_display_Field.value 	= "<?php echo i18n_r("EasyContactButton/lang_Ex_Address");?>";
		if (ci_address_link_Field) ci_address_link_Field.value 	= "https://www.google.com/maps/@40.7558962,-73.9889626,16z";
	  });
	</script>
</div>
<!-- end plugin admin -->

<?php
} 

function EasyContactButton_read_settings() {
	global $EasyContactButton_data;

	if (file_exists($EasyContactButton_data)) {
		$data = getXML($EasyContactButton_data);
		$EasyContactButton_data_setting['buttonType'] 		= (string)$data->buttonType;
		$EasyContactButton_data_setting['include_div_man'] 	= (string)$data->include_div_man;
		
		// WA
		$EasyContactButton_data_setting['wa_position'] 		= (string)$data->wa_position;
		$EasyContactButton_data_setting['wa_speechBubble'] 	= (string)$data->wa_speechBubble;
		$EasyContactButton_data_setting['wa_notification'] 	= isset($data->wa_notification) ? (string)$data->wa_notification : '0';
		$EasyContactButton_data_setting['wa_hide_mobile'] 	= (string)$data->wa_hide_mobile;
		$EasyContactButton_data_setting['wa_avatar'] 		= (string)$data->wa_avatar;
		$EasyContactButton_data_setting['wa_custom_avatar'] = isset($data->wa_custom_avatar) ? (string)$data->wa_custom_avatar : '';
		$EasyContactButton_data_setting['wa_popup_title'] 	= (string)$data->wa_popup_title;
		$EasyContactButton_data_setting['wa_popup_description'] = (string)$data->wa_popup_description;
		$EasyContactButton_data_setting['wa_popup_message'] = (string)$data->wa_popup_message;
		$EasyContactButton_data_setting['wa_popup_textbox'] = (string)$data->wa_popup_textbox;
		$EasyContactButton_data_setting['wa_phone'] 		= (string)$data->wa_phone;
		$EasyContactButton_data_setting['wa_sound'] 		= isset($data->wa_sound) ? (string)$data->wa_sound : 'true';
		$EasyContactButton_data_setting['wa_mon_start'] 	= (string)$data->wa_mon_start;
		$EasyContactButton_data_setting['wa_mon_end'] 		= (string)$data->wa_mon_end;
		$EasyContactButton_data_setting['wa_tue_start'] 	= (string)$data->wa_tue_start;
		$EasyContactButton_data_setting['wa_tue_end'] 		= (string)$data->wa_tue_end;
		$EasyContactButton_data_setting['wa_wed_start'] 	= (string)$data->wa_wed_start;
		$EasyContactButton_data_setting['wa_wed_end'] 		= (string)$data->wa_wed_end;
		$EasyContactButton_data_setting['wa_thu_start'] 	= (string)$data->wa_thu_start;
		$EasyContactButton_data_setting['wa_thu_end'] 		= (string)$data->wa_thu_end;
		$EasyContactButton_data_setting['wa_fri_start'] 	= (string)$data->wa_fri_start;
		$EasyContactButton_data_setting['wa_fri_end'] 		= (string)$data->wa_fri_end;
		$EasyContactButton_data_setting['wa_sat_start'] 	= (string)$data->wa_sat_start;
		$EasyContactButton_data_setting['wa_sat_end'] 		= (string)$data->wa_sat_end;
		$EasyContactButton_data_setting['wa_sun_start'] 	= (string)$data->wa_sun_start;
		$EasyContactButton_data_setting['wa_sun_end'] 		= (string)$data->wa_sun_end;
		$EasyContactButton_data_setting['wa_browser_tab'] 	= (string)$data->wa_browser_tab;

		// Multi ($ma_)
		$EasyContactButton_data_setting['ma_position'] 		= isset($data->ma_position) ? (string)$data->ma_position : 'right';
		$EasyContactButton_data_setting['ma_speechBubble'] 	= isset($data->ma_speechBubble) ? (string)$data->ma_speechBubble : '';
		$EasyContactButton_data_setting['ma_notification'] 	= isset($data->ma_notification) ? (string)$data->ma_notification : '0';
		$EasyContactButton_data_setting['ma_hide_mobile'] 	= isset($data->ma_hide_mobile) ? (string)$data->ma_hide_mobile : 'false';
		$EasyContactButton_data_setting['ma_popup_title'] 	= isset($data->ma_popup_title) ? (string)$data->ma_popup_title : 'Need help? Chat with us';
		$EasyContactButton_data_setting['ma_popup_description'] = isset($data->ma_popup_description) ? (string)$data->ma_popup_description : 'Click one of our representatives below';
		$EasyContactButton_data_setting['ma_sound'] 		= isset($data->ma_sound) ? (string)$data->ma_sound : 'true';
		$EasyContactButton_data_setting['ma_browser_tab'] 	= isset($data->ma_browser_tab) ? (string)$data->ma_browser_tab : '';
		$EasyContactButton_data_setting['ma_persons_json'] 	= isset($data->ma_persons_json) ? (string)$data->ma_persons_json : '[]';
		
		// Contact ($ci_)
		$EasyContactButton_data_setting['ci_position'] 		= (string)$data->ci_position;
		$EasyContactButton_data_setting['ci_button_title'] 	= (string)$data->ci_button_title;
		$EasyContactButton_data_setting['ci_button_color'] 	= (string)$data->ci_button_color;
		$EasyContactButton_data_setting['ci_button_shape'] 	= (string)$data->ci_button_shape;
		$EasyContactButton_data_setting['ci_automaticOpen'] = (string)$data->ci_automaticOpen;
		$EasyContactButton_data_setting['ci_hide_mobile'] 	= (string)$data->ci_hide_mobile;
		$EasyContactButton_data_setting['ci_popup_title'] 	= (string)$data->ci_popup_title;
		$EasyContactButton_data_setting['ci_popup_description'] = (string)$data->ci_popup_description;
		$EasyContactButton_data_setting['ci_phone_description'] = (string)$data->ci_phone_description;
		$EasyContactButton_data_setting['ci_phone_display'] = (string)$data->ci_phone_display;
		$EasyContactButton_data_setting['ci_phone_link'] 	= (string)$data->ci_phone_link;
		$EasyContactButton_data_setting['ci_wa_description'] = isset($data->ci_wa_description) ? (string)$data->ci_wa_description : '';
		$EasyContactButton_data_setting['ci_wa_display'] 	= isset($data->ci_wa_display) ? (string)$data->ci_wa_display : '';
		$EasyContactButton_data_setting['ci_wa_link'] 		= isset($data->ci_wa_link) ? (string)$data->ci_wa_link : '';
		$EasyContactButton_data_setting['ci_wa_avatar'] 	= isset($data->ci_wa_avatar) ? (string)$data->ci_wa_avatar : 'whatsapp.svg';
		$EasyContactButton_data_setting['ci_wa_custom_avatar'] = isset($data->ci_wa_custom_avatar) ? (string)$data->ci_wa_custom_avatar : '';
		$EasyContactButton_data_setting['ci_sound'] 		= isset($data->ci_sound) ? (string)$data->ci_sound : 'true';
		$EasyContactButton_data_setting['ci_mon_start'] 	= (string)$data->ci_mon_start;
		$EasyContactButton_data_setting['ci_mon_end'] 		= (string)$data->ci_mon_end;
		$EasyContactButton_data_setting['ci_tue_start'] 	= (string)$data->ci_tue_start;
		$EasyContactButton_data_setting['ci_tue_end'] 		= (string)$data->ci_tue_end;
		$EasyContactButton_data_setting['ci_wed_start'] 	= (string)$data->ci_wed_start;
		$EasyContactButton_data_setting['ci_wed_end'] 		= (string)$data->ci_wed_end;
		$EasyContactButton_data_setting['ci_thu_start'] 	= (string)$data->ci_thu_start;
		$EasyContactButton_data_setting['ci_thu_end'] 		= (string)$data->ci_thu_end;
		$EasyContactButton_data_setting['ci_fri_start'] 	= (string)$data->ci_fri_start;
		$EasyContactButton_data_setting['ci_fri_end'] 		= (string)$data->ci_fri_end;
		$EasyContactButton_data_setting['ci_sat_start'] 	= (string)$data->ci_sat_start;
		$EasyContactButton_data_setting['ci_sat_end'] 		= (string)$data->ci_sat_end;
		$EasyContactButton_data_setting['ci_sun_start'] 	= (string)$data->ci_sun_start;
		$EasyContactButton_data_setting['ci_sun_end'] 		= (string)$data->ci_sun_end;
		$EasyContactButton_data_setting['ci_email_description'] = (string)$data->ci_email_description;
		$EasyContactButton_data_setting['ci_email_display'] = (string)$data->ci_email_display;
		$EasyContactButton_data_setting['ci_email_link'] 	= (string)$data->ci_email_link;
		$EasyContactButton_data_setting['ci_address_description'] = (string)$data->ci_address_description;
		$EasyContactButton_data_setting['ci_address_display'] = (string)$data->ci_address_display;
		$EasyContactButton_data_setting['ci_address_link'] 	= (string)$data->ci_address_link;
		$EasyContactButton_data_setting['ci_browser_tab'] 	= (string)$data->ci_browser_tab;
	}
	
	$EasyContactButton_data_setting['site_root'] = '/';
	return $EasyContactButton_data_setting;
}

function EasyContactButton_save_settings($settings) {
	global $EasyContactButton_data;

	$xml = @new simpleXMLElement('<EasyContactButton_settings></EasyContactButton_settings>');
	
	$xml->addChild('buttonType', $settings['buttonType']);
	$xml->addChild('include_div_man', $settings['include_div_man']);
	
	// WA
	$xml->addChild('wa_position', $settings['wa_position']);
	$xml->addChild('wa_speechBubble', $settings['wa_speechBubble']);
	$xml->addChild('wa_notification', isset($settings['wa_notification']) && $settings['wa_notification'] == '1' ? '1' : '0');
	$xml->addChild('wa_hide_mobile', $settings['wa_hide_mobile']);
	$xml->addChild('wa_avatar', $settings['wa_avatar']);
	$xml->addChild('wa_custom_avatar', $settings['wa_custom_avatar']);
	$xml->addChild('wa_popup_title', $settings['wa_popup_title']);
	$xml->addChild('wa_popup_description', $settings['wa_popup_description']);
	$xml->addChild('wa_popup_message', $settings['wa_popup_message']);
	$xml->addChild('wa_popup_textbox', $settings['wa_popup_textbox']);
	$xml->addChild('wa_phone', $settings['wa_phone']);
	$settings['wa_sound'] = isset($_POST['wa_sound']) ? 'true' : 'false';
	$xml->addChild('wa_sound', $settings['wa_sound']);
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

	// Multi ($ma_)
	$xml->addChild('ma_position', $settings['ma_position']);
	$xml->addChild('ma_speechBubble', $settings['ma_speechBubble']);
	$xml->addChild('ma_notification', isset($settings['ma_notification']) && $settings['ma_notification'] == '1' ? '1' : '0');
	$xml->addChild('ma_hide_mobile', $settings['ma_hide_mobile']);
	$xml->addChild('ma_popup_title', $settings['ma_popup_title']);
	$xml->addChild('ma_popup_description', $settings['ma_popup_description']);
	$settings['ma_sound'] = isset($_POST['ma_sound']) ? 'true' : 'false';
	$xml->addChild('ma_sound', $settings['ma_sound']);
	$xml->addChild('ma_browser_tab', $settings['ma_browser_tab']);
	$xml->addChild('ma_persons_json', $settings['ma_persons_json']);
	
	// Contact ($ci_)
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
	$xml->addChild('ci_wa_description', $settings['ci_wa_description']);
	$xml->addChild('ci_wa_display', $settings['ci_wa_display']);
	$xml->addChild('ci_wa_link', $settings['ci_wa_link']);
	$xml->addChild('ci_wa_avatar', $settings['ci_wa_avatar']);
	$xml->addChild('ci_wa_custom_avatar', $settings['ci_wa_custom_avatar']);
	$settings['ci_sound'] = isset($_POST['ci_sound']) ? 'true' : 'false';
	$xml->addChild('ci_sound', $settings['ci_sound']);
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