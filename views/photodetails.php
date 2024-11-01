<input type="hidden" id="yayimages-photo-url" value=""/>
<input type="hidden" id="yi-currentid" value="<?php echo $data["id"]?>"/> 
<input type="hidden" id="yi-photographer" value=""/> 

<div class="yi-image-details">
    <h5><?php _e("IMAGE DETAILS",'yayimages');?></h5>
    <div id="yi-image" >
        <span class="yay-celebrity-icon">Editorial license</span>
        <div id="yi-image-preview-button"></div>
    </div>
    <img src="" id="yi-image-src" title=""/>
    
    <div class="yi-image-details-buttons">
        <button class="btn btn-slim yi-image-info-aviary" id="yi-image-aviary-edit"   ><?php _e("Edit image",'yayimages');?></button>
        <button class="yay-similarimages btn btn-slim" id="yi-similar-button" relid="<?php echo $data["id"]?>" relurl=""><?php _e("Similar images",'yayimages');?></button>
    </div>
    <h4></h4>
    <div class="yi-image-info">
        <div class="yi-image-info-row">
            <label><?php _e("ID",'yayimages');?>:</label>
            <div class="yi-image-info-id"><?php echo $data["id"]?></div>
        </div>
        <div class="yi-image-info-row">
            <label><?php _e("Description",'yayimages');?>:</label>
            <div class="yi-image-info-description"></div>
        </div>
        <div class="yi-image-info-row">
            <label><?php _e("Photographer",'yayimages');?>:</label>
            <div><a href="javascript:void(0);" class="yay_photographer  yi-image-info-photographer" relname=""></a></div>
        </div>
        <!--div class="yi-image-info-row">
            <label><?php _e("File type",'yayimages');?>:</label>
            <div class="yi-image-info-mime"></div>
        </div>
        <div class="yi-image-info-row">
            <label><?php _e("File dimensions",'yayimages');?>:</label>
            <div class="yi-image-info-size"></div>
        </div-->

    </div>
</div> 
<div id="yi-image-details-additional">
    <div class="yi-more-images">
        <h4><?php _e("Similar Images",'yayimages');?></h4>
        <div id="yi-similar-photos" class="yi-more-list"></div> 

        <div id="yi-similar-navi" class="yi-more-navi">
            <a href="javascript:void(0);" class="similarimages_lt">&lt;</a>
            <a href="javascript:void(0);" class="yay-similarimages" relid="<?php echo $data["imagedetails"]->id?>" relurl="<?php echo $data["imagedetails"]->largeThumbnailUrl?>"><?php _e("View all",'yayimages');?></a>
            <a href="javascript:void(0);" class="similarimages_gt">&gt;</a>
        </div>

    </div>

    <?php
//END SIMILAR IMAGES
    ?>

    <?php
//PHOTOGRAPHER IMAGES 
    ?>

    <div class="yi-more-images">
        <h4><?php _e("More from Photographer",'yayimages');?></h4>
        <div id="yi-photographer-photos" class="yi-more-list"></div> 
        <div id="yi-photographer-navi" class="yi-more-navi">
            <a href="javascript:void(0);" class="photographerimages_lt">&lt;</a>
            <a href="javascript:void(0);" class="yay_photographer" relname="<?php echo $data["imagedetails"]->photographer?>"><?php _e("View all",'yayimages');?></a>
            <a href="javascript:void(0);" class="photographerimages_gt">&gt;</a>
        </div>

    </div>

    <?php
//END PHOTOGRAPHER IMAGES
    ?> 


    <div class="yi-list-tabs">
        <div class="yi-list-tab">
            <input type="radio" name="tabs-1" id="keywords-tab" checked="checked" />
            <label for="keywords-tab"><?php _e("Keywords",'yayimages');?></label>
            <div id="yi-keywords-content"></div>
        </div>
        <div class="yi-list-tab">
            <input type="radio" name="tabs-1" id="license-tab" />
            <label for="license-tab"><?php _e("Royalty free",'yayimages');?></label>
            <div id="yi-license-content"></div>
        </div>
    </div>   
</div>
