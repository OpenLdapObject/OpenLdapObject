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

namespace OpenLdapObject\Command;


use OpenLdapObject\Builder\EntityBuilder;

class GenerateCommand implements Command {
    private $args;

    public function __construct(array $args) {
        $this->args = $args;
    }

    public function exec() {
        $entityClassName = str_replace('/', '\\', $this->args['entity']);
        if(!array_key_exists('file', $this->args)) {
            $isLoad = $this->autoload($entityClassName);
        } else {
            $isLoad = $this->load($entityClassName, $this->args['file']);
        }

        if($isLoad) {
            $this->generate($entityClassName);
        }
    }

    protected function autoload($className) {
        echo 'No path is defined, try to autoload class...' . PHP_EOL . PHP_EOL;

        if(!class_exists($className)) spl_autoload_call($className);
        if(!class_exists($className)) {
            echo 'Unable to autoload the entity class('.$className.'), try with a path' . PHP_EOL;
            return false;
        } else {
            echo 'Class is load.' . PHP_EOL;
            return true;
        }
    }

    protected function load($className, $file) {
        if(!file_exists($file)) {
            echo 'The file ' . $file . ' don\'t exist';
            return true;
        } else {
            require_once $file;
            if(!class_exists($className)) {
                echo 'The File ' . $file . ' don\'t define the class ' . $className . PHP_EOL;
                return false;
            }
            return true;
        }
    }

    protected function generate($className) {
        echo 'Generate entity...' . PHP_EOL;
        $builder = new EntityBuilder($className);
        $builder->completeEntity();

        echo 'Entity is generate.' . PHP_EOL;
    }
} 