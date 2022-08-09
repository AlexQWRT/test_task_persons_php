<?php

/**
 * Автор: Диденко Алексей
 *
 * Дата реализации: 08.08.2022 14:00
 *
 * Дата изменения: 09.08.2022 17:25
 *
 * Класс для работы с таблицей persons
*/
require_once('DB.php');
/**
 * Класс Person
 * 
 * Класс, позволяющий работать с таблицей persons в базе данных
 * Методы:
 *  - __construct(int $id, ?string $name = NULL, ?string $surname = NULL, ?string $dateOfBirth = NULL, ?bool $sex = NULL, ?string $cityOfBirth = NULL):
 *      заполняет поля класса данными из БД если передать только id, либо заполняет поля класса данными, переданными через аргументы, и сохраняет данные в базу данных;
 *      выполняет валидацию данных перед их сохранением; в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - getId(): возвращает значение поля id данного экземпляра класса, int
 *  - getName(): возвращает значение поля name данного экземпляра класса, string
 *  - getSurname(): возвращает значение поля surname данного экземпляра класса, string
 *  - getDateOfBirthObject(): возвращает значение поля dateOfBirth данного экземпляра класса, DateTimeImmutable
 *  - getDateOfBirth(): возвращает отформатированное значение поля dateOfBirth данного экземпляра класса, string
 *  - getSex(): возвращает значение поля sex данного экземпляра класса, bool
 *  - getCityOfBirth(): возвращает значение поля city данного экземпляра класса, string
 *  - setId(int $id): задаёт значение поля id данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - setName(string $name): задаёт значение поля name данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - setSurname(string $surname): задаёт значение поля surname данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - setDateOfBirth(string $dateOfBirth): задаёт значение поля dateOfBirth данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - setSex(bool $sex): задаёт значение поля sex данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - setCityOfBirth(string $cityOfBirth): задаёт значение поля cityOfBirth данного экземпляра класса; выполняет валидацию данных перед их сохранением
 *  - validateId(int $id): выполняет валидацию переменной по правилам, описанным для поля id; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - validateName(string $name): выполняет валидацию переменной по правилам, описанным для поля name; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - validateSurname(string $surname): выполняет валидацию переменной по правилам, описанным для поля surname; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - validateDateOfBirth(string $dateOfBirth): выполняет валидацию переменной по правилам, описанным для поля dateOfBirth; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - validateSex(bool $sex): выполняет валидацию переменной по правилам, описанным для поля sex; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - validateCityOfBirth(string $cityOfBirth): выполняет валидацию переменной по правилам, описанным для поля cityOfBirth; 
 *      в случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
 *  - savePerson(): сохраняет значения полей данного экземпляра класса в базе данных;
 *      в случае ошибки выбрасывает RuntimeException с сообщением об ошибке
 *  - findPerson($id): возвращает ассоциативный массив с данными о человеке, чей id был передан в метод, array
 *  - deletePerson(): удаляет из базы данных человека, чьи данные хранятся в полях экземпляра класса
 *  - static getPersonAge($person): возвращает возраст человека в годах, int
 *  - static getPersonSex($person): возвращает отформатированный вид пола человека, string
 *  - getStdClass(): возвращает новый экземпляр StdClass со всеми полями изначального класса, отформатированным полом и возрастом вместо даты рождения
 */
class Person
{
    private int $id;
    private string $name;
    private string $surname;
    private DateTimeImmutable $dateOfBirth;
    private bool $sex;
    private string $cityOfBirth;

    private function validateId(int $id) : void
    {
        if (filter_var($id, FILTER_VALIDATE_INT, array('options' => array('default' => NULL, 'min_range' => 1))) == NULL) {
            throw new RuntimeException('Invalid id entered for person! Only integer positive digits are valid.');
        }
    }

    private function validateName(string $name) : void
    {
        if (!ctype_alpha(strval($name)) || strlen(strval($name)) > 100) {
            throw new RuntimeException('Invalid name entered for person! Only letters are valid.');
        }
    }

    private function validateSurname(string $surname) : void
    {
        if (!ctype_alpha(strval($surname)) || strlen(strval($surname)) > 100) {
            throw new RuntimeException('Invalid surname entered for person! Only letters are valid.');
        }
    }

    private function validateDateOfBirth(string $dateOfBirth) : void
    {
        if (filter_var($dateOfBirth, FILTER_VALIDATE_REGEXP, array('options' => 
                array('default' => NULL, 'regexp' => '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/'))) == NULL) {
                throw new RuntimeException('Invalid date of birth entered for person! Valid format: YYYY-MM-DD.');
            }
    }

    private function validateSex(bool $sex) : void
    {
        if (filter_var($sex, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) === NULL) {
            throw new RuntimeException('Invalid sex entered for person! Only boolean values are valid.');
        }
    }

