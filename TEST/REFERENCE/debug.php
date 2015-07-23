<?php
/*
// Load the main class
require_once 'HTML/QuickForm2.php';

echo var_dump(get_included_files());
echo var_dump(get_include_path());
// Instantiate the HTML_QuickForm2 object
$form = new HTML_QuickForm2('tutorial');

// Set defaults for the form elements
$form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
    'name' => 'Joe User'
)));

// Add some elements to the form
$fieldset = $form->addElement('fieldset')->setLabel('QuickForm2 tutorial example');
$name = $fieldset->addElement('text', 'name', array('size' => 50, 'maxlength' => 255))
    ->setLabel('Enter your name:');
$fieldset->addElement('submit', null, array('value' => 'Send!'));

// Define filters and validation rules
$name->addFilter('trim');
$name->addRule('required', 'Please enter your name');

// Try to validate a form
if ($form->validate()) {
    echo '<h1>Hello, ' . htmlspecialchars($name->getValue()) . '!</h1>';
    exit;
}

// Output the form
echo $form;

function k($arr) {
    extract($arr);
    echo $name;
    echo $age;
}

echo "extract test";
k(array("name"=>"hi", "age"=>"4"));
echo "extract test";

trait Tools {
    public function doSthCrazy() {
        echo "Kill Bill".$name;
    }
}
class ClassBuilder
{

    private $className;

    private $superClass;

    private $interface;

    public $properties = array();

    private $methods = array();

    public static function startBuild($className)
    {
        return new self($className);
    }

    private function __construct($className)
    {
        $this->className = $className;
    }

    public function extend($superClass)
    {
        $this->superClass = $superClass;
        return $this;
    }

    public function implement($interface)
    {
        $this->interface = $interface;
        return $this;
    }


    public function addProperty($scope, $name)
    {
        $this->properties[] = array(
            "scope" => $scope,
            "name" => $name,
        );

        return $this;
    }
    public function addProperties($name)
    {
        foreach($name as $n) {
        $this->properties[] = array(
            "scope" => "public",
            "name" => $n,
        );}

        return $this;
    }

    public function addMethod($scope, $signature, $contents)
    {
        $this->methods[] = array(
            "scope" => $scope,
            "signature" => $signature,
            "contents" => $contents,
        );

        return $this;
    }

    public function toString()
    {

        $className = $this->className;
        $superClass = empty($this->superClass) ? "" : " extends {$this->superClass}";
        $interface = empty($this->interface) ? "" : " implements {$this->interface}";

        $properties = array();
        foreach ($this->properties as $property) {

            $scope = empty($method["scope"]) ? "" : $property["scope"];
            $name = empty($method["name"]) ? "" : $property["name"] . ";";

            $properties[] = sprintf("%s %s", $scope, $name);
        }

        $methods = array();
        foreach ($this->methods as $method) {

            $scope = empty($method["scope"]) ? "" : $method["scope"];
            $signature = empty($method["signature"]) ? "" : $method["signature"];
            $contents = empty($method["contents"]) ? "" : $method["contents"];

            $methods[] = sprintf(
                "%s function %s {" .
                "%s" .
                "}"
                , $scope, $signature, $contents
            );
        }

        $class = sprintf(
            "class %s%s%s {use Tools;"
            . "%s"
            . "%s"
            . "}"
            , $className, $superClass, $interface, implode(" ", $properties), implode(" ", $methods)
        );

        return $class;
    }

    public function build()
    {
        eval($this->toString());
    }
}

ClassBuilder::startBuild("Person")
    ->addProperty("protected", "name")
    ->addProperty("protected", "age")
    ->addProperty("protected", "gender")
    ->addProperty("protected", "say")
    ->addMethod("public", 'Person($name, $age, $gender)',
        '$this->name   = $name;'
        . '$this->age    = $age;'
        . '$this->gender = $gender;'
    )
    ->addMethod("public", 'getName()',
        'return $this->name;'
    )
    ->addMethod("public", 'getAge()',
        'return $this->age;'
    )
    ->addMethod("public", 'getGender()',
        'return $this->gender;'
    )
    ->addMethod("public", "toString()",
        'return sprintf("[name : %s, age : %s, gender : %s]", $this->name, $this->age, $this->gender);'
    )
    ->addMethod("public", "say()",
        '$a = 100;' .
        'echo $a;'
    )
    ->build();


$person = new Person("person", 20, "unknown");
$person->doSthCrazy();

function createEverything($classname, $fs) {
    $c=ClassBuilder::startBuild($classname)->addProperties($fs)->build();

}

createEverything("God",array("gender", "age"));
$g = new God();
echo var_dump(get_class_vars(get_class($person)));
echo new ReflectionObject($person).get_defined_vars();
echo var_dump(get_class_vars(get_class($g)));
*/

require_once 'connect.php';
require_once 'staff_class.php';
require_once 'seller_class.php';
print_r(Connection::get_all_from_table('Seller'));
