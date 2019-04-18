<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Application\Widget;

class WidgetTest extends TestCase {
	const NAME = 'WidgetName';

	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'widgets' => [
				'widgetname' => [
					'widget.php' => '<?php',
					'layouts' => [
						'default.html.php' => 'default layout'
					]
				]
			]
		]);
	}

	public function testGetLayout() {
		$layout1 = $this->createWidget()->getLayout();
		$layout2 = $this->createWidget('item')->getLayout();

		$this->addFile('widgets/widgetname/layouts/item.html.php', 'item layout');

		$layout3 = $this->createWidget('item')->getLayout();

		$this->assertEquals(PU_PATH_BASE . '/widgets/widgetname/layouts/default.html.php', $layout1);
		$this->assertEquals(PU_PATH_BASE . '/widgets/widgetname/layouts/default.html.php', $layout2);
		$this->assertEquals(PU_PATH_BASE . '/widgets/widgetname/layouts/default.html.php', $layout3);
	}

	public function testRender() {
		$output1 = $this->createWidget()->render();
		$output2 = $this->createWidget('item')->render();

		$this->addFile('widgets/widgetname/layouts/item.html.php', 'item layout');

		$output3 = $this->createWidget('item')->render();

		$this->assertEquals('default layout', $output1);
		$this->assertEquals('default layout', $output2);
		$this->assertEquals('default layout', $output3);
	}

	protected function addFile($path, $data) {
		vfsStream::newFile($path)->at($this->directory)->setContent($data);
	}

	protected function createWidget($layout = 'default', $format = 'html') {
		$app = new ApplicationMock;
		$app->locale = new LocaleMock;
		$app->request = new AccessObject(compact('layout', 'format'));

		return new Widget(new AccessObject([
			'name' => self::NAME,
			'application' => $app
		]));
	}
}