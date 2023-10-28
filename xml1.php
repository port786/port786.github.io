<?php
try{
    if(isset($_POST["xml_loc"]) && isset($_POST["api_key"])) {
        $xml_loc="";
        $api_key="";
$main_url="";
        if(isset($_POST['xml_loc'])){
            $xml_loc=$_POST['xml_loc'];
        }
        if(isset($_POST['api_key'])){
            $api_key=$_POST['api_key'];
        }
        if(isset($_POST['main_url'])){
            $main_url=$_POST['main_url'];
        }
    }

    $url = $xml_loc;
    $xml = simplexml_load_file($url);
    $main = $main_url;
    // print_r($xml);
    $date = date('D');
    $time = gmdate("d M Y H:i:s");

    $duration = $_POST["duration"];
    $responseObj = [];
    $count = 0;
    foreach($xml as $value){
        echo $key;
        $htmlview = file_get_contents($value->loc);
    
        $data = "HTTP/1.1 200 OK\r\nDate: " . $date . ", " . $time .
            " GMT\r\nAccept-Ranges: bytes\r\nConnection: close\r\nContent-Type: text/html\n\n";
            
        $data = $data . $htmlview;
        
        $encodedData = base64_encode($data);
    
        $vars = array (
            "siteUrl" => $main,
            "url" => (string)$value->loc,
            "httpMessage" => $encodedData,
            "structuredData" => "",
            "dynamicServing" => "0"
        );
        $payload = json_encode($vars);
    
        $apiUrl = "https://ssl.bing.com/webmaster/api.svc/json/SubmitContent?apikey=".$api_key;
    
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL,$apiUrl);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result_data = curl_exec($curl);
        curl_close($curl);
        $responseObj[$count]["url"] = (string)$value->loc;
        $responseObj[$count]["response"] = $result_data;
        $count++;
        sleep($duration);
    }
    sleep(10);
    header('location: response.php?response='.json_encode($responseObj));
    die();
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>