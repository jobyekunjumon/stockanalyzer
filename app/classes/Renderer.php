<?php
class Renderer {
    /**
     * variable to hold post parameters
     */
    private $message;

    /**
     * variable to hold request parameters
     */
    private $errorMessage;


    /**
     * Get message property
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return String $message 
     */
    public function getMessage() {
        return $this->message;
    } // end: function getMessage

    /**
     * Set message property
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param String $message 
     */
    public function setMessage(String $message) {
        $this->message = $message ?? null;
    } // end: function setMessage

    /**
     * Get errorMessage property
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return String $errorMessage 
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    } // end: function getErrorMessage

    /**
     * Set errorMessage property
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param String $errorMessage 
     */
    public function setErrorMessage(String $errorMessage) {
        $this->errorMessage = $errorMessage ?? null;
    } // end: function setErrorMessage
}