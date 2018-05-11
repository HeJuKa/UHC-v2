<?php

namespace uhc\scenario;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;
use uhc\events\StartUHCEvent;
use uhc\events\StopUHCEvent;
use uhc\scenario\scenarios\CatEyes;
use uhc\scenario\scenarios\CutClean;
use uhc\scenario\scenarios\GoldenHead;
use uhc\scenario\scenarios\NoFall;
use uhc\scenario\scenarios\TimeBomb;
use uhc\UHC;

class ScenarioManager
{
    const SCENARIO_FILE = "scenarios.yml";

    private $plugin;
    public static $scenarios = [];
    private static $config;

    /**
     * ScenarioManager constructor.
     * @param UHC $plugin
     */
    public function __construct(UHC $plugin)
    {
        $this->setPlugin($plugin);
        self::registerScenarios();
        self::setConfig(new Config($plugin->getDataFolder() . self::SCENARIO_FILE, Config::YAML, [
            "CutClean" => true,
            "CatEyes" => true,
            "GoldenHead" => true,
            "NoFall" => true,
            "TimeBomb" => true
        ]));
        foreach (self::getConfig()->getAll() as $scenario => $bool){
            if(self::isScenario($scenario)){
                $bool ? self::getScenario($scenario)->setEnabled(true) : self::getScenario($scenario)->setEnabled(false);
            }
        }
    }

    /**
     * @return bool
     */
    public static function registerScenarios() : bool
    {
        self::registerScenario(new CutClean(), true);
        self::registerScenario(new CatEyes(), true);
        self::registerScenario(new GoldenHead(), true);
        self::registerScenario(new NoFall(), true);
        self::registerScenario(new TimeBomb(), true);
        return true;
    }

    /**
     * @param Scenario $scenario
     * @param bool $force
     */
    public static function registerScenario(Scenario $scenario, bool $force = false)
    {
        if($force){
            self::$scenarios[$scenario->getName()] = $scenario;
        } else {
            if (!isset(self::$scenarios[$scenario->getName()])) {
                self::$scenarios[$scenario->getName()] = $scenario;
            } else {
                throw new PluginException("[ScenarioManager] Scenario is already registered!");
            }
        }
    }

    /**
     * @return Config
     */
    public static function getConfig() : Config
    {
        return self::$config;
    }

    /**
     * @param Config $config
     */
    public static function setConfig(Config $config)
    {
        self::$config = $config;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios() : array
    {
        return self::$scenarios;
    }

    /**
     * @param string $name
     * @return Scenario
     */
    public static function getScenario(string $name) : Scenario
    {
        return self::$scenarios[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isScenario(string $name) : bool
    {
        return isset(self::$scenarios[$name]);
    }

    /**
     * @return UHC
     */
    public function getPlugin() : UHC
    {
        return $this->plugin;
    }

    /**
     * @param UHC $plugin
     */
    public function setPlugin(UHC $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function doMove(PlayerMoveEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onMove($event);
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function doBreak(BlockBreakEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onBreak($event);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function doPlace(BlockPlaceEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onPlace($event);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function doDeath(PlayerDeathEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onDeath($event);
        }
    }

    /**
     * @param StartUHCEvent $event
     */
    public function doStart(StartUHCEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onStart($event);
        }
    }

    /**
     * @param StopUHCEvent $event
     */
    public function doStop(StopUHCEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onStop($event);
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function doDamage(EntityDamageEvent $event)
    {
        foreach (self::getScenarios() as $scenario){
            if($scenario->isEnabled()) $scenario->onDamage($event);
        }
    }
}