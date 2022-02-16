<?php

// Класс является абстрактным, если содержит абстрактный метод!
// кроме того, нельзя создать объект абстрактного класса

// Интерфейсы нужны для того, чтобы уйти от проблемы множественного наследования
// добавить некоторый функционал классам, их может быть сколько угодно

abstract class Tag {
    protected string $name;
    protected array $attrs = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function attr(string $name, string $value) : Tag
    {
        $this->attrs[$name] = $value;
        return $this;
    }

    // Допустим, может возникнуть такая ситуация, что некоторый из детей
    // не переопределил метод render, тогда будет вызов этого метода
    // родительского класса Tag и ничего не будет отображаться в итоговом коде!
    // Необходимо заставить дочерний класс переопределять данный метод!
    // Это достигается с помощью такого понятия как абстранктный класс!

//    public function render() : string
//    {
//        return '';
//    }

    // Абстрактный метод не имеет тела
    abstract public function render() : string;

    protected function attrsToString() : string
    {
        $pairs = [];

        foreach($this->attrs as $name => $value) {
            $pairs[] = "{$name}=\"{$value}\"";
        }
        $attrsStr = implode(' ', $pairs);

        return (!empty($attrsStr)) ? ' ' . $attrsStr : '';;
    }
}

class SingleTag extends Tag
{
    public function render() : string
    {
        $attrsStr = $this->attrsToString();
        return "<{$this->name}{$attrsStr}>";
    }
}

class PairTag extends Tag
{
    protected array $children = [];

    public function appendChild(Tag | string $child) : PairTag
    {
        $this->children[] = $child;
        return $this;
    }

    public function render() : string
    {
        $attrsStr = $this->attrsToString();

        // Простой способ добавления дочерних тегов
//        $innerHTML = '';
//
//        foreach ($this->children as $child) {
//            $innerHTML .= $child->render();
//        }

        // Продвинутый способ добавления дочерних тегов
        // Анонимная функция применяется к каждому элементу массива

        // mixed тип данных only PHP8, не рекомендуется использовать!
        $innerHTML = array_map(function (Tag | string $tag) {
            // instanceof - проверка того, является ли текущий
            // объект экземпляром класса Tag
            return $tag instanceof Tag ? $tag->render() : $tag;
        }, $this->children);
        $innerHTML = implode('', $innerHTML);
        return "<{$this->name}{$attrsStr}>{$innerHTML}<{$this->name}/>";
    }
}

$img = (new SingleTag('img'))
    ->attr('src', './nz.jpg')
    ->attr('alt', 'nz not found');

$hr = new SingleTag('hr');

$a = (new PairTag('a'))
    ->attr('href', './nz')
    ->appendChild($img)
    ->appendChild('test')
    ->appendChild($hr);

echo $a->render();
