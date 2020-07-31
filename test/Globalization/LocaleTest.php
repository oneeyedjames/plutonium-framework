<?php

use Plutonium\AccessObject;
use Plutonium\Globalization\Locale;

class LocaleTest extends ComponentTestCase {
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
		$locale = Locale::parse([
			'language' => 'en'
		]);

		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = Locale::parse([
			'language' => 'en',
			'country'  => 'us'
		]);

		$this->assertEquals('en', $locale->language);
		$this->assertEquals('us', $locale->country);
	}

	/*
	 * Tests that locale objects are properly interpreted.
	 */
	public function testParseObject() {
		$locale = Locale::parse(new AccessObject([
			'language' => 'en'
		]));

		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = Locale::parse(new AccessObject([
			'language' => 'en',
			'country'  => 'us'
		]));

		$this->assertEquals('en', $locale->language);
		$this->assertEquals('us', $locale->country);
	}

	public function testCreate() {
		$locale = new Locale('en-us');

		$this->assertEquals('en-US', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEquals('US', $locale->country);

		$this->assertEquals('Hello, App!',
			$locale->localize('hello_world'));

		$locale->load('light', 'themes');
		$this->assertEquals('Hello, Theme!',
			$locale->localize('hello_world'));

		$locale->load('blog', 'modules');
		$this->assertEquals('Hello, Module!',
			$locale->localize('hello_world'));

		$locale->load('calendar', 'widgets');
		$this->assertEquals('Hello, Widget!',
			$locale->localize('hello_world'));
	}
}
