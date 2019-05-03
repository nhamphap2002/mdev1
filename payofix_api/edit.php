<?php
/*
 * Created on : Apr 3, 2019, 4:18:00 PM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
include_once 'config.php';
$table = TB_ORDERLINKS;
$mess = $link = '';
if (!empty($_SESSION["login"])) {
    if (!empty($_REQUEST['isupdatelink'])) {
        $cols["orderid"] = $_REQUEST['orderid'];
        $cols["price"] = $_REQUEST['price'];
        $cols["website"] = $_REQUEST['website'];
        
        $insertID = $_REQUEST['id'];
        $where = "id = '" . $insertID . "' ";
        $result = $db->updateSql($cols, $table, $where);
        if ($result) {
            $mess = '<div class="alert alert-success" role="alert">You updated link success</div>';
        } else {
            $mess = '<div class="alert alert-success" role="alert">You updated link error</div>';
        }//end result
        $fields = "*";
        $where = "WHERE id = '" . $insertID . "'";
        $sql = "SELECT " . $fields . " FROM " . $table . " " . $where;
        $db->query($sql);
        $createlink = $db->loadObject();
    } elseif (!empty($_REQUEST['id'])) {
        $fields = "*";
        $where = "WHERE id = '" . $_REQUEST['id'] . "'";
        $sql = "SELECT " . $fields . " FROM " . $table . " " . $where;
        $db->query($sql);
        $createlink = $db->loadObject();
    }
} else {
    header('Location: login.php');
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Jekyll v3.8.5">
        <title>Create Link</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js" type="text/javascript"></script>
        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size: 3.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarsExample09">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="createlink.php">Createlink</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="links.php">Link</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>



            <main role="main">
                <div class="form-group"></div>
                <div>
                    <?php echo $mess; ?>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <form class="form-signin" method="post">
                            <div class="form-group">
                                <label>Order ID</label>
                                <input name="orderid" type="text" value="<?php echo $createlink->orderid; ?>" class="form-control" placeholder="Order id" required="">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input name="price" type="text" value="<?php echo $createlink->price; ?>" class="form-control" placeholder="Price" required="">
                            </div>
                            <div class="form-group">
                                <label>Website</label>
                                <select class="form-control" id="website" name="website" required="">
                                    <option value="">Please select website</option>
                                    <option value="MP" <?php echo($createlink->website == 'MP') ? 'selected=""' : ''; ?>>Med4Pacific.com</option>
                                    <option value="DM" <?php echo($createlink->website == 'DM') ? 'selected=""' : ''; ?>>Dot4Med.com</option>
                                    <option value="MC" <?php echo($createlink->website == 'MC') ? 'selected=""' : ''; ?>>Med-Cab.net</option>
                                    <option value="FG" <?php echo($createlink->website == 'FG') ? 'selected=""' : ''; ?>>FullGrownHair.com</option>
                                    <option value="AM" <?php echo($createlink->website == 'AM') ? 'selected=""' : ''; ?>>AllPetsMed.com</option>
                                </select>
                            </div>
                            <input type="hidden" name="isupdatelink" value="1"/>
                            <button class="btn btn-lg btn-primary btn-block" type="submit">Update</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <?php
                        echo $link;
                        ?>

                    </div>
                </div>
            </main>
        </div>

    </body>
</html>
