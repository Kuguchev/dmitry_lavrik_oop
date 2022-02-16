<?php

// Только публичные методы без тела
// Либо константы (редко)
interface IRenderable
{
    public function render() : string;
}

interface IValidable
{
    public function isValid() : bool;
}

abstract class Tag implements IRenderable, IValidable
{
    protected string $name;
    protected array $attrs = [];
    protected array $allowedNames;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // Можно не указывать абстрактный метод render
    // Это бремя ложится на дочерние классы, т.к. это абстрактный класс
    // и его экземпляр мы создавать не можем, поэтому определение метода и ложится
    // на дочерние классы...
    // abstract public function render() : string;

    public function attr(string $name, string $value)
    {
        $this->attrs[$name] = $value;
        return $this;
    }

    protected function attrsToString() : string
    {
        $pairs = [];

        foreach($this->attrs as $name => $value){
            $pairs[] = "$name=\"$value\"";
        }
        $attrsStr = implode(' ', $pairs);
        return (!empty($attrsStr)) ? ' ' . $attrsStr : '';
    }

    // Метод доступен всем наследникам!
    // Мы его переопределили в абстрактном классе, поэтому нет необходимости
    // его переопределять в классах наследниках
    public function isValid(): bool
    {
        return in_array($this->name, $this->allowedNames);
    }
}

class SingleTag extends Tag
{
    protected array $allowedNames = ['img', 'hr'];

    public function render() : string
    {
        $attrsStr = $this->attrsToString();
        return "<{$this->name}$attrsStr>";
    }
}

class PairTag extends Tag
{
    protected array $allowedNames = ['div', 'span'];
    protected array $children = [];

    // Передача в функцию типа IRenderable
    // позволяет связывать классы, которые находятся не в одной иерархии!
    // Например можно передать объект класса TextNode, PairTag или SingleTag
    public function appendChild(IRenderable $child)
    {
        $this->children[] = $child;
        return $this;
    }

    public function render() : string
    {
        $attrsStr = $this->attrsToString();

        $childrenHTML = array_map(function(IRenderable $tag)
        {
            return $tag->render();
        }, $this->children);

        $innerHTML = implode('', $childrenHTML);
        return "<{$this->name}$attrsStr>$innerHTML</{$this->name}>";
    }
}

class TextNode implements IRenderable, IValidable
{
    protected string $text;

    public function __construct($text)
    {
        $this->text = trim($text);
    }

    public function render() : string
    {
        return $this->text;
    }

    public function isValid(): bool
    {
        return $this->text !== '';
    }
}

class Markdown implements IValidable
{
    public function isValid(): bool
    {
        return true;
    }
}

$img = (new SingleTag('img'))->attr('src', 'f1.jpg')->attr('alt', 'f1 not found');
$a = (new PairTag('a'))->attr('href', '#')->appendChild(new TextNode('go home'));

$label = (new PairTag('label'))
    ->appendChild($img)
    ->appendChild(new TextNode('attention'))
    ->appendChild($a);

$html = $label->render();
echo $html;
echo '<hr>' . htmlspecialchars($html);