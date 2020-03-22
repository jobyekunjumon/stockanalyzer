<?php
class Fileprocessor {
    /**
     * Save uploaded file gto this specified path
     */
    const UPLOADED_FILE_NAME = '/public/uploads/stockHistory.csv';

    /**
     * Allowed file types
     */
    const ALLOWED_FILE_TYPES = ['text/csv'];

    /**
     * UI Object
     */
    public $ui = null;

    /**
     * Class constructor
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param Renderer $ui UI Object
     */
    public function __construct(Renderer $ui) {
        $this->ui = $ui;
    } // end : function __construct

    /**
     * Handle file upload request
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return bool
     */
    public function handleRequest() {
        $response = false;
        $request = new Request(); 
       
        if($request->hasFiles()) {
            $files = $request->getFiles();
            try {
                $validationErrors = $this->validateRequest($files);
                if($validationErrors) {
                    $this->ui->setErrorMessage($validationErrors);
                } else {
                    $response = move_uploaded_file($files['stockValueHistory']['tmp_name'], realpath(dirname(getcwd())).self::UPLOADED_FILE_NAME);
                    
                    // for every new file upload, rebuild the stock details cache
                    if($response) {
                        $this->processFile();
                    } // end: if
                } // end: if
            } catch (Exception $ex) {
                $this->ui->setErrorMessage('Something went wrong while processing your request. Please try again.');
            } // end: catch

        } // end: if

        return $response;
    } // end: function handleRequest

    /**
     * Validate file upload
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $data Input Data
     * @return String validationErrors
     */
    public function validateRequest(Array $data):String {
        $validationErrors = '';
        
        if(empty($data['stockValueHistory'])) {
            $validationErrors = 'Stock Value History file is empty.';
        } else if(empty($data['stockValueHistory']['size'])) {
            $validationErrors = 'Stock Value History file is empty.';
        } else if(!in_array($data['stockValueHistory']['type'], self::ALLOWED_FILE_TYPES)) {
            $validationErrors = 'File format is not supported.';
        } // end: if

        return $validationErrors;
    } // end: function validateRequest

    /**
     * Iterate throgh the lines of csv file and prepare the array formated data.
     * Save this formated data into a temporary storage for quick access
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return array $stockDetails
     */
    public function processFile() {
        $stockDetails = [];

        if(is_file(realpath(dirname(getcwd())).self::UPLOADED_FILE_NAME)) {
            $stockHistoryFP = fopen(realpath(dirname(getcwd())).self::UPLOADED_FILE_NAME, 'r');
            // skip the csv header
            fgetcsv($stockHistoryFP);
            // loop through each lines in csv and prepare the stockDetails array
            while (($lineData = fgetcsv($stockHistoryFP)) !== false) {
                try{
                    // prepare the stockDetails array
                    $date = new DateTime($lineData[1]);
                    $stockDetails[$lineData[2]][$date->format('Y-m-d')] = $lineData[3];
                } catch(Exception $ex) {
                    // if any rows has issues while processing, continue to the next line 
                    continue;
                }
            } // end: while

            // write to cache if successfully created the data array
            if(!empty($stockDetails)) {
                $sessionAdapter = new Sessionadapter();
                $sessionAdapter->set('stockDetails', $stockDetails);
            } // end: if
        } // end: if

        return $stockDetails;
    }
}