<html>

<head>
    <?php
    include("VideoCollection.php");
    include("ServerConnection.php");
    include("ExcelClient.php");
    ?>
</head>

<body>
    <?php
    $url;
    $excel = new ExcelClient;
    $video = new VideoCollection;
    $server = new ServerConnection;
    $excel -> setFile("../VideoDataCollector/developer-text.xlsx");
    $i = 0;
    while($i <= PHP_INT_MAX):
        $url = $excel->readDataExcel($url);
        $url_code = $video->urlController($url);
        $vid_code = $video->urlVideoController($url);
        $con_code = $video->videoContentDetail($url);
        $vid_com_code = $video->videoDetailUrl($url);
        $vid_json = $video->parseDataJson($url, $url_code, $vid_code, $con_code, $vid_com_code);
        $server->shareDataDB($url, $url_code, $vid_code, $con_code, $vid_json);
        $excel->writeDataExcel($url, $url_code, $vid_code, $con_code, $vid_json);
        $i++;
    endwhile;
    ?>
</body>

</html>