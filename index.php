<?php

abstract class Tag{
    protected string $name;
    protected array $attrs = [];

    public function __construct(string $name){
        $this->name = $name;
    }

    public function attr(string $name, string $value) : static
    {
        $this->attrs[$name] = $value;
        return $this;
    }

    abstract public function render() : string;

    protected function attrsToString() : string{
        $pairs = [];

        foreach($this->attrs as $name => $value){
            $pairs[] = "$name=\"$value\"";
        }

        return implode(' ', $pairs);
    }
}

class SingleTag extends Tag{
    public function render() : string{
        $attrsStr = $this->attrsToString();
        return "<{$this->name} $attrsStr>";
    }
}

class PairTag extends Tag{
    protected array $children = [];

    public function appendChild(Tag $child) : static{
        $this->children[] = $child;
        return $this;
    }

    public function render() : string{
        $attrsStr = $this->attrsToString();

        $childrenHTML = array_map(function(Tag $tag){
            return $tag->render();
        }, $this->children);

        $innerHTML = implode('', $childrenHTML);
        return "<{$this->name} $attrsStr>$innerHTML</{$this->name}>";
    }
}

$img = (new SingleTag('img'))->attr('src', 'f1.jpg')->attr('alt', 'f1 not found');
$label = (new PairTag('label'))
    ->attr('class', 'some')
    ->appendChild($img);

echo $label->render();