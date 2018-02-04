<?php

namespace App;

use Nubs\RandomNameGenerator\Alliteration;

class Monster
{
    /**
     * Monster's name
     * @var
     */
    protected $name;

    /**
     * City where the monster is in
     *
     * @var
     */
    protected $isInCity;

    const DEAD = 0;
    const ALIVE = 1;

    public function __construct()
    {
        $this->status = static::ALIVE;
        $generator = new Alliteration();
        $this->name = $generator->getName();
    }

    /**
     * Gets the Monster name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the city where the monster is
     * @param City $city
     * @return self
     */
    public function isInCity(City $city)
    {
        $this->isInCity = $city->populate($this);

        return $this;
    }

    public function whereIs()
    {
        return $this->isInCity;
    }

    public function setDead()
    {
        $this->status = static::DEAD;
        return $this;
    }

    public function isAlive()
    {
        return $this->status === static::ALIVE;
    }

}