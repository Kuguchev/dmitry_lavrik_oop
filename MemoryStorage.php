<?php

class MemoryStorage implements IStorage
{
    protected array $records = [];
    protected int $ai = 0;

    public function create() : int
    {
        $id = ++$this->ai;
        $this->records[$id] = [];
        return $id;
    }
    public function get(int $id) : ?array
    {
        return $this->records[$id] ?? null;
    }

    public function remove(int $id) : bool
    {
        if(array_key_exists($id, $this->records))
        {
            unset($this->records[$id]);
            return true;
        }
        return false;
    }

    public function update(int $id, array $fields) : bool
    {
        if(array_key_exists($id, $this->records))
        {
            $this->records[$id] = $fields;
            return true;
        }
        return false;
    }
}