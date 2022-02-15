<?php


// Полиморфизм - это изменчивость детей


// Базовый класс
class Animal
{
    public string $name;
    public int $health;
    private int $power;
    public bool $alive;

    public function __construct(string $name, int $health, int $power)
    {
        $this->name = $name;
        $this->health = $health;
        $this->power = $power;
        $this->alive = true;
    }

    public function calcDamage()
    {
        return $this->power * (mt_rand(1, 3) / 2);
    }

    public function applyDamage(int $damage)
    {
        $this->health -= $damage;
        if($this->health <= 0) {
            $this->health = 0;
            $this->alive = false;
        }
    }
}

// Классы наследники

class Dog extends Animal
{

}

class Cat extends Animal
{
    private $lifes;
    private $baseHealth;

    public function __construct(string $name, int $health, int $power)
    {
        // parent - обращение к родительскому классу
        // self - обращение к текущему классу
        parent::__construct($name, $health, $power);
        $this->lifes = 9;
        $this->baseHealth = $health;
    }

    public function applyDamage(int $damage)
    {
        $this->health -= $damage;
        parent::applyDamage($damage);
        // Воскрешение кота:)
        if(!$this->alive && $this->lifes > 1) {
            $this->lifes--;
            $this->alive = true;
            $this->health = $this->baseHealth;
        }
    }
}

class Mouse extends Animal
{
    private $hiddenLevel;

    // Количество параметров в конструкторе должно быть одинаковым
    // что у родителя, что у наследника
    // Конструктор должен быть с одинаковым интерфейсом
    public function __construct(string $name, int $health, int $power)
    {
        // parent - обращение к родительскому классу
        // self - обращение к текущему классу
        parent::__construct($name, $health, $power);
        $this->hiddenLevel = 0.95;
    }

    public function setHiddenLevel(float $level)
    {
        $this->hiddenLevel = $level;
    }

    // Переопределение метода родительского класса
    // Яркий пример полиморфизма (изменчивость)
    public function applyDamage(int $damage)
    {
        // Условие, когда проходит урон по мышке:)
       if((mt_rand(1, 100) / 100) > $this->hiddenLevel) {
           parent::applyDamage($damage);
       }
    }
}

class GameCore
{
    private array $units; // Массив юнитов

    // Конструктор создает массив юнитов
    public function __construct()
    {
        $this->units = [];
    }

    // Добавление юнита на игровое поле
    // Явно указываем класс Animal
    // Чтобы юниты были только из базового класса Animal
    public function addUnit(Animal $unit)
    {
        $this->units[] = $unit;
    }

    public function run()
    {
        $i = 0;
        while(count($this->units) > 1)
        {
            echo "Round $i <br>";
            $this->nextTick();
            $i++;
            echo '<hr>';
        }
    }

    // Следующий ход
    public function nextTick()
    {
        foreach ($this->units as $unit) {
            // Не знаем, есть ли такой метод у объекта
            // Возможно обращение к несуществующемому методу объекта

            // Тут непонятно, у какого объекта мы вызываем метод, но
            // он есть, это может быть родительский класс, либо дочерний
            // яркий пример полиморфизма
            $damage = $unit->calcDamage();
            $target = $this->getRandomUnit($unit);
            $targetPrevHealth = $target->health;
            $target->applyDamage($damage);
            echo "{$unit->name} beat {$target->name}, damage={$damage}, health={$targetPrevHealth} -> 
                {$target->health} <br>";
        }

        $this->units = array_values(array_filter($this->units, function ($unit) {
            return $unit->alive;
        }));
    }

    private function getRandomUnit(Animal $exclude)
    {
        // Исключение текущего юнита из массива
        // $units в данном случае просто ссылка на те же самые объекты
        // ОЗУ не расходуется, новые объекты не создаются
        // после применения array_filter ключи массива заново не индексируются!
        //
        $units = array_values(array_filter($this->units, function ($unit) use ($exclude) {
            // объекты сравниваются по ссылке
            return $unit !== $exclude;
        }));

        return $units[mt_rand(0, count($units) - 1)];
    }
}

$core = new GameCore();

$core->addUnit(new Cat('Murzik', 20, 5));
$core->addUnit(new Dog('Bobik', 200, 10));
$core->addUnit(new Mouse('Jerry', 10, 2));
$core->addUnit(new Cat('Garfild', 30, 15));
$core->addUnit(new Dog('Volk', 180, 9));
$core->addUnit(new Mouse('Guffy', 10, 5));

$core->run();
