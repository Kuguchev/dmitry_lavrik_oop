<?php
/*
 * user status
  0 ->  new
  1 -> activated
  2 -> banned
 */

class UserStatuses
{
    // Константы принадлежат всему классу
    const CREATED = 0;
    const ACTIVATED = 1;
    const BANNED = 2;
}

class User
{
    public $id;
    public $login;
    public $name;
    private $status;
    public $created;
    private $now;

    public function __construct(int $id, string $login, string $name, int $status, int $created)
    {
        $this->id = $id;
        $this->login = $login;
        $this->status = $status;
        $this->name = $name;
        $this->created = $created;

        $this->now = time();
    }

    public function isActive() : bool
    {
        return $this->status == UserStatuses::ACTIVATED;
    }

    public function activate()
    {
        $this->status = UserStatuses::ACTIVATED;
    }

    public function ban()
    {
        $this->status = UserStatuses::BANNED;
    }

    public function save()
    {
        //save user in db
    }
}

// Функция time возвращает количество секунд с 1 января 1970 года
// начало эпохи Unix
$user1 = new User(1, 'admin', 'Dmitry', 0, 1644928166);

$user1->activate();
echo $user1->isActive();

// Тег pre предварительно отформатированный текст()
echo '<pre>';
print_r($user1);
echo '</pre>';

//$user2 = new User(2, 'manager', 'Some', 0, 1644928162);
//echo '<pre>';
//print_r($user2);
//echo '</pre>';