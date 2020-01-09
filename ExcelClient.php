<?php
require_once __DIR__ . '/../vendor/autoload.php';
require("reader.php");
class ExcelClient
{
    //Set the url to this class.
    private $file = "sample.xls";
    function setFile($file)
    {
        $this -> file = $file;
    }
    //Read the urls to excel file.
    function readDataExcel($url)
    {
        $connection = new Spreadsheet_Excel_Reader();
        $connection = $connection -> dump($row_numbers = false, $col_letters = false, $sheet = 0, $table_class = 'excel');
        $connection->read($this->file);
        $i = 1;
        if (empty($url)) 
        {
            $url = $connection -> val($i, 2);
        }
        else 
        {
        while($i <= PHP_INT_MAX):
        if($url == $connection->val($i, 2))
            {
            $i++;
        continue;    
            }
        else
        {
            $url = $connection->val($i, 2);
            $i++;
        break;
        }
        endwhile;
        }
        return $url;
    }
    //Print the information to excel file.
    function writeDataExcel($url, $urlController, $videoController, $videoDetail, $videoProperties)
    {
        $wExcel = new Ellumilel\ExcelWriter();
        $wExcel->setAuthor('DataCollection');
        $wExcel->writeSheet($url, 'link');
        $wExcel->writeSheet($urlController, 'website_status');
        $wExcel->writeSheet($videoController, 'video_status');
        $wExcel->writeSheet($videoDetail, 'video_content');
        $wExcel->writeSheet($videoProperties, 'json ({video : { source: source_data , size : size_data, length: length_data , etc.}})');
        $wExcel->writeToFile($this->file);
    }
}
?>