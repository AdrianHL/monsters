<?php

namespace App;


class City
{
    /**
     * North City
     * @var City
     */
    protected $northCity;

    /**
     * South City
     * @var City
     */
    protected $southCity;

    /**
     * East City
     * @var City
     */
    protected $eastCity;

    /**
     * West City
     * @var City
     */
    protected $westCity;

    /**
     * The city is OK and Monsters can move from and to it.
     */
    const OK = 1;

    /**
     * The city has been destroyed and monster cannot move from and to it.
     */
    const DESTROYED = 0;

    /**
     * @var Monster
     */
    protected $populatedBy;

    /**
     * City constructor.
     *
     * @param string $name
     * @return self
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->status = static::OK;
        $this->populatedBy = null;
        return $this;
    }

    /**
     * Set the City Pairs
     *
     * @param City|null $northCity
     * @param City|null $southCity
     * @param City|null $eastCity
     * @param City|null $westCity
     */
    public function cityPairs(
        City $northCity = null,
        City $southCity = null,
        City $eastCity = null,
        City $westCity = null
    ) {
        $this->northCity = $northCity;
        $this->southCity = $southCity;
        $this->eastCity = $eastCity;
        $this->westCity = $westCity;
    }

    /**
     * Check if the city is accessible
     *
     * @return bool
     */
    public function isAccessible()
    {
        return $this->status === static::OK;
    }

    /**
     * The city has been destroyed
     *
     * @return int
     * @return self
     */
    public function destroy()
    {
        $this->status = static::DESTROYED;
        return $this;
    }

    protected function isDirAccessible($dir)
    {
        if(empty($this->{$dir . 'City'}())) {
            return false;
        }

        return $this->{$dir . 'City'}->isAccessible();
    }

    public function isNorthAccessible()
    {
        return $this->isDirAccessible('north');
    }

    public function isSouthAccessible()
    {
        return $this->isDirAccessible('south');
    }

    public function isWestAccessible()
    {
        return $this->isDirAccessible('west');
    }

    public function isEastAccessible()
    {
        return $this->isDirAccessible('east');
    }

    public function getRandomAccessible()
    {
        $possibilities = ['north', 'south', 'west', 'east'];

        $randomAccessible = null;

        do {
            $random = array_rand($possibilities, 1);
            $randomMove = $possibilities[$random];
            if ($this->isDirAccessible($randomMove)) {
                $randomAccessible = $this->{$randomMove . 'City'};
            } else {
                unset($possibilities[$random]);
            }
        } while (count($possibilities) && empty($randomAccessible));

        return $randomAccessible;
    }

    public function northCity()
    {
        return $this->northCity;
    }

    public function southCity()
    {
        return $this->southCity;
    }

    public function eastCity()
    {
        return $this->eastCity;
    }

    public function westCity()
    {
        return $this->westCity;
    }

    public function getName()
    {
        return $this->name;
    }

    public function populate(Monster $monster = null)
    {
        if (is_null($monster))
        {
            $this->populatedBy = null;
            return $this;
        }

        if ($this->isPopulatedBy())
        {
            //The monsters kill each other
            $monster->setDead();
            $this->isPopulatedBy()->setDead();
            //And the city is destroyed
            $this->destroy();
            //ToDo - Change to a custom Exception
            throw new \Exception(sprintf("%s has been destroyed by monster %s and monster %s!", $this->name, $this->populatedBy->getName(), $monster->getName()));
        }

        $this->populatedBy = $monster;
        return $this;
    }

    /**
     * @return Monster|null
     */
    public function isPopulatedBy()
    {
        return $this->populatedBy;
    }
}