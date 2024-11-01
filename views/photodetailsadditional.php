
<?php
//SIMILAR IMAGES 
?>

<div class="yi-more-images">
    <h4><?php _e( "Similar Images", 'yayimages' ); ?></h4>
    <div id="yi-similar-photos" class="yi-more-list">

        <?php echo  $data['similarimages']?>

    </div> 

    <div id="yi-similar-navi" class="yi-more-navi">
        <a href="javascript:void(0);" class="similarimages_lt">&lt;</a>
        <a href="javascript:void(0);" class="yay-similarimages" relid="<?php echo  $data["imagedetails"]->id?>" relurl="<?php echo  $data["imagedetails"]->largeThumbnailUrl ?>"><?php _e( "View all", 'yayimages' ); ?></a>
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
    <h4><?php _e( "More from Photographer", 'yayimages' ); ?></h4>
    <div id="yi-photographer-photos" class="yi-more-list">

        <?php echo  $data['photographerimages']?>

    </div> 

    <div id="yi-photographer-navi" class="yi-more-navi">
        <a href="javascript:void(0);" class="photographerimages_lt">&lt;</a>
        <a href="javascript:void(0);" class="yay_photographer" relname="<?php echo  $data["imagedetails"]->photographer?>"><?php _e( "View all", 'yayimages' ); ?></a>
        <a href="javascript:void(0);" class="photographerimages_gt">&gt;</a>
    </div>

</div>

<?php
//END PHOTOGRAPHER IMAGES
?> 


<div class="yi-list-tabs">
    <div class="yi-list-tab">
        <input type="radio" name="tabs-1" id="keywords-tab" checked="checked" />
        <label for="keywords-tab"><?php _e( "Keywords", 'yayimages' ); ?></label>
        <div>
            <?php foreach ($data["imagedetails"]->keywords as $keyw) {?>
                <a href="javascript:void(0);" class="yay-keyword"><?php echo $keyw?></a>,&nbsp; 
            <?php }?>
        </div>
    </div>
    <div class="yi-list-tab">
        <input type="radio" name="tabs-1" id="license-tab" />
        <label for="license-tab"><?php _e( "Royalty free", 'yayimages' ); ?></label>
        <div id="yi-license-content">
            <?php echo $data["imagedetails"]->license; ?>
        </div>
    </div>
</div>   