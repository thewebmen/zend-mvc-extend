<?php

if(version_compare(PHP_VERSION, '5.3.0') < 0) {
    throw new RuntimeException('PHP 5.3.0 or higher is required to run this application');
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define ( 'ROOT_PATH', realpath(dirname ( dirname ( __FILE__ ) ) ));
define ( 'APPLICATION_PATH', ROOT_PATH . '/inc/application' );
define ( 'CORE_MODULE_PATH', ROOT_PATH . '/inc/application/code/core/' );
define ( 'LOCAL_MODULE_PATH', ROOT_PATH . '/inc/application/code/local/' );
define ( 'CONFIG_PATH', APPLICATION_PATH . '/config' );
define ( 'DESIGN_PATH', APPLICATION_PATH . '/design' );
define ( 'LIB_PATH', ROOT_PATH . '/inc/library' );
define ( 'DATA_PATH', ROOT_PATH . '/inc/data' );

require_once CORE_MODULE_PATH. 'Twm/Core/Application.php';
$application = new Twm_Core_Application ( APPLICATION_ENV, CONFIG_PATH . '/application.xml' );
//$application = new Twm_Core_Application ( APPLICATION_ENV, CONFIG_PATH . '/application.old.ini' );
$application->bootstrap ()->run();