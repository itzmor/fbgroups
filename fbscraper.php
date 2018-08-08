<?php
#use Facebook\WebDriver\Remote\DesiredCapabilities;
#use Facebook\WebDriver\Remote\RemoteWebDriver;
#use Facebook\WebDriver\Chrome\ChromeOptions;
require_once('vendor/autoload.php');
require_once('vendor/facebook/webdriver/lib/chrome/ChromeOptions.php');
require_once('vendor/facebook/webdriver/lib/remote/DesiredCapabilities.php');
require_once('vendor/facebook/webdriver/lib/remote/RemoteWebDriver.php');
#use Facebook\WebDriver;

class fbscraper
{
    public function connectAndLoginFb(String $account, String $pw)
    {
	$host = 'http://itzik-H110M-S2H:4444/wd/hub'; // this is the default

	$options = new ChromeOptions();
	$prefs = array('profile.default_content_setting_values.notifications' => 2);
	$options->setExperimentalOption('prefs', $prefs);

	$capabilities = DesiredCapabilities::chrome(); 
	$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

	$driver = RemoteWebDriver::create($host, $capabilities, 5000);

	#$email2 = "ramishavit01@walla.com";
	#$pass2 = "qwe123";

	$driver->get('https://facebook.com/login');
	$mail = $driver->findElement(WebDriverBy::cssSelector('#email'));
	$mail->sendKeys($account);
	$pass = $driver->findElement(WebDriverBy::cssSelector('#pass'));
	$pass->sendKeys($pw);

	$driver->findElement(WebDriverBy::cssSelector('#loginbutton'))->click();
	return ($driver);
    }
}
