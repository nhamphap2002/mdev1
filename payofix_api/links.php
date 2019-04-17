<?php
/*
 * Created on : Apr 3, 2019, 4:18:00 PM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
include_once 'config.php';
$table = TB_ORDERLINKS;
$mess = '';
if (!empty($_SESSION["login"])) {
    
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
        <title>Link</title>
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
                    <div class="col-md-12">
                        <?php
                        if (isset($_REQUEST["p"]))
                            $page = (int) $_REQUEST["p"];
                        else
                            $page = 1;
                        $setLimit = Limit;
                        $pageLimit = ($page * $setLimit) - $setLimit;

                        $query = array();
                        $querystr = array();
                        $query_string = $querystring = "";
                        if (isset($_REQUEST["p"]))
                            $query["p"] = $_REQUEST["p"];
                        if (isset($_REQUEST["orderid"])) {
                            $query["orderid"] = $_REQUEST["orderid"];
                            $querystr["orderid"] = $_REQUEST["orderid"];
                        }
                        if (isset($_REQUEST["website"])) {
                            $query["website"] = $_REQUEST["website"];
                            $querystr["website"] = $_REQUEST["website"];
                        }

                        if (!empty($query)) {
                            $query_string = "?" . http_build_query($query);
                        }
                        if (!empty($querystr)) {
                            $querystring = "&" . http_build_query($querystr);
                        }
                        $where = '';
                        if (!empty($_REQUEST["website"]) && !empty($_REQUEST["orderid"])) {
                            $where = ' WHERE website ="' . $_REQUEST["website"] . '" AND orderid LIKE "%' . $_REQUEST["orderid"] . '%"';
                        } else if (!empty($_REQUEST["website"]) && empty($_REQUEST["orderid"])) {
                            $where = ' WHERE website ="' . $_REQUEST["website"] . '"';
                        } else if (empty($_REQUEST["website"]) && !empty($_REQUEST["orderid"])) {
                            $where = ' WHERE orderid LIKE "%' . $_REQUEST["orderid"] . '%"';
                        }
                        //print_r($query_string);
                        $fields = "*";
                        $sql = "SELECT " . $fields . " FROM " . $table . $where . ' LIMIT ' . $pageLimit . " , " . $setLimit;
                        $db->query($sql);
                        $items = $db->loadObjectList();
                        ?>
                        <form>
                            <div class="row">
                                <div class="col-md-4">                                   
                                    <div class="form-group">
                                        <!--<label>Website</label>-->
                                        <select class="form-control" id="website" name="website">
                                            <option value="">Please select website</option>
                                            <option value="MP" <?php echo(!empty($_REQUEST["website"]) && $_REQUEST["website"] == 'MP') ? 'selected=""' : ''; ?>>Med4Pacific.com</option>
                                            <option value="DM" <?php echo(!empty($_REQUEST["website"]) && $_REQUEST["website"] == 'DM') ? 'selected=""' : ''; ?>>Dot4Med.com</option>
                                            <option value="MC" <?php echo(!empty($_REQUEST["website"]) && $_REQUEST["website"] == 'MC') ? 'selected=""' : ''; ?>>Med-Cab.net</option>
                                            <option value="FG" <?php echo(!empty($_REQUEST["website"]) && $_REQUEST["website"] == 'FG') ? 'selected=""' : ''; ?>>FullGrownHair.com</option>
                                            <option value="AM" <?php echo(!empty($_REQUEST["website"]) && $_REQUEST["website"] == 'AM') ? 'selected=""' : ''; ?>>AllPetsMed.com</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="orderid" class="form-control" id="orderid" placeholder="Order Id" value="<?php echo!empty($_REQUEST["orderid"]) ? $_REQUEST["orderid"] : ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <!--<input type="password" class="form-control" id="inputPassword" placeholder="Password">-->
                                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                                    <?php
                                    if (isset($_REQUEST["p"])) {
                                        ?>
                                        <input type="hidden" name="p" id="p" value="<?php echo $_REQUEST["p"]; ?>"> 
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">OrderId</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Website</th>
                                    <th scope="col">Created</th>
                                    <th scope="col">Link</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                if ($items) {
                                    foreach ($items as $key => $value) {
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <?php
                                                echo $i;
                                                $i++;
                                                ?>
                                            </th>
                                            <td>
                                                <?php
                                                echo $value->orderid;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $value->price;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $value->website;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $value->created;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $value->link;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($value->status == 1) {
                                                    echo 'Paid';
                                                }else if ($value->status == 2) {
                                                    echo 'Failed';
                                                }else if ($value->status == 3) {
                                                    echo 'Pending';
                                                }else if ($value->status == 4) {
                                                    echo 'Pending';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                            </tbody>
                        </table>
                        <?php
                        $fields = "count(id) as count";
                        $sql = "SELECT " . $fields . " FROM " . $table . $where;
                        $db->query($sql);
                        $total = $db->loadResult();
                        $setLastpage = ceil($total / $setLimit);
                        ?>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <?php if ($setLastpage > 1) { ?>
                                    <!--<li class="page-item"><a class="page-link" href="#">Previous</a></li>-->
                                    <?php
                                    for ($i = 1; $i < ($setLastpage + 1); $i++) {
                                        ?>
                                        <li class="page-item"><a class="page-link" href="?p=<?php echo $i . $querystring ?>"><?php echo $i ?></a></li>    
                                            <?php
                                        }
                                        ?>
                                    <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>

    </body>
</html>
