<?php 
require_once('../app/controllers/searchController.php'); 
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
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
                <div class="col-lg-12 mx-auto">
                    <div class="card text-center">
                        <div class="card-header bg-warning">
                            <h2>Search</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-10 mx-auto">
                                    <?php 
                                        if(!empty($ui->getErrorMessage())) {
                                            echo "<div class='alert alert-danger'>{$ui->getErrorMessage()}</div>";
                                        }
                                        if(!empty($ui->getMessage())) {
                                            echo "<div class='alert alert-success'>{$ui->getMessage()}</div>";
                                        }
                                    ?>
                                    <form name="frmSearch" method="post" action="" >
                                        <div class="row">
                                            <div class="form-group col-lg-6 mx-auto">
                                                <input type="text" class="form-control" placeholder="Comapny Name" name="companyName" id="companyName" value="<?php echo $stockSuggestions['frmData']['companyName'] ?? ''; ?>" >
                                            </div>
                                            <div class="form-group col-lg-2 mx-auto">
                                                <input type="text" class="form-control datepicker" placeholder="Date From" name="dateFrom" id="dateFrom" value="<?php echo $stockSuggestions['frmData']['dateFrom'] ?? ''; ?>">
                                            </div>
                                            <div class="form-group col-lg-2 mx-auto">
                                                <input type="text" class="form-control datepicker" placeholder="Date To" name="dateTo" id="dateTo" value="<?php echo $stockSuggestions['frmData']['dateTo'] ?? ''; ?>">
                                            </div>
                                            <div class="form-group col-lg-2 mx-auto">
                                                <button type="submit"  class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php if(!empty($stockSuggestions['stockHistory'])) : ?>
                                <div class="row">
                                    <?php if(!empty($stockSuggestions['minPrice'])) : ?>
                                        <div class="col-lg-4 mx-auto">
                                            <div class="card text-white bg-success mb-3" >
                                                <div class="card-header">BUY stocks of <b><?php echo $stockSuggestions['frmData']['companyName'] ?? ''; ?></b></div>
                                                <div class="card-body">
                                                    <h5 class="card-title">At INR : <?php echo number_format((float)$stockSuggestions['minPrice'],2); ?></h5> 
                                                    <h5 class="card-title">On Date(s)  </h5> <?php echo (!empty($stockSuggestions['minPricedDates']) && is_array($stockSuggestions['minPricedDates'])) ? implode('<br />',$stockSuggestions['minPricedDates']) : ''; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(!empty($stockSuggestions['maxPrice']) && $stockSuggestions['maxPrice'] != current($stockSuggestions['stockHistory'])) : ?>
                                        <div class="col-lg-4 mx-auto">
                                            <div class="card text-white bg-danger mb-3" >
                                                <div class="card-header">SELL stocks of <b><?php echo $stockSuggestions['frmData']['companyName'] ?? ''; ?></b></div>
                                                <div class="card-body">
                                                    <h5 class="card-title">At INR : <?php echo number_format((float)$stockSuggestions['maxPrice'],2); ?></h5> 
                                                    <h5 class="card-title">On Date(s) </h5> <?php echo (!empty($stockSuggestions['maxPricedDates']) && is_array($stockSuggestions['maxPricedDates'])) ? implode('<br />',$stockSuggestions['maxPricedDates']) : ''; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-lg-10 mx-auto">
                                        <table class="table table-dark">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Action</th>
                                                    <th scope="col">Profit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $stockCount = 0;
                                                $priceBought = 0;
                                                $defaultPurchaseQty = $stockSuggestions['defaultPurchaseQuantity'] ?? 0;
                                                foreach($stockSuggestions['stockHistory'] as $date => $stockPrice) :  
                                                    $txtBuyOrSell = '--';
                                                    $txtProfit = '';
                                                    // display buy text for the first day
                                                    if(!$stockCount) {
                                                        $txtBuyOrSell = '<span class="text-success">BUY</span>';
                                                        $priceBought = $stockPrice;
                                                    } else if($stockPrice == $stockSuggestions['minPrice']) {
                                                        $txtBuyOrSell = '<span class="text-success">BUY</span>';
                                                        $priceBought = $stockPrice;
                                                    } else if($stockPrice == $stockSuggestions['maxPrice']) {
                                                        $txtBuyOrSell = '<span class="text-danger">SELL</span>';
                                                        $txtProfit = ($stockPrice - $priceBought) * $defaultPurchaseQty;
                                                    }// end: if 
                                                    $stockCount++;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $date; ?></td>
                                                        <td><?php echo 'INR '.number_format((float)$stockPrice,2); ?></td>
                                                        <td><?php echo $txtBuyOrSell; ?></td>
                                                        <td><?php echo $txtProfit ? 'INR '.number_format((float)$txtProfit,2) : '--'; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="pagination">
                                <a href="index.php" ></li>
                                <a href="#" class="active"></li>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
  <script>
      $('.datepicker').datepicker({
        format: 'dd-mm-yyyy'
    });
  </script>
</body>

</html>