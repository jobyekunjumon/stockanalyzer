<?php
// invoke autoloader
require_once('../app/includes/autoloader.php');

// instantiate UI object
$ui = new Renderer();

// process file upload
$stockAnalyser = new Stockanalyser($ui);
$stockSuggestions = $stockAnalyser->getStockSuggestions();

