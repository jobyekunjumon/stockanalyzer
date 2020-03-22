<?php
class Stockanalyser {

    /**
     * UI Object
     */
    public $ui = null;

    /**
     * Default purchase quantity
     */
    const DEFAULT_PURCHASE_QUANTITY = 200;

    /**
     * Class constructor
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param Renderer $ui UI Object
     */
    public function __construct(Renderer $ui) {
        $this->ui = $ui;
    } // end : function __construct
    
    /**
     * get stock suggestions
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return array suggestions
     */
    public function getStockSuggestions(): Array {
        $suggestions = [];
        $request = new Request(); 
       
        if($request->isPost()) {
            $post = $request->getPost();
            // sanitize input data
            $cleanData = $this->sanitizeInputData($post);
            
            // assign cleaned data to the frmData variable to populate in form fields in view
            $suggestions['frmData'] = $cleanData;
            $validationErrors = $this->validateRequest($post);
            if($validationErrors) {
                $this->ui->setErrorMessage($validationErrors);
            } else { 
                try {
                    $stockDetails = $this->getStockDetails();
                    // check if stock name exist
                    if(!empty($stockDetails[$cleanData['companyName']])) {
                        // soryt stocks by date
                        ksort($stockDetails[$cleanData['companyName']]);
                        // get stock values in given date range
                        $stocksInDateRange = $this->getStocksInDateRange($stockDetails[$cleanData['companyName']], new DateTime($cleanData['dateFrom']), new DateTime($cleanData['dateTo'].' 23:59:59'));
                        if(!empty($stocksInDateRange)) {
                            ksort($stocksInDateRange['stocks']);
                            $tmpStockInDateRange = $stocksInDateRange['stocks'];
                            $minPricedStockEntry = array_shift($tmpStockInDateRange);
                            $maxPricedStockEntry = array_pop($tmpStockInDateRange);

                            $suggestions['stockHistory'] = $stocksInDateRange['stockHistory'];
                            $suggestions['minPricedDates'] = $minPricedStockEntry['dates'];
                            $suggestions['maxPricedDates'] = $maxPricedStockEntry['dates'];
                            $suggestions['minPrice'] = $minPricedStockEntry['price'];
                            $suggestions['maxPrice'] = $maxPricedStockEntry['price'];
                            $suggestions['meanStockValue'] = $stockDetails[$cleanData['companyName']] ? (array_sum($stockDetails[$cleanData['companyName']])/count($stockDetails[$cleanData['companyName']])) : 0;
                            $suggestions['standardDeviation'] = $this->getStandardDeviation($stockDetails[$cleanData['companyName']], $suggestions['meanStockValue']) ?? [];
                        } else {
                            $this->ui->setErrorMessage('Could not find any stock values for the given date range.');
                        } //end: if
                    } else {
                        $this->ui->setErrorMessage('Stock value for this company does not exist.');
                    } // end: if
                } catch(Exception $ex) {
                    $this->ui->setErrorMessage('Something went wrong while processing your request.');
                }      
            } //end: if
        } // end: if

        // add default purchase quantity
        $suggestions['defaultPurchaseQuantity'] = self::DEFAULT_PURCHASE_QUANTITY;

        return $suggestions;
    } // end: function getStockSuggestions

    /**
     * get standard deviation 
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $stockPrices Array contains the stock values of particular company
     * @param int $averageStockPrice
     * @return array $standardDeviations Array contains the total standard deviation and each days deviation
     */
    public function getStandardDeviation(Array $stockPrices, Int $averageStockPrice): Float {
        $standardDeviation = 0;
        $variance = 0;

        foreach($stockPrices as $stockPrice) { 
            $variance += pow(($stockPrice - $averageStockPrice), 2); 
        } // end: foreach

        if($variance) {
            $standardDeviation = (float)sqrt($variance/count($stockPrices)); 
        } // end: if

        return $standardDeviation;
    } // end: function getStandardDeviation

    /**
     * get stocks between the given date range 
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $stock Array contains the stock value of particular company
     * @param date $dateFrom
     * @param date $dateTo
     * @return array $stocksInDateRange
     */
    public function getStocksInDateRange(Array $stock, DateTime $dateFrom, DateTime $dateTo): Array {
        $stocksInDateRange = [];

        // get all dates between the start and end dates
        $dateRange = new DatePeriod($dateFrom, new DateInterval('P1D'), $dateTo);
        // iterate throgh the date ranges, and add the dates to the result array if stock value exists for that date
        foreach ($dateRange as $dateInstance) {
            $date = $dateInstance->format('Y-m-d');    
            if(!empty($stock[$date])) {
                $stocksInDateRange['stocks'][$stock[$date]]['dates'][] = $date;
                $stocksInDateRange['stocks'][$stock[$date]]['price'] = $stock[$date];
                $stocksInDateRange['stockHistory'][$date] = $stock[$date];
            } // end: if
        } // end: foreach

        return $stocksInDateRange;
    } // end: function getStockDetails

    /**
     * get stockDetails 
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @return array $stockDetails
     */
    public function getStockDetails(): Array {
        $stockDetails = [];

        // check whether the local storage has stockdetails data
        $sessionAdapter = new Sessionadapter();
        $stockDetails = $sessionAdapter->get('stockDetails');
        
        // if stockDetails is not in cache storage, get it by processing the file and write throgh the cache
        if(empty($stockDetails)) {
            $fileProcessor = new Fileprocessor($this->ui);
            $stockDetails = $fileProcessor->processFile();
        } //end: if

        return $stockDetails;
    } // end: function getStockDetails

    /**
     * get sanitized input data
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $data Input Data
     * @return array $cleanData Clean Data
     */
    public function sanitizeInputData(Array $data): Array {
        $cleanData = [];

        if(!empty($data['companyName'])) {
            $cleanData['companyName'] = filter_var($data['companyName'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        }

        if(!empty($data['dateFrom'])) {
            $cleanData['dateFrom'] = filter_var($data['dateFrom'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        }

        if(!empty($data['dateTo'])) {
            $cleanData['dateTo'] = filter_var($data['dateTo'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        }

        return $cleanData;
    } // end: function sanitizeInputData

    /**
     * Validate searach request
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $data Input Data
     * @return String validationErrors
     */
    public function validateRequest(Array $data):String {
        $validationErrors = '';
        
        if(empty(trim($data['companyName']))) {
            $validationErrors .= 'Company Name is empty. ';
        }
        if(empty(trim($data['dateFrom']))) {
            $validationErrors .= 'Date From is empty. ';
        } else if(!$this->isValidDate($data['dateFrom'])) {
            $validationErrors .= 'Date From is not valid. ';
        } if(empty(trim($data['dateTo']))) {
            $validationErrors .= 'Date To is empty. ';
        } else if(!$this->isValidDate($data['dateTo'])) {
            $validationErrors .= 'Date To is not valid. ';
        } else if(strtotime($data['dateFrom']) > strtotime($data['dateTo'])) {
            $validationErrors .= 'To Date is greater than From Date. ';
        }

        return $validationErrors;
    } // end: function validateRequest

    /**
     * Validate date
     * @author Joby E Kunjumon <jobyekunjumon@gmail.com>
     * @param array $dateString
     * @return Bool $isValidDate
     */
    public function isValidDate(String $dateString): Bool {
        $isValidDate = false;

        $date = DateTime::createFromFormat('d-m-Y', $dateString);
        $isValidDate = ($date && $date->format('d-m-Y') === $dateString);

        return $isValidDate;
    } // end: function isValidDate

}