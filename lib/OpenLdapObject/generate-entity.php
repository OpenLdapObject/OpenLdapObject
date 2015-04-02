#!/usr/bin/php
<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Pierre Pélisset
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

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
    echo 'Version '.\OpenLdapObject\OpenLdapObject::VERSION.' ('.\OpenLdapObject\OpenLdapObject::DATE.')' . PHP_EOL;
} else if($getOpt->getOption('entity')) {
    if($getOpt->getOption('regenerate')) {
        $command = new \OpenLdapObject\Command\ReGenerateCommand($getOpt->getOptions());
    } elseif($getOpt->getOption('clean')) {
        $command = new \OpenLdapObject\Command\CleanCommand($getOpt->getOptions());
    } else {
        $command = new \OpenLdapObject\Command\GenerateCommand($getOpt->getOptions());
    }

    $command->exec();
} else {
	echo $getOpt->getHelpText();
}
?>