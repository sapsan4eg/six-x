<?php echo Bundles::getValue('awesome');?>
<div class="panel panel-default" style="margin-top:20px;">
	<div class="panel-heading">
		<div class="btn btn-danger pull-right" id="clearlog"><i class="fa fa-recycle"></i></div>
		<div class="btn btn-info pull-right" id="refresh" style="position:relative; left:-10px;"><i class="fa fa-refresh"></i></div>
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
	                		alert("cannot change language");
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