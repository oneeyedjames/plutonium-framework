<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Globalization\Locale;

class LocaleTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'application' => [
				'locales' => [
					'en.xml' => '<trans type="application" name="http" lang="en"/>',
					'en-US.xml' => '<trans type="application" name="http" lang="en-US"/>'
				]
			]
		]);
	}

	/*
	 * Tests that locale string names are properly parsed.
	 */
	public function testParseString() {
		$locale = Locale::parse('en');

		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = Locale::parse('en-us');

		$this->assertEquals('en', $locale->language);
		$this->assertEquals('us', $locale->country);
	}

	/*
	 * Tests that locale arrays are properly interpreted.
	 */
	public function testParseArray() {
		$locale = Locale::parse(array(
			'language' => 'en'
		));

		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = Locale::parse(array(
			'language' => 'en',
			'country'  => 'us'
		));

		$this->assertEquals('en', $locale->language);
		$this->assertEquals('us', $locale->country);
	}

	/*
	 * Tests that locale objects are properly interpreted.
	 */
	public function testParseObject() {
		$locale = Locale::parse(new AccessObject(array(
			'language' => 'en'
		)));

		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = Locale::parse(new AccessObject(array(
			'language' => 'en',
			'country'  => 'us'
		)));

		$this->assertEquals('en', $locale->language);
		$this->assertEquals('us', $locale->country);
	}
}