    private function validateCityOfBirth(string $cityOfBirth) : void
    {
        if (!ctype_alpha(strval($cityOfBirth)) || strlen(strval($cityOfBirth)) > 100) {
            throw new RuntimeException('Invalid city of birth entered for person! Only letters are valid.');
        }
    }

    /**
     * Конструктор
     * 
     * Заполняет поля класса данными из БД если передать только id,
     * либо заполняет поля класса данными, переданными через аргументы, и сохраняет данные в БД
     * 
     * Выполняет валидацию данных перед их сохранением
     * 
     * В случае ошибки влидации выбрасывает RuntimeException с сообщением об ошибке
     */
    public function __construct(int $id, ?string $name = NULL, ?string $surname = NULL, ?string $dateOfBirth = NULL, ?bool $sex = NULL, ?string $cityOfBirth = NULL)
    {
        if ($name == NULL || $surname == NULL || $dateOfBirth == NULL || $sex === NULL || $cityOfBirth == NULL) {
            $fetched_array = self::findPerson($id);
            $this->id = $id;
            $this->name = $fetched_array['name'];
            $this->surname = $fetched_array['surname'];
            $this->dateOfBirth = new DateTimeImmutable("{$fetched_array['date_of_birth']}");
            $this->sex = $fetched_array['sex'];
            $this->cityOfBirth = $fetched_array['city_of_birth'];
        } else {
            $this->validateId($id);
            $this->validateName($name);
            $this->validateSurname($surname);
            $this->validateDateOfBirth($dateOfBirth);
            $this->validateSex($sex);
            $this->validateCityOfBirth($cityOfBirth);
            $this->id = $id;
            $this->name = $name;
            $this->surname = $surname;
            $this->dateOfBirth = new DateTimeImmutable($dateOfBirth);
            $this->sex = $sex;
            $this->cityOfBirth = $cityOfBirth;
            $this->savePerson();
            
        }
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->validateId($id);
        $this->id = $id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->validateName($name);
        $this->name = $name;
    }

    public function getSurname() : string
    {
        return $this->surname;
    }

    public function setSurname(string $surname) : void
    {
        $this->validateSurname($surname);
        $this->surname = $surname;
    }

    public function getDateOfBirthObject() : DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function getDteOfBirth() : string
    {
        return $this->dateOfBirth->format('Y-m-d');
    }

    public function setDateOfBirth(string $dateOfBirth) : void
    {
        $this->validateDateOfBirth($dateOfBirth);
        $this->dateOfBirth = new DateTimeImmutable($dateOfBirth);
    }

    public function getSex() : bool
    {
        return $this->sex;
    }

    public function setSex(bool $sex) : void
    {
        $this->validateSex($sex);
        $this->sex = $sex;
    }

    public function getCityOfBirth() : string
    {
        return $this->cityOfBirth;
    }

    public function setCityOfBirth(string $cityOfBirth) : void
    {
        $this->validateCityOfBirth($cityOfBirth);
        $this->cityOfBirth = $cityOfBirth;
    }

    private function savePerson() : void
    {
        DB::connect();
        $sex = $this->sex ? 1 : 0;
        $sql = "INSERT INTO `persons` (`id`, `name`, `surname`, `date_of_birth`, `sex`, `city_of_birth`)
            VALUES ({$this->id}, '{$this->name}', '{$this->surname}', '{$this->dateOfBirth->format('Y-m-d')}',
            {$sex}, '{$this->cityOfBirth}')";
        $result = DB::getConnection()->real_query($sql);
        if ($result == false)
        {
            throw new RuntimeException('Failed to save data!');
        }
    }

    public static function findPerson(int $id) : array
    {
        DB::connect();
        $sql = "SELECT * FROM `persons` WHERE `id`={$id};";
        $result = DB::getConnection()->query($sql);
        $fetched_array = $result->fetch_assoc();
        if ($result == false || $fetched_array == NULL)
        {
            throw new RuntimeException('Most likely such an id is not in the database!');
        }
        return $fetched_array;
    }

    public function deletePerson() : void
    {
        DB::connect();
        $sql = "DELETE FROM `persons` WHERE `id`={$this->id};";
        $result = DB::getConnection()->real_query($sql);
        if ($result == false)
        {
            throw new RuntimeException('Most likely such a person is not in the database!');
        }
    }

    public static function getPersonAge($person) : int
    {
        $now = new DateTimeImmutable();
        $diff = $now->diff($person->getDateOfBirthObject());
        return $diff->y;
    }

    public static function getPersonSex($person) : string
    {
        if ($person->getSex()) {
            return 'муж';
        }
        return 'жен';
    }

    public function getStdClass() : stdClass
    {
        $result = new stdClass();
        $result->id = $this->id;
        $result->name = $this->name;
        $result->surname = $this->surname;
        $result->dateOfBirth = self::getPersonAge($this);
        $result->sex = self::getPersonSex($this);
        $result->cityOfBirth = $this->cityOfBirth;
        var_dump($result);
        return $result;
    }
}

$person = new Person(1);
$person->getStdClass();