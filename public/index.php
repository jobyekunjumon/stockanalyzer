<?php 
require_once('../app/controllers/indexController.php'); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock Value Analyser</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">Stock Value Analyser</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <section class="main-container">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card text-center">
                        <div class="card-header bg-warning">
                            <h2>Upload Stock File</h2>
                        </div>
                        <div class="card-body">
                            <?php 
                                if(!empty($ui->getErrorMessage())) {
                                    echo "<div class='alert alert-danger'>{$ui->getErrorMessage()}</div>";
                                }
                                if(!empty($ui->getMessage())) {
                                    echo "<div class='alert alert-success'>{$ui->getMessage()}</div>";
                                }
                            ?>
                            <form name="frmUploadFile" method="post" action="" enctype="multipart/form-data" >
                                <p class="alert alert-info">Upload your stock value history CSV file to get started !</p>
                                <div class="row">
                                    <div class="col-lg-3 mx-auto">
                                        <input type="file" name="stockValueHistory" id="stockValueHistory">
                                    </div>
                                    <div class="col-lg-3 mx-auto">
                                        <button type="submit"  class="btn btn-primary">Upload</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="pagination">
                                <a href="#" class="active"></li>
                                <a href="#"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
  
  <footer class="bg-dark">
    <div class="container">
      <p class="m-0 text-center text-white">***</p>
    </div>
  </footer>
  <script type="text/javascript" src="js/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>

</html>