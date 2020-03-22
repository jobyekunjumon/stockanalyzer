<?php
class Request {
    /**
     * variable to hold post parameters
     */
    private $post;

    /**
     * variable to hold request parameters
     */
    private $request;

    /**
     * variable to hold file uploads
     */
    private $files;

    /**
     * Class constructor
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     */
    public function __construct() {
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->files = $_FILES;
    } // end: function __construct

    /**
     * Check whether request is post request
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return bool 
     */
    public function isPost() {
        return !empty($this->post) ?? false;
    } // end: function isPost

    /**
     * Return post request parameters
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return Array $post
     */
    public function getPost() {
        return $this->post;
    } // end: function getPost

    /**
     * Return request parameters
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return Array $post
     */
    public function getRequest() {
        return $this->request;
    } // end: function getRequest

    /**
     * Check whether request has files
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return bool 
     */
    public function hasFiles() {
        return !empty($this->files) ?? false;
    } // end: function hasFiles

    /**
     * Return uploded files array
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return Array $files
     */
    public function getFiles() {
        return $this->files;
    } // end: function getFiles

    /**
     * Redirect to a particular url using relative path
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return Array $files
     */
    public function redirect(String $url) {
        if(!empty($url)) {
            ob_start();
            header('Location: '.$url);
        }
    } // end: function redirect
}