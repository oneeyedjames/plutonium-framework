<?php

use Plutonium\AccessObject;
use Plutonium\Application\Module;
use Plutonium\Application\View;

class ViewTest extends ComponentTestCase {
	/*
	 * Tests that layout templates are properly located.
	 */
	public function testGetLayout() {
		$path = PU_PATH_BASE . '/modules/blog';

		$module1 = $this->createModule();
		$module2 = $this->createModule('post');
		$module3 = $this->createModule('post', 'item');

		$module1->initialize();
		$module2->initialize();
		$module3->initialize();

		$layout1 = $module1->getView()->getLayout();
		$layout2 = $module2->getView()->getLayout();
		$layout3 = $module3->getView()->getLayout();

		$this->addFile('modules/blog/views/post/layouts/item.html.php', 'post item layout');

		$layout4 = $module3->getView()->getLayout();

		$this->assertEquals($path . '/views/feed/layouts/default.html.php', $layout1);
		$this->assertEquals($path . '/views/post/layouts/default.html.php', $layout2);
		$this->assertEquals($path . '/views/post/layouts/default.html.php', $layout3);
		$this->assertEquals($path . '/views/post/layouts/item.html.php', $layout4);
	}

	/*
	 * Tests that layout templates are properly rendered.
	 */
	public function testRender() {
		$module1 = $this->createModule();
		$module2 = $this->createModule('post');
		$module3 = $this->createModule('post', 'item');
		$module4 = $this->createModule('post', 'item');

		$module1->initialize();
		$module2->initialize();
		$module3->initialize();
		$module4->initialize();

		$output1 = $module1->getView()->render();
		$output2 = $module2->getView()->render();
		$output3 = $module3->getView()->render();

		$this->addFile('modules/blog/views/post/layouts/item.html.php', 'post item layout');

		$output4 = $module4->getView()->render();

		$this->assertEquals('feed default layout', $output1);
		$this->assertEquals('post default layout', $output2);
		$this->assertEquals('post default layout', $output3);
		$this->assertEquals('post item layout', $output4);
	}

	protected function createModule($resource = 'feed', $layout = 'default') {
		$app = $this->createApplication($layout);
		$app->request->resource = $resource;

		return new Module(new AccessObject([
			'name' => 'blog',
			'application' => $app
		]));
	}
}
