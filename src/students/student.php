<?php
/**
* Сущность студент. Соответствует патерну ActiveRecord
*
*/
  namespace src\students;
  use Framework\db as db;
  class Student{

    /** мужской пол */
    const SEX_MALE = 1;
    /** женский пол */
    const SEX_FEMALE = 0;
    /** максимальный возраст */
    const AGE_MAX = 127;

    /** ID студента в базе данных */
    private $id;

    /** Другие поля */
    private $first_name;
    private $family_name;
    private $sex;
    private $age;
    private $group_name;
    private $faculty;

    /**
    * Могут быть переданы данные для заполнения
    * полей объекта
    *
    * @param array $data
    */
    public function __construct($data=null){
        if( ! empty($data['id'])){
            $this->setId($data['id']);
        }

        if( ! empty($data['first_name'])){
            $this->setFirst_name($data['first_name']);
        }
        if( ! empty($data['family_name'])){
            $this->setFamily_name($data['family_name']);
        }
        if( ! empty($data['sex'])){
            $this->setSex($data['sex']);
        }
        if( ! empty($data['age'])){
            $this->setAge($data['age']);
        }
        if( ! empty($data['group_name'])){
            $this->setGroup_name($data['group_name']);
        }
        if( ! empty($data['faculty'])){
            $this->setFaculty($data['faculty']);
        }
    }

    /**
    * Присвоить ID студента
    *
    * @param int $val
    */
    public function setId($val){
        if( ! empty( $this->id )){
            return false;
        }
        $this->id = $val;
    }

    /**
    * Присвоить имя студенту
    *
    * @param string $val
    */
    public function setFirst_name($val){
        $this->first_name = $val;
    }

    /**
    * Присвоить фамилию студента
    *
    * @param string $val
    */
    public function setFamily_name($val){
        $this->family_name= $val;
    }

    /**
    * Присвоить пол студенту
    *
    * @param int $val
    */
    public function setSex($val){
        if($val != self::SEX_MALE && $val != self::SEX_FEMALE){
            throw new Exception("Неверно указан пол студента");
        }
        $this->sex = $val;
    }

    /**
    * Присвоить возраст стуженту
    *
    * @param int $val
    */
    public function setAge($val){
        $this->age = (int)$val;
    }

    /**
    * Присвоить название группы
    *
    * @param string $val
    */
    public function setGroup_name($val){
        $this->group_name = $val;
    }

    /**
    * Присвоить название факультета
    *
    * @param string $val
    */
    public function setFaculty($val){
        $this->faculty = $val;
    }

    /**
    * Получить ID студента
    *
    * @return int
    */
    public function getId(){
        return $this->id;
    }

    /**
    * Получить имя студента
    *
    * @return string
    */
    public function getFirst_name(){
        return $this->first_name;
    }

    /**
    * Получить фамилию студента
    *
    * @return string
    */
    public function getFamily_name(){
        return $this->family_name;
    }

    /**
    * Получить пол студента
    *
    * @return int
    */
    public function getSex(){
        return $this->sex;
    }

    /**
    * Получить возраст студента
    *
    * @return int
    */
    public function getAge(){
        return $this->age;
    }

    /**
    * Получить название группы студента
    *
    * @return string
    */
    public function getGroup_name(){
        return $this->group_name;
    }

    /**
    * Получить название факультета студента
    *
    * @return string
    */
    public function getFaculty(){
        return $this->faculty;
    }

    /**
    * Сохранение записи в БД
    *
    * @return string
    */
    public function save(){
        if( ! isset($this->id)){
            db::query(<<<EOL
INSERT INTO `students` (first_name, family_name, sex, age, group_name, faculty)
VALUE (':first_name', ':family_name', :sex, :age, ':group_name', ':faculty')
EOL
        , ['first_name'  => $this->first_name,
           'family_name' =>$this->family_name,
           'sex' => $this->sex,
           'age' => $this->age,
           'group_name' => $this->group_name,
           'faculty' => $this->faculty]);
      }else{
           db::query(<<<EOL
UPDATE
  students
SET
  first_name = ':first_name',
  family_name = ':family_name',
  sex = :sex,
  age = :age,
  group_name = ':group_name',
  faculty = ':faculty'
WHERE
  id = :id;
EOL
           ,
          ['first_name'  => $this->first_name,
           'family_name' =>$this->family_name,
           'sex' => $this->sex,
           'age' => $this->age,
           'group_name' => $this->group_name,
           'faculty' => $this->faculty,
           'id' => $this->id]
          );
      }
      return db::affectedRows() > 0;
    }

    /**
    * Получить объект студента по ID
    *
    * @return Student
    */
    public static function initById($id){
        $data = db::fetch(db::query(<<<EOL
SELECT id, first_name, family_name, sex, age, group_name, faculty
FROM `students`
WHERE id = :id
EOL
    , ['id' => (int)$id]));
        if( ! empty($data)){
            return new self($data);
        }
        return false;
    }

  }

