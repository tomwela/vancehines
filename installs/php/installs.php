<?php


require_once('dbConnect.php');

if (isset($_GET['type'])) {

    $type = trim($_GET['type']);
    $d1 = trim($_GET['d1']);
    $d2 = trim($_GET['d2']);

    $mode = (isset($_GET['mode']) && !empty($_GET['mode'])) ? trim(rawurldecode($_GET['mode'])) : 'all';
    switch ($mode) {
        case "all":
            $w = null;
            break;
        case "66007":
            $w = " AND CAST(REPLACE(hVersion, '\.', '') AS DECIMAL) >= ? ";
            break;
        case "66005":
            $w = " AND CAST(REPLACE(hVersion, '\.', '') AS DECIMAL) < ? ";
            break;
        default:
            $w = null;
    }

    switch ($type) {
        case 'byWeek':
            $SQL = " SELECT
                          DATE_FORMAT(FROM_DAYS(TO_DAYS(a.created) - MOD(TO_DAYS(a.created)-2, 7)),'%b %e, %Y') AS letter,
                          count(*) frequency
                      FROM (
                          SELECT
                          created
                          FROM Customers
                          WHERE ecmPart > ? ";

            $SQLEnd = " AND created BETWEEN STR_TO_DATE(?,'%m/%d/%Y') AND STR_TO_DATE(?,'%m/%d/%Y')
                      ) a
                      GROUP BY FROM_DAYS(TO_DAYS(a.created) -MOD(TO_DAYS(a.created)-2, 7))
                      ORDER BY FROM_DAYS(TO_DAYS(a.created) -MOD(TO_DAYS(a.created)-2, 7)) ";

            $query = "{$SQL}{$w}{$SQLEnd}";
            //var_dump($query);


            report($connection, $query, $d1, $d2, $mode, $w);
            break;

        case 'byMonth':
            $SQL = "SELECT
                        DATE_FORMAT(DATE(DATE_FORMAT(created, '%Y-%m-01')),'%b %Y') AS letter,
                        COUNT(*) AS frequency
                    FROM (
                        SELECT created
                        FROM Customers
                        WHERE ecmPart > ? ";

            $SQLEnd = " AND created BETWEEN STR_TO_DATE(?,'%m/%d/%Y') AND STR_TO_DATE(?,'%m/%d/%Y')
                    ) a
                    GROUP BY DATE(DATE_FORMAT(created, '%Y-%m-01'))
                    ORDER BY DATE(DATE_FORMAT(created, '%Y-%m-01')) ";

            $query = "{$SQL}{$w}{$SQLEnd}";

            report($connection, $query, $d1, $d2, $mode, $w);
            break;

        case 'byDay':
            $SQL = " SELECT
                          DATE_FORMAT(DATE(created),'%a %c-%e-%y') AS letter,
                          COUNT(*) AS frequency
                      FROM (
                          SELECT
                          created
                          FROM Customers
                          WHERE ecmPart > ? ";

            $SQLEnd = " AND created BETWEEN STR_TO_DATE(?,'%m/%d/%Y') AND STR_TO_DATE(?,'%m/%d/%Y')
                      ) a
                      GROUP BY DATE(created)
                      ORDER BY DATE(created) ";

            $query = "{$SQL}{$w}{$SQLEnd}";


            report($connection, $query, $d1, $d2, $mode, $w);
            break;

        default:
            echo json_encode("No Data Available");
            break;
    }

} else {
    echo json_encode("No Data Available");
}

mysqli_close($connection);

/*******************************************************************/

function report($connection, $query, $d1, $d2, $mode, $w)
{

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($connection));
    }

    if( $mode == "all"){

        mysqli_stmt_bind_param($stmt, 'iss', $b1, $b2, $b3);
        $b1 = 0.0;
        $b2 = $d1;
        $b3 = $d2;

    } elseif( $mode == '66007' || $mode == '66005' ){

        mysqli_stmt_bind_param($stmt, 'iiss', $b1, $b4, $b2, $b3);
        $b1 = 0.0;
        $b4 = 500;
        $b2 = $d1;
        $b3 = $d2;
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $letter, $frequency);


    $info = array();
    $info['total'] = '';
    $info['results'] = array();

    while (mysqli_stmt_fetch($stmt)) {

        $data = array(
            "letter"    => $letter,
            "frequency" => $frequency,
        );

        array_push($info['results'], $data);
    }
    mysqli_stmt_close($stmt);

    $info['total'] = getNumberOfInstalls($connection, $mode, $w);

    echo json_encode($info);

    return;
}

function getNumberOfInstalls($connection, $mode, $w)
{

    $SQL = " SELECT COUNT(*) Total
                 FROM (
                        SELECT customerID
                        FROM Customers
                        WHERE ecmPart > ? ";

    $SQLEnd = " ) a ";
    $query = "{$SQL}{$w}{$SQLEnd}";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($connection));
    }

    if ($mode == "all"){
        mysqli_stmt_bind_param($stmt, 'i', $b1);
        $b1 = 0.0;
    } elseif( $mode == '66007' || $mode == '66005' ){
        mysqli_stmt_bind_param($stmt, 'ii', $b1, $b2);
        $b1 = 0.0;
        $b2 = 500;
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $Total);

    while (mysqli_stmt_fetch($stmt)) {
        $totalNumberOfInstalls = $Total;
    }

    return $totalNumberOfInstalls;
}

function logger($fname, $fdata)
{

    //$logPath = '../Temporary/_log';
    $logPath = null;
    $logFile = $logPath . "/" . $fname;

    $lsm = fopen($logFile, "a");
    fwrite($lsm, date("m/d/Y h:i:s A") . '');
    fwrite($lsm, "\n" . json_encode($fdata) . " \n");
    fclose($lsm);
}

/*
  function level100( $connection ) {
      $query = "	SELECT
                    FROM_DAYS(TO_DAYS(a.created) - MOD(TO_DAYS(a.created)-2, 7)) AS letter,
                    count(*) frequency
                    FROM (
                                SELECT
                                    created
                                FROM Customers
                                WHERE ecmPart > 0.0
                                GROUP BY customerID
                    ) a
                    GROUP BY FROM_DAYS(TO_DAYS(a.created) -MOD(TO_DAYS(a.created)-2, 7))
                     ORDER BY FROM_DAYS(TO_DAYS(a.created) -MOD(TO_DAYS(a.created)-2, 7)) ";

      $result = mysqli_query($connection, $query);
      if (!$result) {
          die("Database query failed.");
      }

      $data = array();

      for ($x=0; $x < mysqli_num_rows($result); $x++) {
        $data[] = mysqli_fetch_assoc($result);
      }

      mysqli_free_result($result);
      mysqli_close($connection);

      echo json_encode($data);
      return;

  }
*/

?>
