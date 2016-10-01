<?php

/**
* Единственный контроллер приложения
*/

  namespace Controllers;

  use Framework\Response as Response;
  use Framework\html as html;
  use Framework\db as db;
  use src\students\Student as Student;
  use App\App as App;

  class Controller_students{

      /**
      * Приветственная страничка когда не выбрано никакого действия
      *
      * @param mixed $args
      */
      public function actionHello($args){
          $response = new Response(
            html::renderLayout('hello')
          );
          $response->send();
      }

      /**
      * Запись в базу данных нового студента или сохранение изменений редактирования существующего
      *
      * @param mixed $args
      */
      public function actionSave($args){
         $args = func_get_arg(0);
         extract($args);
         $errors = [];
         if(empty($first_name)){
             $errors[] = "Имя указано не верно";
         }
         if(empty($family_name)){
             $errors[] = "Поле фамилии указано не верно";
         }

         if(empty($age)){
             $errors[] = "Возраст студента не указан";
         }else{
             $age = (int)$age;
             if($age > \src\students\Student::AGE_MAX){
                $errors[] = "Указан не правильный возраст";
             }
         }
         if(!isset($sex) || !in_array($sex, [\src\students\Student::SEX_MALE, \src\students\Student::SEX_FEMALE])){
             $errors[] = "Указан несуществующий пол";
         }
         if(empty($group_name)){
             $errors[] = "Не указана группа студента";
         }
         if(empty($faculty)){
             $errors[] = "Не указан факультет студента";
         }

         if( ! empty($errors)){
             App::$message->pushList(\Framework\message::TYPE_ERROR, $errors);
             if(empty($id)){
                return $this->actionNew($args);
             }
             return $this->actionEdit($args);
         }

         $student = new \src\students\Student();
         if( ! empty($id)){
            $student->setId($id);
         }
         $student->setFirst_name($first_name);
         $student->setFamily_name($family_name);
         $student->setAge($age);
         $student->setSex($sex);
         $student->setGroup_name($group_name);
         $student->setFaculty($faculty);
         $student->save();
         App::$message->push(\Framework\message::TYPE_INFO, "Изменения были успешно сохранены");
         $response = new Response(
            html::renderLayout()
         );
         $response->send();
      }

      /**
      * Вывод списка существующих студентов
      *
      * @param mixed $args
      */
      public function actionList($args){
        $args = func_get_arg(0);
        extract($args);
        $sqlParts = '';
        $sqlParams = [];
        if( ! empty($id)){
            $sqlParts .= "\nAND id = :id";
            $sqlParams['id'] = $id;
        }

        if( ! empty($limitFrom)){
            $sqlParts .= "\nLIMIT :limitfrom";
            $sqlParams['limitfrom'] = $limitFrom;

            if( ! empty($limitCount)){
                $sqlParts .= ", :limitcount";
                $sqlParams['limitcount'] = $limitCount;
            }
         }
         $students = [];
         $res = db::query(<<<EOL
SELECT
    id,
    first_name,
    family_name,
    sex,
    age,
    group_name,
    faculty
FROM
  students
WHERE
  1 = 1
  $sqlParts
EOL
        , $sqlParams);

        while($row = db::fetch($res)){
            $students[] = new Student($row);
        }

        $response = new Response(
            html::renderLayout('student_list', ['students' => $students])
        );
        $response->send();

        return $students;
      }

      /**
      * Вывод формы для добавления нового студента
      *
      * @param mixed $args
      */
      public function actionNew($args){
          $response = new Response(
            html::renderLayout('editform', ['student' => new Student($args)])
          );
          $response->send();
      }

      /**
      * Вывод формы для редактировани студента и заполнение её данными
      * выбранного студента
      *
      * @param mixed $args
      */
      public function actionEdit($args){
          $errors = [];
          extract($args);
          if(empty($id)){
            $errors[] = "Не указан ID студента для редактирования";
          }
          App::$message->pushList(\Framework\message::TYPE_ERROR, $errors);
          $student = Student::initById($id);
          $response = new Response(
            html::renderLayout('editform', ['student' => $student])
          );
          $response->send();
      }

      /**
      * Удаление студента из базы данных
      *
      * @param mixed $args
      */
      public function actionRemove($args){
          $errors = [];
          extract($args);
          if(empty($id)){
            $errors[] = "Не указан ID студента для удаления";
          }

          $student = Student::initById($id);
          if(empty($student)){
            $errors[] = "Студент не найден";
          }


          if(empty($errors)){
            db::query("DELETE FROM students WHERE id = :id", ['id' => $id]);
            if(db::affectedRows() > 0){
                 App::$message->push(\Framework\message::TYPE_INFO, "Студент был успешно удален из базы");
            }
          }else{
            App::$message->pushList(\Framework\message::TYPE_ERROR, $errors);
          }

          $response = new Response();
          $response->redirect('/students/list');
      }

  }
