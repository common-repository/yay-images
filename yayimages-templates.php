<script type="text/html" id="tmpl-yay-title-bar">
    <div id="yi-siteheader" >
        <img id="yi-logo" src="<?php echo plugins_url("/images/yay-logo.png", __FILE__)?>" />
        <div id="yi-title-bar-message-box">
        </div>    
        <div id="yi-menu">
            <a id="yay-login"><?php _e("Login",'yayimages');?></a>
            <span id="yay-login-param" style="display:none;"><?php _e("Welcome",'yayimages');?>, <span></span> 
                <a id="yay-logout">Logout</a></span> &nbsp; &nbsp; 
            <a href="https://yayimages.com/pricing" id="yay-priceplans" target="_blank"><?php _e("Price Plans",'yayimages');?></a>
        </div>
    </div>
    <?php
    //settings data
    $settings = get_option('YayimagesPlugin_settings',array());
    foreach ($settings as $setting => $val) {
        ?>
        <input type="hidden" id="yay_setting_<?php echo $setting;?>" value="<?php echo $val;?>"/>
    <?php }?>
    <?php
    //login session data
    if (isset($_SESSION['yay_session_data'])) {
        $loginInfo = $_SESSION['yay_session_data'];
        ?>
        <input type="hidden" id="yay_login_id" value="<?php echo $loginInfo->id;?>"/>
        <input type="hidden" id="yay_login_email" value="<?php echo $loginInfo->email;?>"/>
        <input type="hidden" id="yay_login_token" value="<?php echo $loginInfo->token;?>"/> 

        <?php
    }
    ?>

</script>

<script type="text/html" id="tmpl-yay-content">
<?php 
$yay = new YayImage();
$yay->actionHome();
?>
</script>

<script type="text/html" id="tmpl-yay-toolbar"> 
    <div class="inner text-right" id="yay-media-toolbar"> 
        <?php if (current_theme_supports('post-thumbnails')) { ?>
            <button   class="btn-thumbnail-yay btn-slim btn"><?php _e('Set as featured image','yayimages');?></button>
        <?php } ?>
        <button  class="btn-ok-yay button-primary button-large"><?php _e('Insert into post','yayimages');?></button>
    </div>
</script>