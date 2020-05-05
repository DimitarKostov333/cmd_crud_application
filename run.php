<?php

class Student {

    private $studentId;
    private $studentName;
    private $studentSurname;
    private $studentAge;
    private $studentCarriculum;
    public $message = [];

    public function add($id, $name, $surname, $age, $carriculum) {

        $subDirectory = "";

        if(is_numeric($this->clean($id)) && strlen($this->clean($id)) == 7){
            $this->studentId = $this->clean($id);
            $subDirectory = substr($this->studentId, 0, 2);
        }else{
            $this->message[] = ['error' => "Student ID is invalid."];
        }

        (ctype_alpha($this->clean($name))) ? $this->studentName = $this->clean($name) : $this->message = ['error' => "Student name is invalid."];
        (ctype_alpha($this->clean($surname))) ? $this->studentSurname = $this->clean($surname) : $this->message = ['error' => "Student surname is invalid."];
        (is_numeric($this->clean($age))) ? $this->studentAge = $this->clean($age) : $this->message = ['error' => "Student ID is not a number"];
        (ctype_alpha($this->clean($carriculum))) ? $this->studentCarriculum = $this->clean($carriculum) : $this->message = ['error' => "Student ID is not a number"];

        $studentData = [
            'id' => $this->studentId,
            'name' => $this->studentName,
            'surname' => $this->studentSurname,
            'age' => $this->studentAge,
            'carriculum' => $this->studentCarriculum
        ];

        if(empty($this->message)) {

            if(mkdir($subDirectory)){
                file_put_contents($subDirectory . "/" . $this->studentId . ".json", json_encode($studentData));
                return "Student profile " . $this->studentName . " " . $this->studentSurname . " has been added successfully.";
                exit();
            }

        } else {

            $error = "";
            foreach ($this->message as $key => $error){
                $error .= $error . "\r\n";
            }

            return $error;
            exit();

        }

    }

    public function edit($id, $name, $surname, $age, $carriculum) {
        
        $this->studentName = $this->clean($name);
        $this->studentSurname = $this->clean($surname);
        $this->studentAge = $this->clean($age);
        $this->studentCarriculum = $this->clean($carriculum);

        $studentFile = substr($this->clean($id), 0, 2) . '/' . $this->clean($id) . '.json';

        if($studentData = file_get_contents($studentFile)) {

            $decodedJson = json_decode($studentData, true);

            if(!empty($this->studentName) && ctype_alpha($this->studentName)){
                $decodedJson['name'] = $this->studentName;
            }

            if(!empty($this->studentSurname) && ctype_alpha($this->studentSurname)){
                $decodedJson['surname'] = $this->studentSurname;
            }

            if(!empty($this->studentAge) && ctype_alpha($this->studentAge)){
                $decodedJson['age'] = $this->studentAge;
            }

            if(!empty($this->studentCarriculum) && ctype_alpha($this->studentCarriculum)){
                $decodedJson['carriculum'] = $this->studentCarriculum;
            }

            if(file_put_contents($studentFile, json_encode($decodedJson))){
                return "Student profile updated successfully.";
                exit();
            }

        } else {
            return "Cannot find student ID to edit.";
            exit();
        }
        
    }

    public function delete($id){

        $this->studentId = $this->clean($id);

        if(is_numeric($this->studentId) && strlen($this->studentId) == 7) {

            if(unlink(substr($this->studentId, 0, 2) . '/' . $this->studentId . '.json')){
                rmdir(substr($this->studentId, 0, 2));
                return "Student profile deleted successfully.";
                exit();
            }

        } else {
            return "Invalid student ID.";
            exit();
        }

    }

    public function search($criteria) {

        $tableHeader = "|%-8s |%-10s |%-10s |%-4s |%-30.30s \r\n";
 
        printf($tableHeader, "id", "name", "surname", "age", "carriculum");

        foreach (glob('*/*.json') as $filename) {

            if($this->clean($criteria) != "") {

                if( strpos(file_get_contents($filename), $this->clean($criteria)) !== false) {

                    $json = json_decode(file_get_contents($filename), true);
                    printf($tableHeader, $json['id'], $json['name'], $json['surname'], $json['age'], $json['carriculum']);

                }

            } else {
                
                $json = json_decode(file_get_contents($filename), true);
                printf($tableHeader, $json['id'], $json['name'], $json['surname'], $json['age'], $json['carriculum']);

            }

        }


    }

    public function getStudentInfo($id){

        $studentFile = substr($this->clean($id), 0, 2) . '/' . $this->clean($id) . '.json';

        if($decodedJson = json_decode(file_get_contents($studentFile), true)){

            return [
                'id' => $decodedJson['id'],
                'name' => $decodedJson['name'],
                'surname' => $decodedJson['surname'],
                'age' => $decodedJson['age'],
                'carriculum' => $decodedJson['carriculum']
            ];

        }

    }

    private function clean($string) {
        return trim(preg_replace('/[^A-Za-z0-9\-]/', '', $string));
    }

}

$studentProfile = new Student();
$action = explode('=',$argv[1]);

switch($action){
    case $action[1] == "add";

        echo "Enter student ID (Must be 7 digits): ";
        $id = fgets(STDIN);

        echo "Enter student name: ";
        $name = fgets(STDIN);

        echo "Enter student surname: ";
        $surname = fgets(STDIN);

        echo "Enter student age: ";
        $age = fgets(STDIN);

        echo "Enter student carriculum: ";
        $carriculum = fgets(STDIN);

        echo $studentProfile->add($id, $name, $surname, $age, $carriculum);
        break;

    case $action[1] == "edit";

        $id = explode('=',$argv[2]);

        echo "Enter student name [" . $studentProfile->getStudentInfo($id[1])['name'] . "]: ";
        $name = fgets(STDIN);

        echo "Enter student surname [" . $studentProfile->getStudentInfo($id[1])['surname'] . "]: ";
        $surname = fgets(STDIN);

        echo "Enter student age [" . $studentProfile->getStudentInfo($id[1])['age'] . "]: ";
        $age = fgets(STDIN);

        echo "Enter student carriculum [" . $studentProfile->getStudentInfo($id[1])['carriculum'] . "]: ";
        $carriculum = fgets(STDIN);

        echo $studentProfile->edit($id[1], $name, $surname, $age, $carriculum);
        break;

    case $action[1] == "delete";

        $id = explode('=',$argv[2]);
        
        echo $studentProfile->delete($id[1]);
        break;

    case $action[1] == "search";

        echo "Please enter search criteria: ";
        $searchCriteria = fgets(STDIN);

        echo $studentProfile->search($searchCriteria);
        break;
}


?>