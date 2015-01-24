<?php if(isset($Identity['IsAuthenticated'])){?>
	<li><a href="<?php if(isset($links['profile'])){echo $links['profile']['link'];}?>" title="<?php if(isset($links['profile'])){echo $links['profile']['title'];}?>"><?php echo $Identity['Name'];?></a></li>
	<li><a href="<?php if(isset($links['logout'])){echo $links['logout']['link'];}?>" title="<?php if(isset($links['logout'])){echo $links['logout']['title'];}?>"><?php if(isset($links['logout'])){echo $links['logout']['title'];}?></a></li>
<?php } else {?>
	<li><a href="<?php if(isset($links['registration'])){echo $links['registration']['link'];}?>" title="<?php if(isset($links['registration'])){echo $links['registration']['title'];}?>"><?php if(isset($links['registration'])){echo $links['registration']['title'];}?></a></li>
	<li><a href="<?php if(isset($links['login'])){echo $links['login']['link'];}?>" title="<?php if(isset($links['login'])){echo $links['login']['title'];}?>"><?php if(isset($links['login'])){echo $links['login']['title'];}?></a></li>
<?php }?>
