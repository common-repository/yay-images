<div class="wrap">
  
  <h2><?php esc_html_e('YayImages plugin', 'yayimages'); ?></h2>  

<div id="poststuff" class="metabox-holder has-right-sidebar">
     

    <div id="post-body">
      <div id="post-body-content" class="meta-box-sortables">

         

        <div id="mpp_box_settings" class="postbox">
          
          <h3 class="hndle"><span><?php _e('Settings', 'yayimages'); ?></span></h3>
          <div class="inside">
            <form id="yay_form_settings">
              <table class="form-table"> 

                <tr>
                  <th scope="row"><label for="yay_image_caption"><?php _e('Image caption', 'yayimages'); ?></label></th>
                  <td>
                    <?php $image_caption = isset($settings['yay_image_caption'])?$settings['yay_image_caption']:$this->default_settings['yay_image_caption']; ?>
                    

                    <div class="mpp_image_caption_group">
                      <input type="radio" name="yay_image_caption" value="copyright" id="mpp_image_caption_copyright"<?php echo ($image_caption=='copyright'?' checked':''); ?> />
                      <label for="mpp_image_caption_copyright"><?php esc_html_e('Copyright notice (automatically generated)', 'yayimages'); ?></label>
                    </div>

                    <div class="mpp_image_caption_group">
                      <input type="radio" name="yay_image_caption" value="none" id="mpp_image_caption_none"<?php echo ($image_caption=='none'?' checked':''); ?> />
                      <label for="mpp_image_caption_none"><?php esc_html_e('None', 'yayimages'); ?></label>
                    </div>
                  </td>
                </tr> 

              </table>
            </form>
            <div class="yay_save_area">
              <span class="yay_settings_saved"><?php _e('Settings saved.', 'yayimages'); ?></span>
              <span class="yay_save_loader"><img id="yay_save_loader_settings" src="<?php echo $this->_url; ?>/images/loading.gif" width="15" height="15" /></span>
              <input class="button-primary yay_button_save" id="yay_save_settings" type="submit" name="save" title="<?php _e('Save', 'yayimages'); ?>" value="<?php _e('Save', 'yayimages'); ?>" />
            </div>
            <br class="clear" />
          </div>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
