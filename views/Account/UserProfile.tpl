<div class="panel panel-default" style="margin-top:20px;">
	<div class="panel-heading">
		<h3>Profile page of <?php if(isset($Identity['Name'])){ echo $Identity['Name'];}?></h3>
	</div>
	<div class="panel-body">
	<ul class="nav nav-tabs" role="tablist" id="myTab">
		<li class="active"><a href="#settings" role="tab" data-toggle="tab">User settings</a></li>
  		<li><a href="#pass" role="tab" data-toggle="tab">Change password</a></li>
	</ul>
	<div class="tab-content" style="padding-top:20px;">
	 <div class="tab-pane active" id="settings">
		<h4>User settings</h4>
	 </div>
	 <div class="tab-pane" id="pass">
		<form action="?controller=Account&action=Login" method="post" id="login">
			<div class="row" style="margin-bottom:20px;"><div class="col-md-2">
				<label for="oldpassword"><span class="required">Old password</span></label></div><div class="col-md-8">
				<input name="oldpassword" type="password" class="text required" id="oldpassword" minlength="5" maxlength="20">
			</div></div>
			<div class="row" style="margin-bottom:20px;"><div class="col-md-2">
				<label for="newpassword"><span class="required">New Password</span></label></div><div class="col-md-8">
				<input name="newpassword" type="password" class="text required" id="newpassword" minlength="5" maxlength="20">
			</div></div>
			<div class="row" style="margin-bottom:20px;"><div class="col-md-2">
				<label for="confirmpassword"><span class="required">Confirm new password</span></label></div><div class="col-md-8">
				<input name="confirmpassword" type="password" class="text required" id="confirmpassword" minlength="5" maxlength="20">
			</div></div>
		</form>
		</div>
	  </div>
	</div>
</div>
<?php echo Bundles::getValue('jsvalidate');?>
<script>
	$(function() {
		$("#login").validate({
			rules:{
          		confirmpassword: {
                required: true, equalTo: "#newpassword", minlength: 5
          		}
         	}
       	});
	});
</script>