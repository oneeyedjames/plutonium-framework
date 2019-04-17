<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\Application\Widget;

class WidgetTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'widgets' => [
				'widgetname' => [
					'widget.php' => '<?php',
					'layouts' => []
				]
			]
		]);
	}

	public function testGetPath() {
		$path = Widget::getLocator()->getPath('WidgetName');
		$this->assertTrue(file_exists($path));
	}

	public function testGetFile() {
		$file = Widget::getLocator()->getFile('WidgetName', 'widget.php');
		$this->assertTrue(file_exists($file));
	}
}