<?php
class Super {
    protected $fillable;
    public function __construct() {
        foreach($this->fillable as $key => $val) {
            $this->$val = nil;
        }

    }
}

class Sub extends Super {
    protected $fillable = ["hi", "bye"];
    public function Sub() {
        $parameters = func_get_args();
        foreach($parameters as $p) {
            $key = array_shift($this->fillable);
            $this->$key = $p;
        }
    }
}

$s = new Sub(1,2);
echo $s->hi;
echo $s->bye;


