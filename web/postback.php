<?php
/**
 * Created by PhpStorm.
 * User: anon
 * Date: 08/10/2017
 * Time: 22.43
 */

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        $headers = array ();
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

if ($_GET['signature'] != 'ljf9237fgah923') {
    echo 'Wrong s';
    exit();
}


if ($_GET['status'] == 'ok') {
    $url = 'https://buy.easywebbuilder.eu/api/s/v1/subscription/renew/success?merchant_id=35&apikey=OWRjMTNjNDBiNDg5OGEwY2Q4NTkwYzY2YzUwZDQyNmIzMDFkNmI4Mg';
} else {
    $url = 'https://buy.easywebbuilder.eu/api/s/v1/subscription/renew/fail?merchant_id=35&apikey=OWRjMTNjNDBiNDg5OGEwY2Q4NTkwYzY2YzUwZDQyNmIzMDFkNmI4Mg';
}

$ch = curl_init($url);
$data_str = file_get_contents('php://input');

$headers = getallheaders();
$headers_str = [];

foreach ( $headers as $key => $value){
    if($key == 'Host')
        continue;
    $headers_str[]=$key.":".$value;
}

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers_str );
$result = curl_exec($ch);

//$file = 'resp.txt';
// Open the file to get existing content
//$current = file_get_contents($file);
// Append a new person to the file
//$current .= print_r($result, true) . "\r\n\r\n";

// Write the contents back to the file
//file_put_contents($file, $current, FILE_APPEND);

header("HTTP/1.1 200 OK");
