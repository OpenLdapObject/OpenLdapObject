<?php
/*
 * This file bootstraps the test environment.
 * It is a clone of TestInit of Doctrine/annotations
 */
error_reporting(E_ALL | E_STRICT);

// register silently failing autoloader
spl_autoload_register(function($class) {
    if (0 === strpos($class, 'OpenLdapObject\Tests\\')) {
        $path = __DIR__.'/../../'.strtr($class, '\\', '/').'.php';
        if (is_file($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});

require_once __DIR__ . "/../../../vendor/autoload.php";

require_once __DIR__ . '/TestConfiguration.php';
?>