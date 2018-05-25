<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
define('REQUEST_MICROTIME', microtime(true));
define('ROOT_PATH', __DIR__);

define('GIT_HEAD', shell_exec('git rev-parse HEAD'));

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

date_default_timezone_set ('Europe/Berlin');

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
