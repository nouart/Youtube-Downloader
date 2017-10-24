<?php
session_start();

require "vendor/autoload.php";
require "classes/Processor.php";
require "classes/Abstractor.php";

use Noodlehaus\Config;
use myPHPnotes\Processor;
use myPHPnotes\Abstractor;
//abstratctor

$abstractor = new Abstractor();
//configuration
$config = new Config(__DIR__. '/../config/'.file_get_contents(__DIR__.'/../mode.php').'.php');

//Processor
$processor = new Processor($config);

//Twig View
$loader = new Twig_loader_Filesystem('views');
$twig = new Twig_Environment($loader);
$twig->addGlobal('config', $config);