<?php
// set php ini settings for large file uploads
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);

// invoke autoloader
require_once('../app/includes/autoloader.php');

// instantiate UI object
$ui = new Renderer();

// process file upload
$fileProcessor = new Fileprocessor($ui);
if($fileProcessor->handleRequest()) {
    $request = new Request();
    $request->redirect('search.php');
}
