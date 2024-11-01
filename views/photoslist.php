<?php
if (isset($data['list']->images) && $data['list']->total > 0) {
    foreach ($data['list']->images as $image) {
        ?>

        <div class="yay-image-thumb yay_imagedetails" relid="<?php echo $image->id?>" 
             style="background-image: url('<?php echo str_replace("128x128","200x200",$image->thumbnailUrl)?>');">
                 <?php if ($image->category == "celebrity") {?>
                <span class="yay-celebrity-icon"><span>Editorial</span></span>
                <?php
            }
            ?>
            <script class="imageData" id="imageData_<?php echo $image->id;?>" type="application/json">
                <?php echo json_encode($image,JSON_HEX_APOS | JSON_HEX_QUOT);?>
            </script>            
        </div> 

    <?php
    }
} else {
    ?>

    <p><?php _e("No images found",'yayimages');?></p>
<?php }?>


