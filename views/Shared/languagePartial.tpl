<?php if(isset($list_languges)){
	 foreach ($list_languges as $key => $value) {?>
		<li><a class="lang_site likeButton" id="<?php echo $key;?>">
			<span class="visible-xs col-xs-3"><?php echo $value;?></span>
			<img src="<?php echo HTTP_SERVER;?>lang/<?php echo $key;?>/flag.png" alt="<?php echo $value;?>" title="<?php echo $value;?>" />			
			</a>
		</li>
	<?php } 
	if(isset($link_change_languge)){?>
	<script>
		$(function(){
			$('.lang_site').click(function(){
				$.ajax({
	                url: '<?php echo $link_change_languge;?>',
	                type: 'post',
	                data: { language: $(this).attr("id")},
	                dataType: 'json',
	                beforeSend: function () {
	                },
	                complete: function () {
	                },
	                success: function (json) {
	                	if(json['answer'] == 'success'){
	                		window.location = "<?php if(isset($RequestedUrl)){ echo $RequestedUrl;}?>";
	                	} else{
	                		alert("cannot change language");
	                	}	                    
	                },
	                error: function (xhr, ajaxOptions, thrownError) {
	                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	                }
	            });				
			});
		});
	</script>
<?php }}?>