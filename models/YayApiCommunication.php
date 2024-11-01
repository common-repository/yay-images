<?php

class YayApiCommunication
{

    private $_apiToken = '';
    private static $_instance;

    /**
     * @return YayApiCommunication
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    function __construct()
    {
        $this->_apiToken = $this->_getApiToken();
    }

    public function callPostAPI($target, $postfields, $userToken = false)
    {
        $headers = $this->_getHeaders($this->_apiToken, $userToken);

        $process = curl_init(YAY_API_HOST . "/" . $target);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return);
    }

    public function callGetAPI($target, $userToken = false)
    {
        $headers = $this->_getHeaders($this->_getApiToken(), $userToken);

        $process = curl_init(YAY_API_HOST . "/" . $target);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);

        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return);
    }

    public function login($post)
    {
        $curl_post_data = array(
            "email" => $post['username'],
            "password" => $post['userpass'],
        );
        return ($this->callPostAPI("users/authenticate", $curl_post_data));
    }

    public function getDownloadlink($post)
    {
        $target = "images/{$post["id"]}/streamingLink";

        $image = $this->callGetAPI($target, $post["token"]);
        $image->freeImagesCounter = $this->getFreeImagesCounter();

        return $image;
    }

    public function getEditBase($post)
    {
        $target = "images/{$post["id"]}/editBaseImage/streaming";

        $image = $this->callPostAPI($target, array(), $post['token']);
        $image->freeImagesCounter = $this->getFreeImagesCounter();

        return $image;
    }

    public function saveEditedImage($post)
    {
        $target = "images/edited/%d";
        $url = sprintf($target, $post['id']);

        $result = $this->callPostAPI($url, $post, $post['token']);

        return $result;
    }

    public function imagesSearch($params, $post, $offset = 0, $limit = 60)
    {

        $g = array();
        if ($post["phrase"] && !$post["exclude"])
            $g[] = "phrase=" . urlencode($post["phrase"]);
        if (!$post["phrase"] && $post["exclude"])
            $g[] = "phrase=" . urlencode("NOT {$post["exclude"]}");
        if ($post["phrase"] && $post["exclude"])
            $g[] = "phrase=" . urlencode("{$post["phrase"]} NOT {$post["exclude"]}");
        if ($post["category"])
            $g[] = "categories={$post["category"]}";
        if ($post["vector"])
            $g[] = "vector={$post["vector"]}";
        if ($post["orientation"] && $post["orientation"] != "all") {
            $g[] = "orientation={$post["orientation"]}";
        }
        if ($post["photographer"])
            $g[] = "photographer=" . urlencode($post["photographer"]);
        if ($post["explicit"])
            $g[] = "explicit={$post["explicit"]}";
        if ($post["sortby"])
            $g[] = "order={$post["sortby"]}";

        $similarurl = "";
        if ($post["similarid"]) {
            $similarurl = isset($post["similarurl"]) ? $post["similarurl"] : "";
            $g[] = "similarToId={$post["similarid"]}";
            if ($post["similarweight"]) {
                $g[] = "similarImageWeight={$post["similarweight"]}";
            } else {
                $g[] = "similarImageWeight=90";
            }
        }

        if ($post["people"]) {
            switch ($post["people"]) {
                case "-1":

                    break;
                case "n":
                    $g[] = "minPeople=0&maxPeople=0";
                    break;
                case "p":
                    $g[] = "minPeople=1";
                    break;
                case "1p":
                    $g[] = "minPeople=1&maxPeople=1";
                    break;
                case "2p":
                    $g[] = "minPeople=2&maxPeople=2";
                    break;
                case "g":
                    $g[] = "minPeople=3";
                    break;
            }
        }

        if (isset($post["colors"])) {
            foreach ($post["colors"] as $color => $val) {
                $g[] = urlencode("color{$color}") . "=$val";
            }
        }

        $gstring = implode("&", $g);

        $offset = isset($post["offset"]) && $post["offset"] ? $post["offset"] : $offset;

        $target = "images/search?{$gstring}&offset={$offset}&limit={$limit}";

        $content = $this->callGetAPI($target);

        return array("content" => $content, "target" => $target, "similarurl" => $similarurl);
    }

    public function imageDetails($id)
    {
        $target = "images/{$id}";
        return $this->callGetAPI($target);
    }

    public function similarImages($id, $offset = 0, $limit = 60)
    {
        ///todo
        $target = "images/search?similarToId={$id}&offset={$offset}&limit={$limit}";

        return $this->callGetAPI($target);
    }

    public function photographerImages($photographer, $offset = 0, $limit = 6)
    {
        ///todo
        $target = "images/search?photographer={$photographer}&offset={$offset}&limit={$limit}";
        return $this->callGetAPI($target);
    }

    public function getAppToken($url)
    {
        $target = "accessKeys";
        return $this->callPostAPI($target, array('appUrl' => $url));
    }

    public function checkUserToken($token)
    {
        $target = "accessKeys/check";
        return $this->callGetAPI($target, $token);
    }

    private function _getApiToken()
    {
        $optionName = 'YayimagesPlugin_apiAppToken_' . md5(YAY_API_HOST);
        $token = get_option($optionName, false);
        if (!$token) {
            $token = $this->getAppToken(get_option('siteurl'));
            update_option($optionName, $token);
        }
        return $token;
    }

    public function getFreeImagesCounter()
    {

        $optionName = 'YayimagesPlugin_freeImagesCounter';
        $currentCounter = get_option($optionName, false);
        $newCounter = 0;

        if (($currentCounter === false) || ($currentCounter > 0)) {

            $counter = $this->_getFreeImagesCounter();
            $newCounter = ($counter->limit - $counter->used);
            update_option($optionName, $newCounter);
        }

        return $newCounter;
    }

    private function _getFreeImagesCounter()
    {
        $target = "images/freeCount?appToken={$this->_apiToken}";
        return $this->callGetAPI($target);
    }

    private function _getHeaders($appToken, $userToken)
    {
        $headers = array(
            'X-YayImagesApi-AppToken: ' . $appToken,
        );
        if ($userToken) {
            array_push($headers, 'X-YayImagesApi-UserToken: ' . $userToken);
        }
        return $headers;
    }

}
