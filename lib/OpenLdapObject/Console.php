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

namespace OpenLdapObject;


use OpenLdapObject\Builder\EntityBuilder;

class Console {
    const VERSION = '1.1.0alpha';
    const DATE = '2015-03-22';

    public function main($argc, array $argv) {
        if($argc > 1 && $argv[1] == 'generate') {
            if($argc > 2 && $argc < 5) {
                $entity = $argv[2];
                $path = (count($argc) == 4 ? $argv[3] : null);
                $this->generate($entity, $path);
            } else {
                echo 'Usage php OpenLdapObject.php generate Entity [pathToEntityFile]' . PHP_EOL . PHP_EOL;
            }
        } elseif($argc > 1 && $argv[1] == 'version') {
            $this->version();
        } else {
            echo 'Usage php OpenLdapObject.php command Entity [options]' . PHP_EOL . PHP_EOL;
            echo 'Commands:' . PHP_EOL;
            echo 'generate      - Add getters and setters in an entity' . PHP_EOL;
            echo 'version       - Get version information' . PHP_EOL;
        }
    }

    public function generate($entityClassName, $pathToEntity) {
        $entityClassName = str_replace('/', '\\', $entityClassName);
        if(is_null($pathToEntity)) {
            echo 'No path is defined, try to autoload class...' . PHP_EOL . PHP_EOL;

            spl_autoload_call($entityClassName);
            if(!class_exists($entityClassName)) {
                echo 'Unable to autoload the entity class, try with a path' . PHP_EOL;
                echo '  Example: php OpenLdapObject.php generate /Namespace/Entity Namespace/Entity.php';
                exit();
            } else {
                echo 'Class is load.' . PHP_EOL;
            }
        } else {
            if(!file_exists($pathToEntity)) {
                echo 'The file ' . $pathToEntity . ' don\'t exist';
                exit();
            }
            require_once $pathToEntity;
            if(!class_exists($entityClassName)) {
                echo 'The File ' . $pathToEntity . ' don\'t define the class ' . $entityClassName . PHP_EOL;
                exit();
            }
        }

        echo 'Generate entity...' . PHP_EOL;
        $builder = new EntityBuilder($entityClassName);
        $builder->completeEntity();

        echo 'Entity is generate.' . PHP_EOL;
    }

    public function version() {
        echo 'Version ' . Console::VERSION . ' (' . CONSOLE::DATE . ')' . PHP_EOL;
    }
}
