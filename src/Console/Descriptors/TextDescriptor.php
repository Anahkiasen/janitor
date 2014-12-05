<?php
namespace Janitor\Console\Descriptors;

use Illuminate\Support\Collection;
use Janitor\Abstracts\AbstractAnalyzedEntity;
use Janitor\Abstracts\Console\AbstractDescriptor;

class TextDescriptor extends AbstractDescriptor
{
	/**
	 * Describe a Collection
	 *
	 * @param Collection $object
	 * @param array      $options
	 *
	 * @return string
	 */
	protected function describeCollection(Collection $object, array $options = [])
	{
		$output = null;
		foreach ($object as $entity) {
			$output .= PHP_EOL.$this->describeEntity($entity);
		}

		return $output;
	}

	/**
	 * Describe an entity
	 *
	 * @param AbstractAnalyzedEntity $object
	 * @param array                  $options
	 *
	 * @return string
	 */
	protected function describeEntity(AbstractAnalyzedEntity $object, array $options = [])
	{
		$output = sprintf('| <comment>%s</comment> (usage: <info>%s</info>)', $object->name, $object->usage);
		if ($object->usage) {
			foreach ($object->occurences as $occurence) {
				$output .= PHP_EOL.sprintf('|-- "%s" in %s', $occurence['context'], $occurence['file']);
			}
		}

		return $output;
	}
}
