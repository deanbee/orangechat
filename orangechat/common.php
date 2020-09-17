<?php
session_start();
/*Choose your language*/
define('LANGUAGE', 'en');
/*Choose your Theme*/
define('THEME', 'dark');
define('TIMEFORMAT', 'm/d/y g:iA');
function getcontacts($uid) {
    include "connect.php";
	$userid = $_SESSION['userid'];
    $qry = mysqli_query($conn, "SELECT userid FROM members WHERE online = '1' AND userid != '$userid'");
	$users = array();
	if(mysqli_num_rows($qry)) {
		while($row = mysqli_fetch_array($qry)) {
			$users[] = $row['userid'];
		}
	}
	return $users;
}

function get_display_name($uid) {
    include "connect.php";
	$qry = mysqli_query($conn, "SELECT username FROM members WHERE userid = '$uid'");
	if(mysqli_num_rows($qry)) {
		$fetch = mysqli_fetch_array($qry);
		return $fetch['username'];
	} else return $fetch['username'];
}
function get_avatar($uid) {
    include "connect.php";
    $qry = mysqli_query($conn, "SELECT avatar FROM members WHERE userid = '$uid'");
    if (mysqli_num_rows($qry)) {
        $get = mysqli_fetch_array($qry);
        if ($get['avatar'] == '') {
            $get['avatar'] = "default.jpg";
        }
        return $get['avatar'];
    } else return null;
}
function is_online($userid) {
    include "connect.php";
	$qry = mysqli_query($conn,"SELECT time FROM chat_lastactivity WHERE user = '$userid' ORDER BY id DESC LIMIT 1");
	if(!mysqli_num_rows($qry)) return false;
	else {
		$fetch = mysqli_fetch_array($qry);
		$lastact = $fetch['time'];
		$limit = strtotime("-20 seconds"); // update interval is 9 seconds
		return ($lastact>$limit);
	}
}
function getonlinecontacts($userid) {
	$return = array();
	$friends = getcontacts($userid);
	foreach($friends as $friend) {
		if(is_online($friend)) {
			$return[] = $friend;
		}
	}
	return $return;
}
function hook_message_text($message) {
	return $message;
}
function hook_message_sent($from, $to, $message, $time, $message_id) {
	// do nothing
}
function base() {
	echo "/orangechat/";
}
function update_lastact() {
    include "connect.php";
	$userid = $_SESSION['userid'];
	$time = time();
	$qry = mysqli_query($conn,"SELECT * FROM chat_lastactivity WHERE user='$userid' ORDER BY id DESC LIMIT 1");
	if(mysqli_num_rows($qry)) {
		mysqli_query($conn,"UPDATE chat_lastactivity SET time='$time' WHERE user='$userid'");
	} else {
		mysqli_query($conn,"INSERT INTO chat_lastactivity (`user`, `time`) VALUES ('$userid', '$time');");
	}
}

function t($string) {
	if(!file_exists('lang/'.LANGUAGE.'.xml')) return $string;
	$xml = simplexml_load_file('lang/'.LANGUAGE.'.xml');
	$result = $xml->xpath('/lang/message[@original="'.$string.'"]');
	if(isset($result[0][0])) {
		return trim($result[0][0]);	
	} else {
		return $string;
	}
}

function info() {
	echo <<<HTML
/*
	OrangeChat

	Programming, integration support and administration panel by Jefrey S. Santos (jefreysobreira[at]gmail[dot]com)
    Updated to work with mysqli and tweeked some areas, added chime to new message off focus
	Chat boxes by Anant Garg (anantgarg.com)
*/


HTML;
}