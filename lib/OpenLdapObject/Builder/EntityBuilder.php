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


use OpenLdapObject\Builder\Method\AdderBuilder;
use OpenLdapObject\Builder\Method\GetterBuilder;
use OpenLdapObject\Builder\Method\MethodBuilder;
use OpenLdapObject\Builder\Method\RemoverBuilder;
use OpenLdapObject\Builder\Method\SetterBuilder;
use OpenLdapObject\Manager\EntityAnalyzer;

class EntityBuilder {
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

        foreach($missingMethod as $method) {
            $before .= $this->getBuilder($method)->getMethodSrc();
        }

        $lines[$this->analyzer->getReflection()->getEndLine()-1] = $before . $after;

        file_put_contents($this->analyzer->getReflection()->getFileName(), implode('', $lines));
    }

	public function regenerateGetterSetter() {
		$methodList = $this->analyzer->listRequiredMethod();
		$fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());

		foreach($methodList as $method) {
            $methodBuilder = $this->getBuilder($method);
            $fileContent = str_replace($methodBuilder->getMethodName(), $methodBuilder->getMethodSrc(), $fileContent);
		}

		file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
	}

	public function cleanGetterSetter() {
		$methodList = $this->analyzer->listRequiredMethod();
		$fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());
		foreach($methodList as $method) {
			$methodBuilder = $this->getBuilder($method);
            $fileContent = str_replace($methodBuilder->getMethodName(), '', $fileContent);
		}
		file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
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

    /**
     * @param $method
     * @return MethodBuilder
     */
    protected function getBuilder($method) {
        switch($method['type']) {
            case EntityAnalyzer::GETTER:
                return new GetterBuilder($method['column']);
            case EntityAnalyzer::SETTER:
                return new SetterBuilder($method['column']);
            case EntityAnalyzer::ADDER:
                return new AdderBuilder($method['column']);
            case EntityAnalyzer::REMOVER:
                return new RemoverBuilder($method['column']);
        }
    }
}