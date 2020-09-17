<?php
session_start();
require_once 'common.php';
update_lastact();
if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); } 
if ($_GET['action'] == "sendchat") { sendChat(); } 
if ($_GET['action'] == "closechat") { closeChat(); } 
if ($_GET['action'] == "startchatsession") { startChatSession(); } 

if (!isset($_SESSION['chatHistory'])) {
	$_SESSION['chatHistory'] = array();	
}

if (!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();	
}
//////////////////////////////////////////////
function chatHeartbeat() {
	include "connect.php";
	$sql = "select * from chat where (chat.to = '".$_SESSION['userid']."' AND recd = 0) order by id ASC";
	$query = mysqli_query($conn,$sql);
	$items = '';

	$chatBoxes = array();

	while ($chat = mysqli_fetch_array($query)) {

		if (!isset($_SESSION['openChatBoxes'][$chat['from']]) && isset($_SESSION['chatHistory'][$chat['from']])) {
			$items = $_SESSION['chatHistory'][$chat['from']];
		}

		$chat['message'] = sanitize($chat['message']);
		$chat['displayname'] = get_display_name($chat['from']);
		$items .= <<<EOD
					   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$chat['from']])) {
		$_SESSION['chatHistory'][$chat['from']] = '';
	}

	$chat['displayname'] = get_display_name($chat['from']);
	$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['from']]);
		$_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];
	}

	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date(TIMEFORMAT, strtotime($time));

			$message = t('Sent at')." $time";
			if ($now > 180) {
				$displayname = get_display_name($chatbox);
				$items .= <<<EOD
{
"s": "2",
"f": "$chatbox",
"d": "{$displayname}",
"m": "{$message}"
},
EOD;

	if (!isset($_SESSION['chatHistory'][$chatbox])) {
		$_SESSION['chatHistory'][$chatbox] = '';
	}

	$displayname = get_display_name($chatbox);
	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
"s": "2",
"f": "$chatbox",
"d": "{$displayname}",
"m": "{$message}"
},
EOD;
			$_SESSION['tsChatBoxes'][$chatbox] = 1;
		}
		}
	}
}

	$sql = "update chat set recd = 1 where chat.to = '".$_SESSION['userid']."' and recd = 0";
	$query = mysqli_query($conn,$sql);

	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}
//////////////////////////////////////////////
function chatBoxSession($chatbox) {
	
	$items = '';
	
	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}
//////////////////////////////////////////////
function startChatSession() {
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{
		"username": "<?php echo $_SESSION['userid']; ?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}
//////////////////////////////////////////////
function sendChat() {
    include "connect.php";
	$from = $_SESSION['userid'];
	$to = $_POST['to'];
	$message = $_POST['message'];
	if(function_exists('hook_message_text') AND hook_message_text($message)!='') $message = hook_message_text($message);

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());
	
	$messagesan = sanitize(stripslashes($message));

	if (!isset($_SESSION['chatHistory'][$_POST['to']])) {
		$_SESSION['chatHistory'][$_POST['to']] = '';
	}

	$displayname = t('Me'); //get_display_name(getuserid());
	$_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
					   {
			"s": "1",
			"f": "{$to}",
			"d": "{$displayname}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][$_POST['to']]);

	$sql = "insert into chat (chat.from,chat.to,message,sent) values ('".mysqli_real_escape_string($conn,$from)."', '".mysqli_real_escape_string($conn,$to)."','".mysqli_real_escape_string($conn,$message)."',NOW())";
	$query = mysqli_query($conn,$sql);
	if(function_exists('hook_message_sent')) hook_message_sent($from, $to, $message, time(), mysqli_insert_id($conn));
	echo "1";
	exit(0);
}
//////////////////////////////////////////////
function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}
//////////////////////////////////////////////
function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br />",$text);
	return $text;
}
