<?php

// Класс статей
class Article
{
    protected int $id;
    public string $title;
    public string $content;
    protected IStorage $storage;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public function create()
    {
        // Можем писать код в слепую, не зная, как реализован метод create
        // Второй программист может параллельно реализовывать класс,
        // в который также имплементирован интерфейс IStorage, который реализует
        // например, создание статьи в базе данных!
        $this->id = $this->storage->create();
    }

    public function load(int $id)
    {
        $data = $this->storage->get($id);

        if($data === null) {
            throw new Exception("article with id={$id} not found");
        }

        $this->id = $id;
        $this->title = $data['title'];
        $this->content = $data['content'];
    }

    public function save()
    {
        $this->storage->update($this->id, [
            'title' => $this->title,
            'content' => $this->content
        ]);
    }
}