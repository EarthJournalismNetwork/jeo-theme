<form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
    <div>
    	<input type="text" name="s" id="s" placeholder="<?php _e('Search for stories', 'infoamazonia'); ?>" value="<?php echo isset($_GET['s']) ? $_GET['s'] : ''; ?>" />
        <input type="submit" class="button" id="searchsubmit" value="<?php _e('Search', 'infoamazonia'); ?>" />
    </div>
</form>
