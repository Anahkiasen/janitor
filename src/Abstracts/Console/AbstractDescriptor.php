<?php
namespace Janitor\Abstracts\Console;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Janitor\Abstracts\AbstractAnalyzedEntity;
use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractDescriptor implements DescriptorInterface
{
	/**
	 * @type OutputInterface
	 */
	protected $output;

	/**
	 * Describes an InputArgument instance.
	 *
	 * @param OutputInterface $output
	 * @param object          $object
	 * @param array           $options
	 */
	public function describe(OutputInterface $output, $object, array $options = array())
	{
		$this->output = $output;

		switch (true) {
			case $object instanceof Collection:
				$this->output->writeln($this->describeCollection($object, $options));
				break;

			case $object instanceof AbstractAnalyzedEntity:
				$this->output->writeln($this->describeEntity($object, $options));
				break;

			default:
				throw new InvalidArgumentException(sprintf('Object of type "%s" is not describable.', get_class($object)));
		}
	}

	/**
	 * Describe a Collection
	 *
	 * @param Collection $object
	 * @param array      $options
	 *
	 * @return string
	 */
	abstract protected function describeCollection(Collection $object, array $options = []);

	/**
	 * Describe an entity
	 *
	 * @param AbstractAnalyzedEntity $object
	 * @param array                  $options
	 *
	 * @return string
	 */
	abstract protected function describeEntity(AbstractAnalyzedEntity $object, array $options = []);
}
