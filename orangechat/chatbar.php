<?php
session_start();
require_once 'common.php';
include "connect.php";
update_lastact();

if(!(isset($_GET['act'])) OR !(preg_match('/^(update_chat_bar|chat_friends_list)$/', $_GET['act']))) exit;

switch ($_GET['act']) {
	case 'update_chat_bar':
		update_chat_bar();
		break;
	
	case 'chat_friends_list':
		chat_friends_list();
		break;
}

function update_chat_bar() {
	$count = sizeof(getonlinecontacts($_SESSION['userid']));
	echo t('Chat').' ('.$count.')';
	exit;
}

function chat_friends_list() {
	$friends = getonlinecontacts($_SESSION['userid']);
	$count = sizeof($friends);
	if($count) {
		$result = null;
		foreach($friends as $friend) {
			$result .= '<a href="#" onclick="javascript:chatWith(\''.$friend.'\', \''.get_display_name($friend).'\');hide_chat_list();return false;" class="chat_boxes" ><li class="chat_boxes"><img src="___DIR___./../avatars/'.get_avatar($friend).'" style="max-width: 24px; max-height: 24px; width: 24px; height: 24px; border-radius: 50%; vertical-align: middle;" /> '.get_display_name($friend).'</li></a>';
		}
		// echo '<div class="sub chat_boxes">'.t('Chat').' ('.$count.')</div>';
		echo '<ul  class="chat_boxes">'.$result.'</ul>';
	} else {
		echo t('<div style="padding-bottom: 6px;">No online users.</div>');
	}
}
