<?php
    class VideoCollection
    {
        //Required files and libraries
        private $ffmpeg_path = '../Ahmet/bin/ffmpeg';
        public $url = "https://www.broadwayworld.com/bwwtv/article/VIDEO-Watch-A-Clip-From-New-Aziz-Ansari-Stand-Up-Special-Streaming-On-Netflix-Tomorrow-20190708";
        //Checks the status of the page according to the http request.
        function urlController($url)
        {
        $timeout = 8;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $http_respond = curl_exec($ch);
        $http_respond = trim(strip_tags($http_respond));
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($http_code == "200") || ($http_code == "302")) {
            return '$http_code';
            print_r('Sayfada problem yok.');
        } else {
            return '$http_code';
            print_r('Sayfada problem var.');
        }
        curl_close($ch);
        }
        //Checks the presence of video content.
        function urlVideoController($vid)
        {
        if (file_exists($vid)) {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $vid); 
            finfo_close($finfo);
            if (preg_match('/video\/*/', $mime_type)) {

                $video_attributes = _get_video_attributes($vid, $this -> ffmpeg_path);

                print_r('Codec: ' . $video_attributes['codec'] . '<br/>');

                print_r('Dimension: ' . $video_attributes['width'] . ' x ' . $video_attributes['height'] . ' <br/>');

                print_r('Duration: ' . $video_attributes['hours'] . ':' . $video_attributes['mins'] . ':'
                    . $video_attributes['secs'] . '.' . $video_attributes['ms'] . '<br/>');

                print_r('Size:  ' . _human_filesize(filesize($vid)));
            } else {
                return 'Video uzantısıdır.';
            }
        } else {
            return 'Video uzantısı değildir.';
        }
        }
        //Checks that the video extension is from a different website.
        function videoContentDetail($url)
        {
        $host = explode('.', str_replace('www.', '', strtolower(parse_url($url, PHP_URL_HOST))));
        $host = isset($host[0]) ? $host[0] : $host;
        switch ($host) {
            case 'vimeo':
                $video_id = substr(parse_url($url, PHP_URL_PATH), 1);
                $hash = json_decode(file_get_contents("http://vimeo.com/api/v2/video/{$video_id}.json"));
                return array(
                    'provider'          => 'Vimeo',
                    'title'             => $hash[0]->title,
                    'description'       => str_replace(array("<br>", "<br/>", "<br />"), NULL, $hash[0]->description),
                    'description_nl2br' => str_replace(array("\n", "\r", "\r\n", "\n\r"), NULL, $hash[0]->description),
                    'thumbnail'         => $hash[0]->thumbnail_large,
                    'video'             => "https://vimeo.com/" . $hash[0]->id,
                    'embed_video'       => "https://player.vimeo.com/video/" . $hash[0]->id,
                );
                break;
            case 'youtube':
                preg_match("/v=([^&#]*)/", parse_url($url, PHP_URL_QUERY), $video_id);
                $video_id = $video_id[1];
                $hash = json_decode(file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$video_id}?v=2&alt=jsonc"));
                return array(
                    'provider'          => 'YouTube',
                    'title'             => $hash->data->title,
                    'description'       => str_replace(array("<br>", "<br/>", "<br />"), NULL, $hash->data->description),
                    'description_nl2br' => str_replace(array("\n", "\r", "\r\n", "\n\r"), NULL, nl2br($hash->data->description)),
                    'thumbnail'         => $hash->data->thumbnail->hqDefault,
                    'video'             => "http://www.youtube.com/watch?v=" . $hash->data->id,
                    'embed_video'       => "http://www.youtube.com/v/" . $hash->data->id,
                );
                break;
        }
        }
        //Examines the properties of the video.
        function videoDetailUrl($url)
        {
        $curlInit = curl_init($url);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlInit);
        curl_close($curlInit);
        if ($response) return true; return false;
        }
        function videoDetail($video, $ffmpeg)
        {
        $command = $ffmpeg . ' -i ' . $video . ' -vstats 2>&1';
        $output = shell_exec($command);
        $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/"; 
        if (preg_match($regex_sizes, $output, $regs)) {
            $codec = $regs[1] ? $regs[1] : null;
            $width = $regs[3] ? $regs[3] : null;
            $height = $regs[4] ? $regs[4] : null;
        }
        $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
        if (preg_match($regex_duration, $output, $regs)) {
            $hours = $regs[1] ? $regs[1] : null;
            $mins = $regs[2] ? $regs[2] : null;
            $secs = $regs[3] ? $regs[3] : null;
        }
        return array(
            'codec' => $codec,
            'width' => $width,
            'height' => $height,
            'hours' => $hours,
            'mins' => $mins,
            'secs' => $secs,
        );
        }
        //Converts the information to json making it available.
        function parseDataJson($url, $urlController, $videoController, $videoDetail, $videoProperties)
        {
        $values =array('url' => ['$urlController','$videoController','$videoDetail','$videoProperties']);
        return json_encode($values);
        }
    }
?>


