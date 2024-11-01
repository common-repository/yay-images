<?php

require_once(YAY_IMAGES_PATH.'config.php');
require_once(YAY_IMAGES_PATH.'models/YayRender.php');
require_once(YAY_IMAGES_PATH.'models/YayApiCommunication.php');

class YayImage
{

    public function actionHome()
    {

        $content = YayRender::createPage(
            "photolist",
            array('YayimagesPlugin_freeImagesCounter' => $this->_getFreeImagesCounter()),
            true
        );
        echo $content;
    }

    public function actionSaveFeaturedImage($params, $post = array())
    {
        $image = $params['thumbUrl'];
        $attachmentId = $this->_findAttachmentByStreamingUrl($image);

        if (!$attachmentId) {
            $attachmentId = $this->_createFeaturedImageAttachment($params);
        }

//        var_dump($attachmentId);
//        die();
        set_post_thumbnail($params["postId"], $attachmentId);
        echo json_encode(array("success" => true));
    }

    public function actionSavesettings($params, $post = array())
    {
        $formData = $params["formdata"];
        $settings = array();

        foreach ($formData as $row) {
            $name = $row["name"];
            $value = $row["value"];
            $settings[$name] = $value;
        }

        update_option('YayimagesPlugin_settings', $settings);
    }

    public function actionLogin($params, $post = array())
    {
        $logininfo = YayApiCommunication::getInstance()->login($post);
        if (!isset($logininfo->error)) {
            $_SESSION['yay_session_data'] = $logininfo;
        }
        echo json_encode(array("login" => $logininfo));
    }

    public function actionLogout()
    {
        unset($_SESSION['yay_session_data']);
        echo json_encode(true);
    }

    public function actionDownloadlink($params, $post = array())
    {
        echo json_encode(array("linkdata" => YayApiCommunication::getInstance()->getDownloadlink($post)));
    }

    public function actionEditBase($params, $post = [])
    {
        $result = [
            "linkdata"          => YayApiCommunication::getInstance()->getEditBase($post),
            "freeImagesCounter" => $this->_getFreeImagesCounter(),
        ];

        echo json_encode($result);
    }

    public function actionSaveEditedImage($params, $post = [])
    {
        $result = [
            'linkdata'          => YayApiCommunication::getInstance()->saveEditedImage($post),
            "freeImagesCounter" => $this->_getFreeImagesCounter(),
        ];

        echo json_encode($result);
    }

    public function actionImagedetails($params, $post = array())
    {
        $content = YayRender::createPage("photodetails", array("id" => $params['id']));
        echo $content;
    }

    public function actionImagedetailsadditional($params, $post = array())
    {
        $imageDetails = YayApiCommunication::getInstance()->imageDetails($params['id']);

        $similarImages = $this->_getSimilarImages($params);
        $params['photographer'] = $imageDetails->photographer;
        $photographerImages = $this->_getPhotographerImages($params);

        $imageKeywords = YayRender::createPage("photodetailskeywords", array("imagedetails" => $imageDetails));

        echo json_encode(
            array(
                "imagedetails" => $imageDetails,
                "imagekeywords" => $imageKeywords,
                "similarimages" => $similarImages,
                "photographerimages" => $photographerImages
            )
        );
    }

    public function actionImagesearch($params, $post = array())
    {
        $post['sortby'] = (($post['category'] === 'celebrity') || $this->_isDefaultSearch($post)) ? 'date' : 'relevancy';
        $params = YayApiCommunication::getInstance()->imagesSearch($params, $post);

        $imageList = $params["content"];
        $content = YayRender::createPage("photoslist", array("list" => $imageList, "url" => $params["target"]));

        echo json_encode(
            array(
                "content" => $content,
                "similarurl" => $params['similarurl'],
                "count" => count($imageList->images),
                "total" => isset($imageList->total) ? $imageList->total : 0,
                "offset" => $post["offset"]
            )
        );
    }

    public function actionShowsimilarimages($params, $post = array())
    {
        $imageList = YayApiCommunication::getInstance()->similarImages($params['id'], 0, 20);

        $content = YayRender::createPage("photoslist", array("list" => $imageList));

        echo $content;
    }

    public function actionPhotographersearch($params, $post = array())
    {
        $imageList = YayApiCommunication::getInstance()->imagesSearch($params['phrase'], $post);

        $content = YayRender::createPage("photoslist", array("list" => $imageList));

        echo $content;
    }

    public function actionSimilarimages($params, $post = array())
    {
        echo $this->_getSimilarImages($params);
    }

    public function actionPhotographerimages($params, $post = array())
    {
        echo $this->_getPhotographerImages($params);
    }

    public function actionCheckUserToken($params, $post = array())
    {
        echo json_encode(YayApiCommunication::getInstance()->checkUserToken($params['token']));
    }

    //////////////////////////////////////////// PROTECTED METHODS ///////////////////////////////////////////////

    protected function _getSimilarImages($params)
    {
        $currentOffset = isset($params['offset']) ? $params['offset'] : 0;
        $currentLimit = 6;

        $similarImages = YayApiCommunication::getInstance()->similarImages($params['id'], $currentOffset, $currentLimit);
        $total = $similarImages->total;

        $content = YayRender::createPage(
            "partials/similarimages",
            array(
                "similarimages" => $similarImages,
                "offset" => $currentOffset,
                "limit" => $currentLimit,
                "total" => $total
            ),
            false
        );

        return $content;
    }

    protected function _getPhotographerImages($params)
    {
        $currentOffset = isset($params['offset']) ? $params['offset'] : 0;
        $currentLimit = 6;

        $photographerImages = YayApiCommunication::getInstance()->photographerImages(
            rawurlencode($params['photographer']),
            $currentOffset,
            $currentLimit
        );

        $total = $photographerImages->total;

        $content = YayRender::createPage(
            "partials/photographerimages",
            array(
                "photographerimages" => $photographerImages,
                "offset" => $currentOffset,
                "limit" => $currentLimit,
                "total" => $total
            ),
            false
        );

        return $content;
    }

    protected function _isDefaultSearch($params)
    {

        $defaultSearch = array(
            'phrase' => '',
            'category' => '',
            'exclude' => '',
            'people' => '-1',
            'vector' => '0',
            'orientation' => 'all',
            'photographer' => '',
            'explicit' => '1',
            'similarid' => '0',
            'similarweight' => '0',
            'similarurl' => ''
        );

        foreach ($defaultSearch as $key => $value) {
            if ($params[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    protected function _getFreeImagesCounter()
    {
        return YayApiCommunication::getInstance()->getFreeImagesCounter();
    }

    /**
     * @param $params
     * @return int
     */
    protected function _createFeaturedImageAttachment($params)
    {
        $thumbUrl = $params['thumbUrl'];
        $postId = $params["postId"];

        update_post_meta($postId, 'yayimages_featured_url', $thumbUrl);

        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($thumbUrl);

        $filename = basename($params['smallThumbUrl']);

        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file, $postId);

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $file);

        wp_update_attachment_metadata($attach_id, $attach_data);
        add_post_meta($attach_id, 'yay_streaming_url', $thumbUrl);

        return $attach_id;

    }

    protected function _findAttachmentByStreamingUrl($url)
    {
        $queryArgs = array(
            'meta_query' => array(
                array(
                    'key' => 'yay_streaming_url',
                    'value' => $url
                )
            ),
            'post_type' => 'attachment',
            'posts_per_page' => 1
        );
        $result = get_posts($queryArgs);

        if (count($result) == 1) {
            return $result[0]->ID;
        } else {
            return false;
        }
    }

}
