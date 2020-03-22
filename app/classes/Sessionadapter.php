<?php
class Sessionadapter {
    

    /**
     * Class constructor
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param Renderer $ui UI Object
     */
    public function __construct() {
        ob_start();
        session_start();
    } // end : function __construct

    /**
     * write data to storage
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return bool
     */
    public function set(String $key, $data): Bool {
        $response = false;
        
        if(!empty($key)) {
            $_SESSION[$key] = $data;
            $response = true;
        } // end: if

        return $response;
    } // end: function handleRequest

    /**
     * get data storage
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return bool
     */
    public function get(String $key) {
        return $_SESSION[$key] ?? null;
    } // end: function handleRequest
}