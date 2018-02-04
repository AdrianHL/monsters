<?php

namespace App\Console\Commands;

use App\Monster;
use App\World;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:world {--monsters=100 : The number of monsters that will start in the world} {--size=small : The size of the world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the Monsters World X';

    /**
     * Max iterations
     */
    const MAX_ITERATIONS = 10000;

    /**
     * Create a new command instance.
     *
     * @return bool
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nMonsters = $this->option('monsters');
        if (!(is_int($nMonsters) || ctype_digit($nMonsters)) || (int)$nMonsters < 1 ) {
            $this->error(sprintf("The monsters number provided is not valid: %s, please pass a positive integer.", $nMonsters));
            return false;
        }

        $size = $this->option('size');
        if (($size != "small") && ($size != "medium")) {
            $this->error(sprintf("The only allowed world sizes are small or medium.", $nMonsters));
            return false;
        }

        $world = new World();

        //$this->info("Processing the world information...");
        $file = new \SplFileObject(public_path(sprintf('world_map_%s.txt', $size)));

        while (!$file->eof()) {

            $line = $file->fgets();
            preg_match("/([a-zA-Z0-9-_]+)( north=([a-zA-Z0-9-_]+))?( south=([a-zA-Z0-9-_]+))?( east=([a-zA-Z0-9-_]+))?( west=([a-zA-Z0-9-_]+))?/ui", $line, $matches);

            $cityName = $matches[1] ?? null;

            if (empty($cityName)) {
                continue;
            }

            $northCity = $matches[3] ?? null;
            $southCity = $matches[5] ?? null;
            $eastCity  = $matches[7] ?? null;
            $westCity  = $matches[9] ?? null;

            $world->addCityPairs($cityName, $northCity, $southCity, $eastCity, $westCity);
        }


        $monsters = [];

        for($i = 1; $i < $nMonsters; $i++)
        {
            $rCity = $world->randomCity();

            if (empty($rCity)) {
                $this->info("There are no cities left so the monster cannot be placed in the world!");
                return;
            }

            $monster = new Monster();

            try {
                $monster->isInCity($rCity);
                $monsters[$monster->getName()] = $monster;
            } catch (\Exception $ex) {
                $world->cityDestroyed($rCity);
                //ToDo - Move the HTML to an if as an extra parameter when using the command so the output is based on the format expected
                $this->info($ex->getMessage() . "<br>");
            }
        }

        $keepRunning = true;

        $iterations = 0;

        do
        {
            $iterations = $iterations + 1;

            foreach($monsters as $monster)
            {
                $moveFrom = $monster->whereIs();
                $moveTo = $moveFrom->getRandomAccessible();
                if (!empty($moveTo)) {
                    $moveFrom->populate(null);

                    try {
                        $monster->isInCity($moveTo);
                    } catch (\Exception $ex) {
                        $world->cityDestroyed($moveTo);
                        //ToDo - Move the HTML to an if as an extra parameter when using the command so the output is based on the format expected
                        $this->info($ex->getMessage() . "<br>");
                    }

                } else {
                    //$this->info(sprintf("The monster %s is trapped in %s", $monster->getName(), $moveFrom->getName()));
                    unset($monsters[$monster->getName()]);
                }

            }

            //Keep running while there are more than one monster and it has iterate less than the maximum allowed
            $keepRunning = $keepRunning && count($monsters > 1) && ($iterations < static::MAX_ITERATIONS);
        } while ($keepRunning);


        Cache::put('world.left', (string) $world, 10);
    }
}
