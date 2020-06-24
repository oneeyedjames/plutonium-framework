<?php

use Plutonium\AccessObject;
use Plutonium\Application\Module;

class ModuleTest extends ComponentTestCase {
	/*
	 * Tests that view objects are properly loaded.
	 */
	public function testGetView() {
		$module = $this->createModule();
		$module->initialize();

		$view = $module->getView();

		$this->assertEquals('blog', $view->module->name);
		$this->assertEquals('feed', $view->name);
	}

	/*
	 * Tests that view templates are properly located.
	 */
	public function testRender() {
		$module1 = $this->createModule();
		$module2 = $this->createModule('post');
		$module3 = $this->createModule('post', 'item');

		$module1->initialize();
		$module2->initialize();
		$module3->initialize();

		$output1 = $module1->render();
		$output2 = $module2->render();
		$output3 = $module3->render();

		$this->addFile('modules/blog/views/post/layouts/item.html.php', 'post item layout');

		$module4 = $this->createModule('post', 'item');
		$module4->initialize();

		$output4 = $module4->render();

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
