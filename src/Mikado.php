<?php


namespace kriskbx\mikado;


use InvalidArgumentException;

/**
 * Class Mikado. Holding manager objects.
 * @package kriskbx\mikado
 */
class Mikado
{

    /**
     * @var Manager[]
     */
    protected $managers = [];

    /**
     * Add manager.
     *
     * @param string $identifier
     * @param Manager $manager
     */
    public function add($identifier, Manager $manager)
    {
        $this->managers[$identifier] = $manager;
    }

    /**
     * Get manager by the given identifier.
     *
     * @param $identifier
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function get($identifier)
    {
        if(!isset($this->managers[$identifier]))
            throw new InvalidArgumentException('A manager with that identifier does not exist.');

        return $this->managers[$identifier];
    }


}