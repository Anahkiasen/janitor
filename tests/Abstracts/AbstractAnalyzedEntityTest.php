<?php
namespace Janitor\Abstracts;

use Janitor\Entities\View;
use Janitor\TestCases\JanitorTestCase;
use Symfony\Component\Finder\SplFileInfo;

class AbstractAnalyzedEntityTest extends JanitorTestCase
{
	public function testCanGetUsageMatrixAsString()
	{
		$view = $this->getDummyEntity();

		$pattern = strtr('{folder}/{file}|{folder}/{basename}|{dotfolder}.{basename}|{file}|{basename}', array(
			'{folder}'    => __DIR__,
			'{dotfolder}' => str_replace(DS, '.', __DIR__),
			'{file}'      => basename(__FILE__),
			'{basename}'  => str_replace('.php', null, basename(__FILE__)),
		));

		$this->assertEquals($pattern, $view->getUsagePattern());
	}

	public function testCanTransformToArray()
	{
		$view = $this->getDummyEntity();

		$array = $view->toArray();
		$this->assertInternalType('array', $array);
		$this->assertEquals('folder', $array['root']);
	}

	public function testCanTransformToJson()
	{
		$view = $this->getDummyEntity();

		$json = json_encode($view);
		$this->assertInternalType('string', $json);
		$this->assertContains('{"root":"folder"', $json);
	}

	/**
	 * @return View
	 */
	protected function getDummyEntity()
	{
		$file = new SplFileInfo(__FILE__, __DIR__, __DIR__.'/'.__FILE__);
		$view = new View('folder', $file->getBasename());
		$view->setFile($file);

		return $view;
	}
}
