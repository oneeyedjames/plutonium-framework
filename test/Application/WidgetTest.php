<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Application\Widget;

class WidgetTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'widgets' => [
				'widgetname' => [
					'widget.php' => '<?php',
					'layouts' => [
						'default.html.php' => ''
					]
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

	public function testGetLayout() {
		$widget = new Widget(new AccessObject([
			'name' => 'WidgetName',
			'application' => (object) [
				'locale' => new LocaleMock
			]
		]));

		$request = new AccessObject([
			'layout' => 'item',
			'format' => 'html'
		]);

		$layout = $widget->getLayout($request);

		$this->assertEquals(PU_PATH_BASE . '/widgets/widgetname/layouts/default.html.php', $layout);
	}
}