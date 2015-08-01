<h2><?php if(isset($title)){ echo $title;}?></h2>
<div class="row">
    <div class="col-md-7">
    <form action="?controller=Account&action=Login<?php if(isset($camefrom)){ echo '&Came_From=' . $camefrom;}?>" method="post" id="login">
        <section id="loginForm">
            <h4><?php if(isset($text_local_account)){ echo $text_local_account;}?></h4>
            <hr />
            <div class="form-group">
                <label for="email">
					<span class="required"><?php if(isset($email)){ echo $email;}?></span>
				</label>
				<input id="email" name="email" autocomplete="off" class="text required email input-medium" type="text">
            </div>
            <div class="form-group">
            	<label for="password">
					<span class="required"><?php if(isset($password)){ echo $password;}?></span>
				</label>
				<input name="password" type="password" class="text required input-medium" id="password" minlength="5" maxlength="20">
            </div>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <input type="submit" value="<?php if(isset($login)){ echo $login;}?>" class="btn btn-default pull-right" />
                </div>
            </div>
            <p>
                <a href="" title="register"><?php if(isset($register)){ echo $register;}?></a>
            </p>
        </section>
    </form>
    </div>
</div>
<?php echo Bundles::getValue('jsvalidate');?><?php if(isset($localization_validate)){ echo $localization_validate;}?>
<script>
	$(function() {
		$("#login").validate()
	});
</script>
