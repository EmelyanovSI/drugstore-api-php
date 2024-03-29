<?php

header('Content-Type: text/html; charset=utf-8');

set_time_limit(0);

require_once 'res/strings.php';

$response = array();

/**
 * @param mysqli_result $result3
 * @param array $drug
 * @param array $response
 * @return array
 */
function getArr(mysqli_result $result3, array $drug, array $response)
{
    while ($row = mysqli_fetch_array($result3)) {
        $drug = array();
        $drug['id'] = intval($row['id']);
        $drug['name'] = $row['name'];
        $drug['composition'] = $row['composition'];
        $drug['country'] = 'США';
        $response['drugs'][] = $drug;
    }
    return $response;
}

if (isset($_GET['name'])) {

    $name = $_GET['name'];

    require_once 'config/db_connect.php';

    $db = new DB_CONNECT();
    $con = $db->connect();

    $response['drugs'] = array();
    $composition = array();

    $result1 = mysqli_query($con, "SELECT * FROM drugsbel WHERE name LIKE '%$name%'")
    or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($result1)) {
        foreach ($composition as $value) {
            if ($value == $row['composition']) {
                break;
            }
        }
        $composition[] = $row['composition'];
    }

    $result2 = mysqli_query($con, "SELECT * FROM drugsturkey WHERE name LIKE '%$name%'")
    or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($result2)) {
        foreach ($composition as $value) {
            if ($value == $row['composition']) {
                break;
            }
        }
        $composition[] = $row['composition'];
    }

    $result3 = mysqli_query($con, "SELECT * FROM drugsusa WHERE name LIKE '%$name%'")
    or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($result3)) {
        foreach ($composition as $value) {
            if ($value == $row['composition']) {
                break;
            }
        }
        $composition[] = $row['composition'];
    }

    if (!empty($composition)) {

        $response['success'] = 1;
        $response['message'] = $messageFined;

        foreach ($composition as $value) {
            $result1 = mysqli_query($con, "SELECT * FROM drugsbel WHERE composition LIKE '%$value%'")
            or die(mysqli_error($con));
            while ($row = mysqli_fetch_array($result1)) {
                $drug = array();
                $drug['id'] = intval($row['id']);
                $drug['name'] = $row['name'];
                $drug['composition'] = $row['composition'];
                $drug['country'] = 'Беларусь';
                $response['drugs'][] = $drug;
            }
        }

        foreach ($composition as $value) {
            $result2 = mysqli_query($con, "SELECT * FROM drugsturkey WHERE composition LIKE '%$value%'")
            or die(mysqli_error($con));
            while ($row = mysqli_fetch_array($result2)) {
                $drug = array();
                $drug['id'] = intval($row['id']);
                $drug['name'] = $row['name'];
                $drug['composition'] = $row['composition'];
                $drug['country'] = 'Турция';
                $response['drugs'][] = $drug;
            }
        }

        foreach ($composition as $value) {
            $result3 = mysqli_query($con, "SELECT * FROM drugsusa WHERE composition LIKE '%$value%'")
            or die(mysqli_error($con));
            $response = getArr($result3, $drug, $response);
        }
    } elseif (empty($composition)) {
        $response['success'] = 1;
        $response['message'] = $messageFinedNull;
    } else {
        $response['success'] = 0;
        $response['message'] = $error;
    }
} else {
    $response['success'] = 0;
    $response['message'] = $messageNotOk;
}
echo json_encode($response, JSON_UNESCAPED_UNICODE);
