<?php
namespace Janitor\Console\Descriptors;

use Illuminate\Support\Collection;
use Janitor\Abstracts\AbstractAnalyzedEntity;
use Janitor\Abstracts\Console\AbstractDescriptor;

class JsonDescriptor extends AbstractDescriptor
{
    /**
     * Describe an entity.
     *
     * @param AbstractAnalyzedEntity $object
     * @param array                  $options
     *
     * @return string
     */
    protected function describeEntity(AbstractAnalyzedEntity $object, array $options = [])
    {
        return $object->toJson();
    }

    /**
     * Describe a Collection.
     *
     * @param Collection $object
     * @param array      $options
     *
     * @return string
     */
    protected function describeCollection(Collection $object, array $options = [])
    {
        return $object->toJson();
    }
}
