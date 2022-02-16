<?php

// Общее у тегов: название и атрибут
// Любой тег можно отобразить

class Tag {
    protected string $name;
    protected array $attrs = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function attr(string $name, string $value) : Tag
    {
        $this->attrs[$name] = $value;
        return $this; // Аля паттерн builder
    }

    public function render() : string
    {
        return '';
    }

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

    public function appendChild(Tag $child) : PairTag
    {
        $this->children[] = $child;
        return $this; // Аля паттерн builder
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
        $innerHTML = array_map(function (Tag $tag){
            return $tag->render();
        }, $this->children);
        $innerHTML = implode('', $innerHTML);
        return "<{$this->name}{$attrsStr}>{$innerHTML}<{$this->name}/>";
    }
}

// Цепочка вызовов методов
// После применения метода attr возвращается ссылка на текущий объект
// соответственно мы также можем обратиться к этому же объекту

$img = (new SingleTag('img'))
    ->attr('src', './nz.jpg')
    ->attr('alt', 'nz not found');

$hr = new SingleTag('hr');

$a = (new PairTag('a'))
    ->attr('href', './nz')
    ->appendChild($img)
    ->appendChild($hr);

echo $a->render();
