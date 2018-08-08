<?php
require_once __DIR__ . '/database.php';
#require_once __DIR__ . '/fbscraper_group_loop.php';

class myProcess {
	public function __construct(String $user_id, String $name, String $email, String $fb_account, String $pw){
		$this->user_id = $user_id;
		$this->name1 = $name;
		$this->email = $email;
		$this->fb_account = $fb_account;
		$this->pw = $pw;
	}

	public function run(){
			#$this->fgl = new fbscraper_group_loop();
			#$this->fgl->getGroupItems($this->user_id, $this->name1, $this->email, $this->fb_account, $this->pw);
			$comando="php ./fbscraper_group_loop.php " . $this->user_id . " " . str_replace(" ", "%20", $this->name1) . " " . $this->email . " " . $this->fb_account . " " . $this->pw;
			print $comando;
			#shell_exec("/usr/bin/nohup ".$comando." >/dev/null 2>&1 &");
			shell_exec("/usr/bin/nohup ".$comando." >/var/log/WUM/" . $this->user_id . " 2>&1 &");
			#shell_exec($comando." >/dev/null 2>&1 &");
	}
}

$db = new database();
$users = $db->getUsers();


print (var_dump($users));
foreach ($users as $user) {
	$user_id = $user['id'];
	$name = $user['name'];
	$email = $user['email'];
	$fb_account = $user['fb_account'];
	$pw = $user['pw'];
	$workers[$user_id]=new myProcess($user_id, $name, $email, $fb_account, $pw);
	$workers[$user_id]->run();
}
while (1) {
	sleep(1000);
}
