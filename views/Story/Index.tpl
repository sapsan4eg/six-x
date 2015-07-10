<?php echo Bundles::getValue('awesome');?>
<div class="panel panel-default" style="margin-top:20px;">
	<div class="panel-heading title">
		<div class="row">
			<div class="col-md-9">	
				<h4><?php if(isset($title)) echo $title;?></h4>
			</div>
			<div class="col-md-3 col-xs-3">
				<button class="btn btn-danger pull-right" id="delete" title="<?php echo isset($delete) ? $delete : "";?>"><i class="fa fa-recycle"></i></button>
				<button class="btn btn-success pull-right" id="create" style="position:relative; left:-10px;" title="<?php echo isset($create) ? $create : "";?>"><i class="fa fa-file-text-o"></i></button>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<form action="" method="POST">
		<?php if(isset($storys)) {
			foreach ($storys As $story) {;?>
		<div class="row"  style="margin:20px;">
			<div class="col-md-1 centers">
				<input type="checkbox" name="story[]" class="storys" value="<?php echo $story['story_id']?>" />
			</div>
			<div class="col-md-7 centers">
				<?php echo urldecode($story['title'])?>
			</div>
			<div class="col-md-2 centers"><?php echo $story['sort_order']?></div>
			<div class="col-md-2 centers">
				<button class="btn btn-info pull-right edit" id="edit_<?php echo $story['story_id']?>" title="<?php echo isset($edit) ? $edit : "";?>"><i class="fa fa-pencil"></i></button>				
			</div>
		</div>
		<?php }}?>
		</form>
	</div>
</div>

<div class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo isset($delete) ? $delete : "";?></h4>
      </div>
      <div class="modal-body">
        <p><?php echo isset($permanently) ? $permanently : "";?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo isset($close) ? $close : "";?></button>
        <button type="button" class="btn btn-primary" id="delete_all"><?php echo isset($submit) ? $submit : "";?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
<script>
$(function() {
	function getNumById(elem) {
        var str = elem;
        var start = str.indexOf("_") + 1;
        var end = str.length;
        var num = parseInt(str.substring(start, end));
        return num;
    }
	$('.edit').click(function(e) {
		e.preventDefault();
		window.location = '<?php echo $link_edit;?>&story=' + getNumById($(this).attr('id'));
	});
	$('#create').click(function() {
		window.location = '<?php echo $link_create;?>';
	});
	$('#delete').click(function() {
		if($('input:checked').length> 0) {
			$('.modal').modal('show');
		}
	});
	$('#delete_all').click(function(){
		$('form').submit();
	});
});
</script>