<?php if(isset($Identity['IsAuthenticated'])){?>
	<li><a href="<?php if(isset($links['profile'])){echo $links['profile'];}?>" title=""><?php echo $Identity['Name'];?></a></li>
	<li><a href="<?php if(isset($links['logout'])){echo $links['logout'];}?>" title="<?php echo _('button_logout');?>"><?php echo _('button_logout');?></a></li>
<?php } else {?>
	<li><a href="<?php if(isset($links['registration'])){echo $links['registration'];}?>" title="<?php echo _('button_registration');?>"><?php echo _('button_registration');?></a></li>
	<li><a href="<?php if(isset($links['login'])){echo $links['login'];}?>" title="<?php echo _('button_login');?>"><?php echo _('button_login');?></a></li>
<?php }?>
