<?php

include_once('IStorage.php');
include_once ('Article.php');
include_once ('MemoryStorage.php');

// Создание новой статьи
$ms = new MemoryStorage();
$art1 = new Article($ms);

$art1->create();
$art1->title = 'New article';
$art1->content = 'New content';
$art1->save();

// Внедрение зависимости через конструктор (Dependency injection)
$art2 = new Article($ms);
// Изменение статьи
$art2->load(1);
echo "<pre>";
print_r($art2);
echo "</pre>";


$art2->title = 'NZ';
$art2->save();

$art3 = new Article($ms);
$art2->load(1);
echo "<pre>";
print_r($art3);
echo "</pre>";