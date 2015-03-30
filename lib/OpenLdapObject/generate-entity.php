#!/usr/bin/php
<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

if(php_sapi_name() !== 'cli') {
    echo 'This Script must be run in a CLI.';
    exit();
}

if(strpos(__DIR__, 'vendor') === false) {
    // We are in a local development
    $vendorDirectory = __DIR__ . '/../../vendor/';
} else {
    // We are in vendor directory
    $vendorDirectory = __DIR__ . '/../../../../';
}

require_once $vendorDirectory . 'autoload.php';

$getOpt = new Getopt(array(
    new Option('e', 'entity', Getopt::REQUIRED_ARGUMENT),
    new Option('f', 'file', Getopt::OPTIONAL_ARGUMENT),
    new Option(null, 'regenerate', Getopt::OPTIONAL_ARGUMENT),
    new Option(null, 'clean', Getopt::OPTIONAL_ARGUMENT),
    new Option(null, 'help', Getopt::NO_ARGUMENT),
    new Option(null, 'version', Getopt::NO_ARGUMENT)
));
$getOpt->parse();

if($getOpt->getOption('help')) {
    echo $getOpt->getHelpText();
} else if($getOpt->getOption('version')) {
    echo 'Version 1.1.0alpha3 (2015-03-30)' . PHP_EOL;
} else {
    if($getOpt->getOption('regenerate')) {
        $command = new \OpenLdapObject\Command\ReGenerateCommand($getOpt->getOptions());
    } elseif($getOpt->getOption('clean')) {
        $command = new \OpenLdapObject\Command\CleanCommand($getOpt->getOptions());
    } else {
        $command = new \OpenLdapObject\Command\GenerateCommand($getOpt->getOptions());
    }

    $command->exec();
}
?>