<?php
namespace Janitor\Console;

use Janitor\Console\Descriptors\JsonDescriptor;
use Janitor\Console\Descriptors\TextDescriptor;

class DescriptorHelper extends \Symfony\Component\Console\Helper\DescriptorHelper
{
	public function __construct()
	{
		$this
			->register('txt', new TextDescriptor())
			->register('json', new JsonDescriptor());
	}
}
