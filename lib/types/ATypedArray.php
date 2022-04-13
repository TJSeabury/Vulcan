<?php

namespace Vulcan\lib\types;

use \Collections\TypeValidator;
use \Collections\Exceptions\InvalidArgumentException;

/**
 * Class TypedArray
 *
 * This is a typed array
 *
 * By enforcing the type, you can guarantee that the content is safe to simply iterate and call methods on.
 */
abstract class AbstractTypedArray extends ArrayObject
{
    use TypeValidator; //This is probably going to to error hard... resolve lib conflicts.

    /**
     * Define the class that will be used for all items in the array.
     * To be defined in each sub-class.
     */
    const ARRAY_TYPE = null;

    /**
     * Array Type
     *
     * Once set, this ArrayObject will only accept instances of that type.
     *
     * @var string $arrayType
     */
    private $arrayType = null;

    /**
     * Constructor
     *
     * Store the required array type prior to parental construction.
     *
     * @param mixed[] $input Any data to preset the array to.
     * @param int $flags The flags to control the behaviour of the ArrayObject.
     * @param string $iteratorClass Specify the class that will be used for iteration of the ArrayObject object. ArrayIterator is the default class used.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $input = [],
        $flags = 0,
        $iteratorClass = ArrayIterator::class
    ) {
        // ARRAY_TYPE must be defined.
        if (empty(static::ARRAY_TYPE)) {
            throw new \RuntimeException(
                sprintf(
                    '%s::ARRAY_TYPE must be set to an allowable type.',
                    get_called_class()
                )
            );
        }

        // Validate that the ARRAY_TYPE is appropriate.
        try {
            $this->arrayType = $this->determineType(static::ARRAY_TYPE);
        } catch (\Collections\Exceptions\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        // Validate that the input is an array or an object with an Traversable interface.
        if (!(is_array($input) || (is_object($input) && in_array(Traversable::class, class_implements($input))))) {
            throw new InvalidArgumentException('$input must be an array or an object that implements \Traversable.');
        }

        // Create an empty array.
        parent::__construct([], $flags, $iteratorClass);

        // Append each item so to validate it's type.
        foreach ($input as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Adding a new value at the beginning of the collection
     *
     * @param mixed $value
     *
     * @return int Returns the new number of elements in the Array
     *
     * @throws InvalidArgumentException
     */
    public function unshift($value): int
    {
        try {
            $this->validateItem($value, $this->arrayType);
        } catch (\Collections\Exceptions\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        return parent::unshift($value);
    }

    /**
     * Check the type and then store the value.
     *
     * @param mixed $offset The offset to store the value at or null to append the value.
     * @param mixed $value The value to store.
     *
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        try {
            $this->validateItem($value, $this->arrayType);
        } catch (\Collections\Exceptions\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Sort an array, taking into account objects being able to represent their sortable value.
     *
     * {@inheritdoc}
     */
    public function sort($sortFlags = SORT_REGULAR)
    {
        if (!in_array(SortableInterface::class, class_implements($this->arrayType))) {
            throw new \RuntimeException(
                sprintf(
                    "Cannot sort an array of '%s' as that class does not implement '%s'.",
                    $this->arrayType,
                    SortableInterface::class
                )
            );
        }
        // Get the data from
        $originalData = $this->getArrayCopy();
        $sortableData = array_map(
            function (SortableInterface $item) {
                return $item->getSortValue();
            },
            $originalData
        );

        $result = asort($sortableData, $sortFlags);

        $order = array_keys($sortableData);
        uksort(
            $originalData,
            function ($key1, $key2) use ($order) {
                return array_search($key1, $order) <=> array_search($key2, $order);
            }
        );

        $this->exchangeArray($originalData);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $callback, int $flag = 0)
    {
        if ($flag == ARRAY_FILTER_USE_KEY) {
            throw new InvalidArgumentException('Cannot filter solely by key. Use ARRAY_FILTER_USE_BOTH and amend your callback to receive $value and $key.');
        }

        return parent::filter($callback, $flag);
    }
}
