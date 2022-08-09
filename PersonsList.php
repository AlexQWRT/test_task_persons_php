<?php

/**
 * Автор: Диденко Алексей
 *
 * Дата реализации: 09.08.2022 14:00
 *
 * Дата изменения: 09.08.2022 17:25
 *
 * Класс для работы со списком людей
*/
require_once('DB.php');
require_once('Person.php');
if (!class_exists('DB')) {
    throw new RuntimeException("Class DB does not exists!");
}
if (!class_exists('Person')) {
    throw new RuntimeException("Class Person does not exists!");
}
/**
 * Класс PersonsList
 * 
 * Класс, позволяющий работать со списком всех людей из таблицы Persons
 * Методы:
 *  - __construct(): конструктор класса, инициализирует массив идентификаторов
 *  - getPersonsLis(): возвращает массив объектов класса Person
 *  - deletePersons(): удаляет записи о людях из базы данных в соответствии с массивом, полученным в конструкторе
 */
class PersonList
{
    private array $ids;

    public function __construct()
    {
        $sql = 'SELECT `id` FROM `persons`';
        DB::connect();
        $result = DB::getConnection()->query($sql);
        while($id = $result->fetch_assoc()) {
            $this->ids[] = $id['id'];
        }
    }

    public function getPersonsList() : array
    {
        $result_array = [];
        foreach ($this->ids as $id) {
            $result_array[] = new Person($id);
        }
        return $result_array;
    }

    public function deletePersons() : void
    {
        $persons = $this->getPersonsList();
        foreach ($persons as $person) {
            $person->deletePerson();
        }
    }
}

$persons = new PersonList();
$persons_array = $persons->getPersonsList();
print_r($persons_array);