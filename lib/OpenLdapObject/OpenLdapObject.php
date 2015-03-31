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

/**
 * Class OpenLdapObject
 *
 * Information and configuration about the library
 *
 * @package OpenLdapObject
 */
abstract class OpenLdapObject {
	/**
	 * ID Version
	 */
	const VERSION = '1.1.0dev';
	/**
	 * Release Date
	 */
	const DATE = '31/03/2015';

	/**
	 * True if the global strict mode is enable
	 * @var bool enable/disable
	 */
	private static $strict = true;

	/**
	 * Check if the global strict mode is enable
	 * @return bool
	 */
	public static function isStrict() {
		return self::$strict;
	}

	/**
	 * Enable the global strict mode
	 */
	public static function enableStrictMode() {
		self::$strict = true;
	}

	/**
	 * Disable the global strict mode
	 */
	public static function disableStrictMode() {
		self::$strict = false;
	}
}