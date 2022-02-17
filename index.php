<?php

// Интерфейсы позволяют объединять классы, которые
// не выстраиваются в какую-то иерархию

interface IStorage
{
    public function add(string $key, mixed $data) : void;
    public function remove(string $key) : void;
    public function contains(string $key) : bool;
    public function get(string $key) : mixed;
}

class Storage implements IStorage, JsonSerializable
{
    protected array $storage = [];

    public function add(string $key, mixed $data) : void
    {
        $this->storage[$key] = $data;
    }

    public function remove(string $key) : void
    {
        if($this->contains($key)) {
            unset($this->storage[$key]);
        }
    }

    public function contains(string $key) : bool
    {
        //  В даннном случае будет проверка по ключу
        // если по заданному ключу лежат данные как null
        // то isset вернет false, что не есть гуд
        //return isset($this->storage[$key]);

        // Проблема решается использованием функции array_key_exists
        // проверка ключа в массиве
        return array_key_exists($key, $this->storage);
    }

    public function get(string $key) : mixed
    {
        //return $this->storage[$key] ?? null;
        return $this->contains($key) ? $this->storage[$key] : null;
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->storage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}


class Animal implements JsonSerializable
{
    public $name;
    public $health;
    public $alive;
    protected $power;

    public function __construct(string $name, int $health, int $power)
    {
        $this->name = $name;
        $this->health = $health;
        $this->power = $power;
        $this->alive = true;
    }

    public function calcDamage()
    {
        return $this->power * (mt_rand(100, 300) / 200);
    }

    public function applyDamage(int $damage)
    {
        $this->health -= $damage;

        if($this->health <= 0){
            $this->health = 0;
            $this->alive = false;
        }
    }

    public function jsonSerialize(): mixed
    {
        return json_encode(
            [
                'name' => $this->name,
                'health' => $this->health,
                'alive' => $this->alive,
                'power' => $this->power
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

//
class JSONLogger{
    protected array $objects = [];

    public function addObject(JsonSerializable $obj) : void
    {
        $this->objects[] = $obj;
    }

    public function log(string $betweenLogs = ',') : string
    {
        $logs = array_map(function(JsonSerializable $obj){
            return $obj->jsonSerialize();
        }, $this->objects);

        return implode($betweenLogs, $logs);
    }
}

$gameStorage = new Storage();
$gameStorage->add('test', null);
$gameStorage->add('test2', mt_rand(1, 10));

$a1 = new Animal('Murzik', 20, 5);
$a2 = new Animal('Bobik', 30, 3);

var_dump($gameStorage->get('test')) ;
var_dump($gameStorage->get('test3'));
var_dump($gameStorage->contains('test'));
var_dump($gameStorage->remove('test'));
var_dump ($gameStorage->contains('test'));

$logger = new JSONLogger();
$logger->addObject($a1);
$logger->addObject($a2);
$logger->addObject($gameStorage);

echo $logger->log('<br>') . '<hr>';

$a2->applyDamage($a1->calcDamage());
$gameStorage->add('other', mt_rand(1, 10));

echo $logger->log('<br>');
