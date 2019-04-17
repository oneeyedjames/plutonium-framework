<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Globalization\Locale;

class LocaleTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'locales' => [
				'en.xml' => '<trans type="application" name="http" lang="en"/>',
				'en-US.xml' => '<trans type="application" name="http" lang="en-US"/>'
			]
		]);
	}

	public function testConstruct() {
		$locale = new Locale('en');

		$this->assertEquals('en', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = new Locale(array(
			'language' => 'en'
		));

		$this->assertEquals('en', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = new Locale(new AccessObject(array(
			'language' => 'en'
		)));

		$this->assertEquals('en', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEmpty($locale->country);

		$locale = new Locale('en-us');

		$this->assertEquals('en-US', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEquals('US', $locale->country);

		$locale = new Locale(array(
			'language' => 'en',
			'country'  => 'US'
		));

		$this->assertEquals('en-US', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEquals('US', $locale->country);

		$locale = new Locale(new AccessObject(array(
			'language' => 'en',
			'country'  => 'US'
		)));

		$this->assertEquals('en-US', $locale->name);
		$this->assertEquals('en', $locale->language);
		$this->assertEquals('US', $locale->country);
	}
}
