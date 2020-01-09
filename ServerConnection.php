<?php
class ServerConnection
{
    private $tableCode = "myTable (urlData, urlController, urlVideo, urldetail, urlProperties)";
    //There are connection codes required for database connection.
    private function pdoSettings()
    {
        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // Turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //Turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Make the default fetch be an associative array
        ];
        $dsn = "mysql:host=localhost/8080;dbname=testDatabase;charset=utf8mb4";
        $pdo = new PDO($dsn, "username", "password", $options);
        try {
            return $pdo;
        } catch (Exception $e) {
            return error_log($e->getMessage());
            exit('Veri tabanı bağlantınızı kontrol ediniz.');
        }
    }
    //Stores information in the database.
    function shareDataDB($url, $urlController, $videoController, $videoDetail, $videoProperties)
    {
        $pdo1 = $this->pdoSettings();
        $stmt = $pdo1->prepare("INSERT INTO " . $this->tableCode
            . " VALUES ('$url', '$urlController', '$videoController', '$videoDetail', '$videoProperties')");
        echo $_POST[$stmt];
    }
}
?>