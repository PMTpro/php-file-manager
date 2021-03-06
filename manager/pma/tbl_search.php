<?php

// wap phpmyadmin
// ionutvmi@gmail.com
// master-land.net

include "lib/settings.php";

connect_db($db);
$check = $db->query("SHOW DATABASES LIKE '" . $db->real_escape_string($_GET['db']) . "'");
$check = $check->num_rows;
$db_name = trim($_GET['db']);

// if no db exit

if ($db_name == '' OR $check == 0) {
    header("Location: main.php");
    exit;
}

// select db

$db->select_db($db_name);
$check = $db->query("SHOW TABLE STATUS LIKE '" . $db->real_escape_string($_GET['tb']) . "'");
$check = $check->num_rows;
$tb_name = trim($_GET['tb']);

// if no tb exit

if ($tb_name == '' OR $check == 0) {
    header("Location: main.php");
    exit;
}

// define url query

$_url = "db=" . urlencode($db_name) . "&tb=" . urlencode($tb_name);
$cl = $db->query("SHOW FULL COLUMNS FROM " . PMA_bkq($tb_name));

while ($c = $cl->fetch_array()) {
    $_cols[] = $c['Field'];
}

if ($_POST['_v']) {
    $vi = 0;
    $sq = $_POST['_v'];
    $func = $_POST['func'];
    foreach($func as $fu) {
        $sq[$vi] = $db->real_escape_string($sq[$vi]);
        if (trim($sq[$vi]) != '' || $fu == '13' || $fu == '14' || $fu == '19' || $fu == '20') {
            if ($fu == '1') {
                $cond[] = PMA_bkq($_cols[$vi]) . " > " . $sq[$vi];
            }

            if ($fu == '2') {
                $cond[] = PMA_bkq($_cols[$vi]) . " >= " . $sq[$vi];
            }

            if ($fu == '3') {
                $cond[] = PMA_bkq($_cols[$vi]) . " < " . $sq[$vi];
            }

            if ($fu == '4') {
                $cond[] = PMA_bkq($_cols[$vi]) . " <= " . $sq[$vi];
            }

            if ($fu == '5') {
                $cond[] = PMA_bkq($_cols[$vi]) . " LIKE '" . $sq[$vi] . "'";
            }

            if ($fu == '6') {
                $cond[] = PMA_bkq($_cols[$vi]) . " LIKE '%" . $sq[$vi] . "%'";
            }

            if ($fu == '7') {
                $cond[] = PMA_bkq($_cols[$vi]) . " NOT LIKE '" . $sq[$vi] . "'";
            }

            if ($fu == '8') {
                $cond[] = PMA_bkq($_cols[$vi]) . " = '" . $sq[$vi] . "'";
            }

            if ($fu == '9') {
                $cond[] = PMA_bkq($_cols[$vi]) . " != '" . $sq[$vi] . "'";
            }

            if ($fu == '10') {
                $cond[] = PMA_bkq($_cols[$vi]) . " REGEXP '" . $sq[$vi] . "'";
            }

            if ($fu == '11') {
                $cond[] = PMA_bkq($_cols[$vi]) . " REGEXP '^" . $sq[$vi] . "$'";
            }

            if ($fu == '12') {
                $cond[] = PMA_bkq($_cols[$vi]) . " NOT REGEXP '" . $sq[$vi] . "'";
            }

            if ($fu == '13') {
                $cond[] = PMA_bkq($_cols[$vi]) . " =''";
            }

            if ($fu == '14') {
                $cond[] = PMA_bkq($_cols[$vi]) . " !=''";
            }

            if ($fu == '15') {
                $cond[] = PMA_bkq($_cols[$vi]) . " IN (" . $sq[$vi] . ")";
            }

            if ($fu == '16') {
                $cond[] = PMA_bkq($_cols[$vi]) . " NOT IN (" . $sq[$vi] . ")";
            }

            if ($fu == '17') {
                $cond[] = PMA_bkq($_cols[$vi]) . " BETWEEN " . str_replace(',', ' AND ', $sq[$vi]);
            }

            if ($fu == '18') {
                $cond[] = PMA_bkq($_cols[$vi]) . " NOT BETWEEN " . str_replace(',', ' AND ', $sq[$vi]);
            }

            if ($fu == '19') {
                $cond[] = PMA_bkq($_cols[$vi]) . " IS NULL ";
            }

            if ($fu == '20') {
                $cond[] = PMA_bkq($_cols[$vi]) . " IS NOT NULL ";
            }
        }

        ++$vi;
    }

    $query = @implode(" AND ", $cond);
    if (trim($query != '')) {
        $_SESSION['search'] = base64_encode($query);
        header("Location: tbl_browse.php?search2=1&$_url");
        exit;
    }
}

$pma->title = $lang->Search;
include $pma->tpl . "header.tpl";

include $pma->tpl . "tbl_search.tpl";

include $pma->tpl . "footer.tpl";
