<?php

namespace App;


class World
{
    /**
     * Cities in the world
     *
     * @var array
     */
    protected $cities;

    /**
     * Monsters in the world
     *
     * @var array
     */
    protected $monsters;

    /**
     * World constructor.
     *
     * @return self
     */
    public function __construct()
    {
        $this->cities   = [];
        $this->monsters = [];
        return $this;
    }

    /**
     * Gets the City from the World or creates it
     *
     * @param string|null $cityName
     * @return City
     */
    public function city(string $cityName = null)
    {
        //Validate the city name
        if (empty($cityName)) {
            return null;
        }

        //Create the city if it doesn't exist
        if (!isset($this->cities[$cityName]))
        {
            $this->cities[$cityName] = new City($cityName);
        }

        return $this->cities[$cityName];
    }

    public function cityDestroyed(City $city)
    {
        unset($this->cities[$city->getName()]);
    }

    /**
     * Add the
     * @param string|null $cityName
     * @param string|null $northCityName
     * @param string|null $southCityName
     * @param string|null $eastCityName
     * @param string|null $westCityName
     */
    public function addCityPairs(
        string $cityName = null,
        string $northCityName = null,
        string $southCityName = null,
        string $eastCityName = null,
        string $westCityName = null
    )
    {
        $this->city($cityName)->cityPairs(
            $this->city($northCityName),
            $this->city($southCityName),
            $this->city($eastCityName),
            $this->city($westCityName)
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = "";
        foreach ($this->cities as $cityName => $city)
        {
            if (!$city->isAccessible()) {
              continue;
            }

            $output .= $city->getName();

            if ($city->isNorthAccessible()) {
                $output .= " north=" . $city->northCity()->getName();
            }

            if ($city->isSouthAccessible()) {
                $output .=  " south=" . $city->southCity()->getName();
            }

            if ($city->isEastAccessible()) {
                $output .=  " east=" . $city->eastCity()->getName();
            }

            if ($city->isWestAccessible()) {
                $output .=  " west=" . $city->westCity()->getName();
            }

            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * Returns a random city from the world
     *
     * @return City|null
     */
    public function randomCity()
    {
        if (empty($this->cities)) {
            return null;
        }
        return $this->cities[array_rand($this->cities, 1)];
    }

    /**
     * Place the Monster in the city
     * @param Monster $monster
     * @param City $city
     * @return bool
     */
    public function placeMonster(Monster $monster, City $city)
    {
        if (isset($this->monsters[$monster->name()]))
        {
            return false;
        }

        $this->monsters[$monster->name()] = $monster;

        $city->populate($monster);
    }

    public function getMonsters()
    {
        return $this->monsters;
    }
}