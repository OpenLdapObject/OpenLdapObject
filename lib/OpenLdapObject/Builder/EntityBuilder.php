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

namespace OpenLdapObject\Builder;


use OpenLdapObject\Manager\EntityAnalyzer;
use OpenLdapObject\Utils;

class EntityBuilder {
    private static $space = '    ';
    private static $eol = PHP_EOL;
    private static $getterVisibility = 'public';
    private static $setterVisibility = 'public';
    private static $getterTemplate = '<space><visibility> function get<column|capitalize>() {<eol><space><space>return $this-><column>;<eol><space>}<eol><eol>';
    private static $setterTemplate = '<space><visibility> function set<column|capitalize>($value) {<eol><space><space>$this-><column> = $value;<eol><space><space>return $this;<eol><space>}<eol><eol>';
    private static $adderTemplate = '<space><visibility> function add<column|capitalize>($value) {<eol><space><space>$this-><column>->add($value);<eol><space><space>return $this;<eol><space>}<eol><eol>';
    private static $removerTemplate = '<space><visibility> function remove<column|capitalize>($value) {<eol><space><space>$this-><column>->removeElement($value);<eol><space><space>return $this;<eol><space>}<eol><eol>';


    private $className;
	/**
	 * @var EntityAnalyzer
	 */
    private $analyzer;

    public function __construct($className) {
        $this->className = $className;
        $this->analyzer = EntityAnalyzer::get($this->className);
    }

    public function completeEntity() {
        $missingMethod = $this->analyzer->listMissingMethod();

        $lines = file($this->analyzer->getReflection()->getFileName());

        $lineToSet = $lines[$this->analyzer->getReflection()->getEndLine()-1];

        $endClassPos = strrpos($lineToSet, '}');

        $before = substr($lineToSet, 0, $endClassPos-2);
        $after = substr($lineToSet, $endClassPos-2);

        foreach($missingMethod as $data) {
            switch($data['type']) {
                case EntityAnalyzer::GETTER:
                    $before .= $this->createGetter($data['column']);
                    break;
                case EntityAnalyzer::SETTER:
                    $before .= $this->createSetter($data['column']);
                    break;
                case EntityAnalyzer::ADDER:
                    $before .= $this->createAdder($data['column']);
                    break;
                case EntityAnalyzer::REMOVER:
                    $before .= $this->createRemover($data['column']);
                    break;
            }
        }

        $lines[$this->analyzer->getReflection()->getEndLine()-1] = $before . $after;

        file_put_contents($this->analyzer->getReflection()->getFileName(), implode('', $lines));
    }

	public function regenerateGetterSetter() {
		$methodList = $this->analyzer->listRequiredMethod();
		$fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());

		foreach($methodList as $data) {
			switch($data['type']) {
				case EntityAnalyzer::GETTER:
					$methodName = 'get' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), $this->createGetter($data['column']), $fileContent);
					break;
				case EntityAnalyzer::SETTER:
					$methodName = 'set' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), $this->createSetter($data['column']), $fileContent);
					break;
				case EntityAnalyzer::ADDER:
					$methodName = 'add' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), $this->createAdder($data['column']), $fileContent);
					break;
				case EntityAnalyzer::REMOVER:
					$methodName = 'remove' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), $this->createRemover($data['column']), $fileContent);
					break;
			}
		}

		file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
	}

	public function cleanGetterSetter() {
		$methodList = $this->analyzer->listRequiredMethod();
		$fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());

		foreach($methodList as $data) {
			switch($data['type']) {
				case EntityAnalyzer::GETTER:
					$methodName = 'get' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), '', $fileContent);
					break;
				case EntityAnalyzer::SETTER:
					$methodName = 'set' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), '', $fileContent);
					break;
				case EntityAnalyzer::ADDER:
					$methodName = 'add' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), '', $fileContent);
					break;
				case EntityAnalyzer::REMOVER:
					$methodName = 'remove' . Utils::capitalize($data['column']);
					$fileContent = str_replace($this->getMethodSrc($methodName), '', $fileContent);
					break;
			}
		}

		file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
	}

    public function createGetter($property) {
        return $this->buildMethod(self::$getterTemplate, self::$getterVisibility, $property);
    }

    public function createSetter($property) {
        return $this->buildMethod(self::$setterTemplate, self::$setterVisibility, $property);
    }

    public function createAdder($property) {
        return $this->buildMethod(self::$adderTemplate, self::$setterVisibility, $property);
    }

    public function createRemover($property) {
        return $this->buildMethod(self::$removerTemplate, self::$setterVisibility, $property);
    }

    protected function buildMethod($template, $visibility, $property) {
        $template = str_replace('<visibility>', $visibility, $template);
        $template = str_replace('<space>', self::$space, $template);
        $template = str_replace('<eol>', self::$eol, $template);
        $template = str_replace('<column|capitalize>', Utils::capitalize($property), $template);
        $template = str_replace('<column>', $property, $template);

        return $template;
    }

	protected function getMethodSrc($methodName, $file = null) {
		if(is_null($file)) {
			$file = file($this->analyzer->getReflection()->getFileName());
		}

		try {
			$startLine = $this->analyzer->getReflection()->getMethod($methodName)->getStartLine() - 1;
			$endLine = $this->analyzer->getReflection()->getMethod($methodName)->getEndLine()+1;
		} catch(\ReflectionException $e) {
			return '';
		}
		$length = $endLine - $startLine;

		return implode('', array_slice($file, $startLine, $length));
	}
}

?>