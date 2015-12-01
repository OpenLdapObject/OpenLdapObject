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

namespace OpenLdapObject\Builder\Method;

use OpenLdapObject\Utils;

abstract class MethodBuilder {
    private static $space = '    ';
    private static $eol = PHP_EOL;
    private static $visibility = 'public';
    private $property;

    public function __construct($property) {
        $this->property = $property;
    }

    public function getMethodSrc() {
        $template = static::$template;
        $template = str_replace('<visibility>', self::$visibility, $template);
        $template = str_replace('<space>', self::$space, $template);
        $template = str_replace('<eol>', self::$eol, $template);
        $template = str_replace('<column|capitalize>', Utils::capitalize($this->property), $template);
        $template = str_replace('<column>', $this->property, $template);
        return $template;
    }

    public function getProperty() {
        return $this->property;
    }

    public abstract function getMethodName();
}