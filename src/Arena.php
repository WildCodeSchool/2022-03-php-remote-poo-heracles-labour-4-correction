<?php

namespace App;

use Exception;

class Arena 
{
    private array $monsters;
    private Hero $hero;

    private int $size = 10;

    public function __construct(Hero $hero, array $monsters)
    {
        $this->hero = $hero;
        $this->monsters = $monsters;
    }

    public function getDistance(Fighter $startFighter, Fighter $endFighter): float
    {
        $Xdistance = $endFighter->getX() - $startFighter->getX();
        $Ydistance = $endFighter->getY() - $startFighter->getY();
        return sqrt($Xdistance ** 2 + $Ydistance ** 2);
    }

    public function touchable(Fighter $attacker, Fighter $defenser): bool 
    {
        return $this->getDistance($attacker, $defenser) <= $attacker->getRange();
    }

    /**
     * Get the value of monsters
     */ 
    public function getMonsters(): array
    {
        return $this->monsters;
    }

    /**
     * Set the value of monsters
     *
     */ 
    public function setMonsters($monsters): void
    {
        $this->monsters = $monsters;
    }

    /**
     * Get the value of hero
     */ 
    public function getHero(): Hero
    {
        return $this->hero;
    }

    /**
     * Set the value of hero
     */ 
    public function setHero($hero): void
    {
        $this->hero = $hero;
    }

    /**
     * Get the value of size
     */ 
    public function getSize(): int
    {
        return $this->size;
    }

    public function move(Fighter $fighter, string $direction)
    {
        $fighterX = $moveToX = $fighter->getX();
        $fighterY = $moveToY = $fighter->getY();
        switch ($direction) {
            case "N":
                $moveToY = $fighterY-1;
                break;
            case "S":
                $moveToY = $fighterY+1;
                break;
            case "E":
                $moveToX = $fighterX+1;
                break;
            case "W":
                $moveToX = $fighterX-1;
                break;
        }

        // switch (true) {
        //     case $moveToY < 0:
        //     case $moveToY >= $this->getSize():
        //     case $moveToX < 0:
        //     case $moveToX >= $this->getSize():
        //         throw new Exception("Out of map!");
        // }
        
        $outOfMap = match(true) {
            $moveToY < 0 => true,
            $moveToY >= $this->getSize() => true,
            $moveToX < 0 => true,
            $moveToX >= $this->getSize() => true,
            default => false
        };
        if ($outOfMap) {
            throw new Exception("Out of map!");
        }

        foreach($this->getMonsters() as $monster) {
            if ($monster->getX() === $moveToX && $monster->getY() === $moveToY ) {
                throw new Exception("Occupied!");
            }
        }

        $fighter->setX($moveToX);
        $fighter->setY($moveToY);
    }

    public function battle(int $id)
    {
        $monster = $this->monsters[$id];
        if (!$this->touchable($this->getHero(), $monster)) {
            throw new Exception('Monster is Too fare!');
        } 
        $this->getHero()->fight($monster);

        if ($monster->isAlive()) {
            if (!$this->touchable($monster, $this->getHero())) {
                throw new Exception('Hero is Too fare!');
            }
            $monster->fight($this->getHero());
        } else {
            $this->hero->setExperience($this->hero->getExperience() + $monster->getExperience());
            unset($this->monsters[$id]);
        }
        
    }
}