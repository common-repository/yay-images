<input type="hidden" id="yay_url" value="<?php echo plugins_url("yayimages")?>"/> 

<div id="yi-main" class="yi-main">
    <div class="subcontent" id="yi-subcontent">
        <?php echo $data["search"]?>
        <?php echo $data["content"]?>
    </div>
    <?php echo $data["photonavi"]?>    
    <?php echo $data["login"]?>
    <?php echo $data["priceplans"]?>
    <?php echo $data["help"]?>
    <div id="yi-flash-msg">

        <dl id="system-message">
            <dd class="message message">
                <ul>
                    <li>
                        <p class="message-title"></p>
                        <p class="message-content"></p>
                    </li>
                    <a id="yi-message-close" href="#" class="yi-closeButton"></a>
                </ul>
            </dd>
        </dl>

    </div>
</div>

