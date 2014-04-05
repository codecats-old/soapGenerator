<?php
/*
basic colors (python syntax)
HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
*/
class Detector {
	static $found = false;
}
require './vendor/autoload.php';

for ($i = 1 ; $i < count($argv); $i++) {
	
	
	$content = '';
	$cwd = getcwd();
	if (is_file('./in/' . $argv[$i]))$content =  file_get_contents('./in/' . $argv[$i]);
	else echo "\033[93m 404 Not found file in \"$cwd/in/$argv[$i]\" \033[0m\n";
	require_once './in/' . $argv[$i];

	preg_replace_callback('@class (.+) @U', function ($found) {
		$class = $found[1];
		
		$autodiscover = new Zend\Soap\AutoDiscover();
		echo "\033[42m 200 OK for \"$class\" \033[0m\n";
		$defaultServer = 'http://localhost/server.php';
		$line = readline("Put server name [$defaultServer]: ");
		
		readline_add_history($line);
		$line = readline_info();
		$line = trim($line['line_buffer']);
		if (empty($line)) $line = $defaultServer;

		$autodiscover->setClass($class)->setUri($line);

		$wsdl = $autodiscover->generate();
		//$autodiscover->toXml() 
		
		$wsdl->dump("./out/file-$class.wsdl");
		echo "\033[42m \"$class\" done. \033[0m\n";
		
		Detector::$found = true;
	}, $content);

	if (Detector::$found === false) {
		
		echo "\033[91m 404 Not found any class in \"$cwd/in/$argv[$i]\" \033[0m\n";
	}
	Detector::$found = false;
}
/*
$autodiscover = new Zend\Soap\AutoDiscover();
$autodiscover->setClass('Books')
             ->setUri('http://localhost/server.php');

$wsdl = $autodiscover->generate();
//$autodiscover->toXml() 
echo "\033[42m 200 OK \033[0m\n";
$wsdl->dump("./out/file-$argv[1].wsdl");
*/
