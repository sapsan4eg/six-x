<?php echo Bundles::getValue('awesome');?>
<?php echo Bundles::getValue('mirror');?>
<?php echo Bundles::getValue('summer');?>
<?php if(isset($localization_summer)){ echo $localization_summer;}?>
<form action="<?php echo $link_form;?>" method="POST">
	<input type="hidden" name="id" value="">
<div class="panel panel-default">
	<div class="panel-heading title">
		<div class="row"><div class="col-md-9">	
			<h4><?php if(isset($title)) echo $title;?></h4></div>
			<div class="col-md-3 col-xs-3">
				<div class="btn-group" data-toggle="buttons">
				  <label class="btn btn-default activators" id="on">
				    <input type="radio" name="options" id="option1" autocomplete="off" value="on"> <?php if(isset($buttons['on'])) echo $buttons['on'];?>
				  </label>
				  <label class="btn btn-default activators" id="off">
				    <input type="radio" name="options" id="option2" autocomplete="off" value="off"> <?php if(isset($buttons['off'])) echo $buttons['off'];?>
				  </label>
				</div>	
				<a class="btn btn-default" id="back" title="<?php if(isset($buttons['cancel'])) echo $buttons['cancel'];?>"><i class="fa fa-reply"></i></a>
				<button class="btn btn-primary" style="margin-right: 10px;" title="<?php if(isset($buttons['save'])) echo $buttons['save'];?>"><i class="fa fa-save"></i></button>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<ul class="nav nav-tabs" role="tablist" id="myTab">
			<?php if(isset($storys)) { $a = 0;?>
				<?php foreach ($storys as $value) {?>
			<li <?php if($a == 0) echo 'class="active"';?>><a href="#language_<?php echo $value['langid']; ?>" role="tab" data-toggle="tab">
				<img src="<?php echo HTTP_IMAGE . 'lang/' . $value['locale'] . '.png'; ?>" />
				<?php echo $value['name']; ?></a>
			</li>
				<?php $a++;}?>
			<?php }?>
		</ul>
		<div class="tab-content" style="padding-top:20px;">
			<?php if(isset($storys)) {  $a = 0;?>
				<?php foreach ($storys as $value) {?>
			 <div class="tab-pane<?php if($a == 0) echo ' active';?>" id="language_<?php echo $value['langid']; ?>">
			 	<h5><?php echo $title_text; ?></h5>
			 	<input type="text" autocomplete="off" name="title[<?php echo $value['langid'];?>]" placeholder="<?php echo $title_text; ?>" class="input-long" id="title_text_<?php echo $value['langid']; ?>" value="<?php echo $value['title']; ?>">
			 	<h5><?php echo $text_body; ?></h5>
				<textarea class="form-control" name="text[<?php echo $value['langid'];?>]" placeholder="<?php echo $text_body; ?>" id="text_story_<?php echo $value['langid']; ?>"><?php echo $value['text']; ?></textarea>
				<div class="row">
					<div class="col-md-4">
						<h5><?php echo $meta_title; ?></h5>
						<textarea name="meta_title[<?php echo $value['langid'];?>]" placeholder="<?php echo $meta_title; ?>" class="input-long" id="meta_title_<?php echo $value['langid']; ?>"><?php echo $value['meta-title']; ?></textarea>	
					</div>
					<div class="col-md-4">
						<h5><?php echo $meta_descr; ?></h5>
						<textarea name="meta_descr[<?php echo $value['langid'];?>]" placeholder="<?php echo $meta_descr; ?>" class="input-long" id="meta_descr_<?php echo $value['langid']; ?>"><?php echo $value['description']; ?></textarea>
					</div>
					<div class="col-md-4">
						<h5><?php echo $meta_key; ?></h5>
						<textarea name="meta_key[<?php echo $value['langid'];?>]" placeholder="<?php echo $meta_key; ?>" class="input-long" id="meta_key_<?php echo $value['langid']; ?>"><?php echo $value['key']; ?></textarea>
					</div>
				</div>
			 </div>
			 	<?php $a++;}?>
			<?php }?>
		</div>
	 </div>
</div>
</form>
<script>
$(function() {
	var active = "<?php if(isset($active)) {echo $active; } else { echo 'on';} ?>";
	$('#back').click(function() {
		window.location = '<?php echo $link_back;?>';
	});
	$("<?php if(isset($summers)) echo $summers;?>").summernote({
		codemirror: {
          theme: 'monokai'
       },
		height: 300<?php if(isset($lang_summer)){ echo ',' . PHP_EOL . 'lang:\'' . $lang_summer . '\'';}?>
	});
	$(".activators").click(function(){ChangeStateButton($(this));});
	function ChangeStateButton(e){
		active = e.attr("id");
		if(active == "on")
		{
			$("#on").removeClass("btn-default").addClass("btn-success active");
			$("#option1").attr("checked", "checked");
			$("#off").removeClass("btn-danger active").addClass("btn-default");
		} else {
			$("#off").removeClass("btn-default").addClass("btn-danger active");
			$("#option2").attr("checked", "checked");
			$("#on").removeClass("btn-success active").addClass("btn-default");
		}
	}
	ChangeStateButton($("#" + active));
});
</script>