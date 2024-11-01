<?php

class YayRender {
    
    public function getLocation() {
        return YAY_IMAGES_PATH.'views/';
    }
    
    public function renderView($view, $data = array(), $dontDisplay = false) {
        $viewLocation = $this->getLocation() . $view;
        
        ob_start();
        require_once($viewLocation . ".php");
        $content = ob_get_clean();
        
        if (!$dontDisplay) {
            echo $content;
        } else {
            return $content;
        }
    }
    
    public static function createPage($view, $data = array(), $withparts = false) {
        $yr = new YayRender();
        
        $content = $yr->renderView($view, $data, true);
        
        if ($withparts) {
            //$header     = $yr->renderView("partials/header",      array(), true);
            //$footer     = $yr->renderView("partials/footer",      array(), true);
            //$navi       = $yr->renderView("partials/navi",        array(), true);
            //$siteheader = $yr->renderView("partials/siteheader",  array(), true);
            $search     = $yr->renderView("partials/search",      array(), true);
            $photonavi  = $yr->renderView("partials/photonavi",   array(), true);
            
            $login      = $yr->renderView("partials/login",       array(), true);
            $priceplans = $yr->renderView("partials/priceplans",  array(), true);
            $help       = $yr->renderView("partials/help",        array(), true);
            
            /*$layoutView = $yr->renderView("layout", 
                array("header" => $header, "footer" => $footer, "navi" => $navi, "siteheader" => $siteheader,
                    "search" => $search, "photonavi" => $photonavi, "content" => $content), 
                true);*/
            
            $layoutView = $yr->renderView("layout", 
                array("search" => $search, "photonavi" => $photonavi, "content" => $content,
                    "login" => $login, "priceplans" => $priceplans, "help" => $help), 
                true);
            
            return $layoutView;
        } else {
            return $content;
        }
        
    }
    
}
