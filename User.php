	<?php
02	class User {
03	    protected $name;
04	 
05	    public function getName() {
06	        return $this->name;
07	    }
08	 
09	    public function setName($name) {
10	        $this->name = $name;
11	    }
12	 
13	    public function talk() {
14	        return "Hello world!";
15	    }
16	}
