<?php
require_once('vendor/autoload.php');
require_once('fbscraper.php');
require_once __DIR__ . '/database.php';

use Facebook\WebDriver;
use fbscraper;

class fbscraper_group_loop extends fbscraper
{
	public function getGroupItems(String $user_id, String $name, String $email, String $fb_account, String $pw){
                $this->user_id = $user_id;
                $this->name = $name;
                $this->email = $email;
                $this->fb_account = $fb_account;
                $this->pw = $pw;
		$driver = $this->connectAndLoginFb($this->fb_account, $this->pw);
		$db = new database();
		$searches = $db->getSearchesForUser ($this->user_id);
		$groups = $db->getGroups();
		$mygroups = Array();
		foreach ($groups as $group) {
			$mygroups[$group['id']] = $group['name'];
		}

		while (1) {
			foreach ($searches as $search) {
				$user_id = $search['user_id'];
				$group_id = $search['group_id'];
				$query = $search['text'];
				$comment_text = $search['comment_text'];
				$date = date("Y-m");
				$fb_link='https://facebook.com/groups/' . $group_id . '/search/?query='. str_replace(" ", "%20", $query) . '&filters_rp_creation_time=%7B%22name%22%3A%22creation_time%22%2C%22args%22%3A%22%7B%5C%22start_month%5C%22%3A%5C%22' . $date . '%5C%22%2C%5C%22end_month%5C%22%3A%5C%22' . $date . '%5C%22%7D%22%7D&filters_rp_chrono_sort=%7B%22name%22%3A%22chronosort%22%2C%22args%22%3A%22%22%7D';
				$fb_link_group='https://facebook.com/groups/' . $group_id;
				$this->logger("Looking for - " . $query . ", in group - " . $mygroups[$group_id]);
				$driver->get($fb_link);
	
				usleep(2500000);


				$dom = new DOMDocument();
				$dom->loadHTML($driver->getPageSource());

				$p_date="";
				$values = array();
				$first_date_pos = strpos($driver->getPageSource(), 'timestampContent');
				if ($first_date_pos) {
					$date_area = substr($driver->getPageSource(), $first_date_pos);
					$first_date_pos_2 = strpos ($date_area, ">");
					$first_date_pos_3 = strpos ($date_area, "<");
					$p_date = substr($date_area, $first_date_pos_2 + 1, $first_date_pos_3 - $first_date_pos_2 - 1);
					if ($p_date == "Just now" || $p_date == "1 min" || $p_date == "2 mins" || $p_date == "3 mins" || $p_date == "4 mins"
						|| $p_date == "5 mins" || $p_date == "6 mins") {
					#if ($p_date == "4 August at 21:51") {
						$this->logger ("\nFound............................\n\n");
						$this->sendEmail($user_id, $query, $comment_text, $fb_link_group, $this->email, $mygroups[$group_id]);
					}
				}
			}
			sleep(60 * 5);
		}
		$driver->close();
	}

	private function sendEmail($user_id, $query, $comment_text, $fb_link_group, $email, $group_name) {
		#$text_to_notify = "In group:\n" . $group_id . "\nresult was found just now for the keyword:\n" . $query . "\nthe comment to copy:\n" . $comment_text . "\nin the following link:\n" . $fb_link_group;
		$text_to_notify = "בקבוצת הפייסבוק:\n\n" . $group_name . "\n\nנמצא פוסט עם מילת מפתח:\n\n" . $query . "\n\nההודעה להעתיק:\n\n" . $comment_text . "\n\nללינק הבא:\n\n" . $fb_link_group;
		$this->logger("Sending mail - " . $text_to_notify);
		$msg = $text_to_notify;
		$msg = wordwrap($msg,70);
		#mail("itzikfbrunner@gmail.com","Item was found in Facebook",$msg);
		#mail($email, "Item was found in Facebook",$msg);
		mail($email, "מייל אוטומטי - נמצא פוסט מעניין בפייסבוק",$msg);
	}
	private function logger(String $message) {
		print date("Y-m-d H:i:s") . " - " . $message . "\n";
	}
}

$fgl = new fbscraper_group_loop();
$fgl->getGroupItems($argv[1], $argv[2], $argv[3], $argv[4], $argv[5]);
?>
