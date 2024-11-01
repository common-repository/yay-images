<div id="yi-search">
    <div id="yi-search-top">
        <input type="text" id="yi-search-key" rel="phrase" placeholder="Enter keywords..." />
        <button class="btn"  id="yay_search"><?php _e("Search", 'yayimages'); ?></button>	                
        <div class="yi-search-tabs">
            <label for="yi-filters-simple" id="yi-search-tab-simple" class="yi-search-tab yi-tabs-active yi-tabs-down">
                <?php _e("Search filters", 'yayimages'); ?>
            </label>            
            <label for="yi-filters-adv" id="yi-search-tab-adv" class="yi-search-tab">
                <?php _e("Creative filters", 'yayimages'); ?> <span class="count"></span>
            </label>            
        </div>		
    </div>    
</div>

<input type="checkbox" id="yi-filters-simple" class="yi-filters-on yi-filters-simple" checked="checked"/>
<div class="yi-filters-box yi-filters-simple-box">
    <div class="yi-search-display-size">
        <div>
            <span class="title"><?php _e("Display size", 'yayimages'); ?>:</span>
            <div id="yi-slider"></div>
        </div>
    </div>
    <div class="yi-search-category">
        <div>
            <span class="title">Filters:</span>                
            <div class="yi-filters">
                <label>                    
                    <input type="checkbox" name="filter" class="yi-filter-type" />
                    <span class="options">
                        <span>
                            <input type="radio" id="yi-search-people-any" name="yi-search-people" value="-1" checked="checked" rel="people" />
                            <label for="yi-search-people-any">
                                <span class="yi-sprite-filters-people-all"></span>
                                <span class="yi-filters-label-text"><?php _e("Any", 'yayimages'); ?></span>
                            </label>                                
                        </span>
                        <span>
                            <input type="radio" id="yi-search-people-none" name="yi-search-people" value="n" rel="people" />
                            <label for="yi-search-people-none">
                                <span class="yi-sprite-filters-people-off"></span>
                                <span class="yi-filters-label-text"><?php _e("None", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-people-people" name="yi-search-people" value="p" rel="people" />
                            <label for="yi-search-people-people">
                                <span class="yi-sprite-filters-people-on"></span>
                                <span class="yi-filters-label-text"><?php _e("People", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-people-one" name="yi-search-people" value="1p" rel="people" />
                            <label for="yi-search-people-one">
                                <span class="yi-sprite-filters-people-one"></span>
                                <span class="yi-filters-label-text"><?php _e("One Person", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-people-two" name="yi-search-people" value="2p" rel="people" />
                            <label for="yi-search-people-two">
                                <span class="yi-sprite-filters-people-two"></span>
                                <span class="yi-filters-label-text"><?php _e("Two People", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-people-two-group" name="yi-search-people" value="g" rel="people" />
                            <label for="yi-search-people-two-group">
                                <span class="yi-sprite-filters-people-group"></span>
                                <span class="yi-filters-label-text"><?php _e("Groups", 'yayimages'); ?></span>
                            </label>
                        </span>
                    </span>
                    <span class="yi-filter-name"><?php _e("People", 'yayimages'); ?></span>
                    <span class="yi-title-layer" title="<?php _e("People", 'yayimages'); ?>"></span>
                </label>
                <label>
                    <input type="checkbox" name="filter" class="yi-filter-type" />
                    <span class="options">
                        <span>
                            <input type="radio" id="yi-search-orientation-any" name="yi-search-orientation" value="all" checked="checked" rel="orientation" />
                            <label for="yi-search-orientation-any">
                                <span class="yi-sprite-filters-orientation-all"></span>
                                <span class="yi-filters-label-text"><?php _e("Any", 'yayimages'); ?></span>
                            </label>                                   
                        </span>
                        <span>
                            <input type="radio" id="yi-search-orientation-horizontal" name="yi-search-orientation" value="horizontal" rel="orientation" />
                            <label for="yi-search-orientation-horizontal">
                                <span class="yi-sprite-filters-orientation-horizontal"></span>
                                <span class="yi-filters-label-text"><?php _e("Horizontal", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-orientation-vertical" name="yi-search-orientation" value="vertical" rel="orientation" />
                            <label for="yi-search-orientation-vertical">
                                <span class="yi-sprite-filters-orientation-vertical"></span>
                                <span class="yi-filters-label-text"><?php _e("Vertical", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-orientation-square" name="yi-search-orientation" value="square" rel="orientation" />
                            <label for="yi-search-orientation-square">
                                <span class="yi-sprite-filters-orientation-square"></span>
                                <span class="yi-filters-label-text"><?php _e("Square", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <!--span>
                            <input type="radio" id="yi-search-orientation-panorama" name="yi-search-orientation" value="panorama" rel="orientation" />
                            <label for="yi-search-orientation-panorama">
                                <span class="yi-sprite-filters-orient-panorama"></span>
                                <span class="yi-filters-label-text"><?php _e("Panorama", 'yayimages'); ?></span>
                            </label>
                        </span-->
                    </span>
                    <span class="yi-filter-name"><?php _e("Orientation", 'yayimages'); ?></span>
                    <span class="yi-title-layer" title="<?php _e("Orientation", 'yayimages'); ?>"></span>
                </label>

                <label>
                    <input type="checkbox" name="filter" class="yi-filter-type" />
                    <span class="options">
                        <span>
                            <input type="radio" id="yi-search-vector-any" name="yi-search-vector" value="0" checked="checked" rel="vector" />
                            <label for="yi-search-vector-any">
                                <span class="yi-sprite-filters-vector-all"></span>
                                <span class="yi-filters-label-text"><?php _e("All", 'yayimages'); ?></span>
                            </label>                                   
                        </span>
                        <span>
                            <input type="radio" id="yi-search-vector-vector" name="yi-search-vector" value="1" rel="vector" />
                            <label for="yi-search-vector-vector">
                                <span class="yi-sprite-filters-vector-on"></span>
                                <span class="yi-filters-label-text"><?php _e("Vectors", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" id="yi-search-vector-no" name="yi-search-vector" value="-1" rel="vector" />
                            <label for="yi-search-vector-no">
                                <span class="yi-sprite-filters-vector-off"></span>
                                <span class="yi-filters-label-text"><?php _e("Photos", 'yayimages'); ?></span>
                            </label>
                        </span>                            
                    </span>
                    <span class="yi-filter-name"><?php _e("Type", 'yayimages'); ?></span>
                    <span class="yi-title-layer" title="<?php _e("Type", 'yayimages'); ?>"></span>
                </label>

                <label>
                    <input type="checkbox" name="filter" class="yi-filter-type" />
                    <span class="options">
                        <span>
                            <input type="radio" class="yi-search-category-field" name="yi-search-category" 
                                   rel="category" value="" checked="checked" id="yi-search-category-all" />
                            <label for="yi-search-category-all">
                                <span class="yi-sprite-filters-category-all"></span>
                                <span class="yi-filters-label-text"><?php _e("All", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" class="yi-search-category-field" name="yi-search-category" 
                                   rel="category" value="celebrity" id="yi-search-category-celebrity" />
                            <label for="yi-search-category-celebrity">
                                <span class="yi-sprite-filters-category-celebrity"></span>
                                <span class="yi-filters-label-text"><?php _e("Celebrity", 'yayimages'); ?></span>
                            </label>
                        </span>
                        <span>
                            <input type="radio" class="yi-search-category-field" name="yi-search-category" 
                                   rel="category" value="illustration" id="yi-search-category-creative" />
                            <label for="yi-search-category-creative">
                                <span class="yi-sprite-filters-category-illustration"></span>
                                <span class="yi-filters-label-text"><?php _e("Creative", 'yayimages'); ?></span>
                            </label>
                        </span>
                    </span>
                    <span class="yi-filter-name"><?php _e("Category", 'yayimages'); ?></span>
                    <span class="yi-title-layer" title="<?php _e("Category", 'yayimages'); ?>"></span>
                </label>




                <label>
                    <input type="checkbox" name="filter" class="yi-filter-type" />
                    <span class="options yi-filter-on">
                        <span>
                            <input type="radio" id="yi-search-explicit-off" name="yi-search-explicit" rel="explicit" value="0" />
                            <label for="yi-search-explicit-off">
                                <span class="yi-sprite-filters-explicit-all"></span>
                                <span class="yi-filters-label-text">Off</span>
                            </label>                            
                        </span>
                        <span>
                            <input type="radio" id="yi-search-explicit-on" name="yi-search-explicit" rel="explicit" value="1" checked="checked" />
                            <label for="yi-search-explicit-on">
                                <span class="yi-sprite-filters-explicit-on"></span>
                                <span class="yi-filters-label-text">On</span>
                            </label>
                        </span>                                                  
                    </span>
                    <span class="yi-filter-name"><?php _e("Explicit", 'yayimages'); ?></span>
                    <span class="yi-title-layer" title="<?php _e("Explicit", 'yayimages'); ?>"></span>
                </label>
            </div>
            <button class="btn yay_resetfilters" title="<?php _e("Reset filters", 'yayimages'); ?>"><?php _e("Reset filters", 'yayimages'); ?></button>
        </div>
    </div>
</div>
<input type="checkbox" id="yi-filters-adv" class="yi-filters-on yi-filters-adv"/>
<div class="yi-filters-box yi-filters-adv-box">
    <button class="btn yay_resetfilters"><?php _e("Reset filters", 'yayimages'); ?></button>
    <div class="yi-search-adv-visual">
        <div>
            <span class="title"><?php _e("Visual Search", 'yayimages'); ?>:<span class="count"></span></span>
            <div class="yi-search-adv-visual-placeholder">
                <img id="yi-search-adv-visual-image" />
                <span id="yi-search-adv-visual-close"></span>
                <span class="yi-search-adv-visual-icon-image">Drag and drop any image here</span>
            </div>
            <span class="subtitle"><?php _e("Visual Weight", 'yayimages'); ?>:</span>
            <div id="yi-search-adv-visual-slider"></div>
        </div>
    </div>
    <div class="yi-search-adv-color">
        <div>
            <span class="title"><?php _e("Color", 'yayimages'); ?>:<span class="count"></span></span>
            <div class="palette">
                <?php
                $colors = array("Red", "Orange", "Yellow", "Green", "Cyan", "Blue", "Purple", "Pink", "White", "Grey", "Black", "Brown");
                foreach ($colors as $color) {
                    ?>
                    <div class="yi-adv-color" style="background-color:<?php echo $color ?>;" status="0" relval="<?php echo $color ?>"></div>
                    <?php
                }
                ?>     
            </div>
        </div>
    </div>
</div>
<div class="yi-filters-line"></div>
