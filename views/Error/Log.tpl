<?php echo Bundles::getValue('awesome');?>
<div class="panel panel-default">
	<div class="panel-heading">

		<div class="btn btn-danger pull-right" id="clearlog"><i class="fa fa-recycle"></i></div>
		<div class="btn btn-info pull-right" id="refresh" style="position:relative; left:-10px;"><i class="fa fa-refresh"></i></div>
        <!-- Single button -->
        <div class="btn-group pull-right" style="position:relative; left:-20px;">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $logfile;?> <span class="caret"></span>
            </button>
            <?php if(count($listlogs) > 0) { ?>
            <ul class="dropdown-menu">
                <?php foreach($listlogs As $logs) { ?>
                <li><a href="<?php if(isset($RequestedUrl)){
                    if(strpos($RequestedUrl, 'logfile') !== FALSE)
                    {
                        $start = substr($RequestedUrl, 0, strpos($RequestedUrl, 'logfile'));
                        $change = substr($RequestedUrl, strpos($RequestedUrl, 'logfile'));
                        $end = '';
                        if(strpos($change, '&') !== FALSE)
                        {
                            $end = substr($change, strpos($change, '&'));
                            $change = substr($change, 0, strpos($change, '&'));
                        }
                        echo  $start . 'logfile=' . $logs . $end;
                    } else echo $RequestedUrl . (strpos($RequestedUrl, '?') !==FALSE ? '&' : '?') . "logfile=" . $logs;}?>"><?php echo $logs;?></a></li>
                <?php }?>
            </ul>
            <?php }?>
        </div>
        <h3>Error log</h3>
		
	</div>
	<div class="panel-body">
		<textarea wrap="off" style="width: 100%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"><?php echo $log; ?></textarea>
	</div>
</div>
<script>
    $(function() {
    	$('#refresh').click(function(){
    		window.location = "<?php if(isset($RequestedUrl)){ echo $RequestedUrl;}?>";
    	});
    	$('#clearlog').click(function(){
    		$.ajax({
		      url: '<?php echo isset($links['clearlog']) ? $links['clearlog'] : '' ?>',
			  type: 'post',
			  data: {clear : 'clear'},
			  dataType: 'json',
			  success: function (json) {
				if(json['answer']){	
					if(json['answer'] == 'success'){
	                		window.location = "<?php if(isset($RequestedUrl)){ echo $RequestedUrl;}?>";
	                	} else{
	                		alert("Cannot delete log");
	                	}	
				}				
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
			      alert(thrownError + " " + xhr.statusText + " " + xhr.responseText);
			  }
			});
		});
    });
</script>