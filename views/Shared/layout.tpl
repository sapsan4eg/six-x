<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php if(isset($title)){ echo $title;}?></title>
  <?php echo Bundles::getValue('css') . Bundles::getValue('js');?>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?php if(isset($links['mainlink'])){ echo $links['mainlink']['link'];} else { echo HTTP_SERVER;}?>" title="<?php if(isset($links['mainlink'])){ echo $links['mainlink']['title'];} ?>" class="navbar-brand" style="padding-left:30px;">SIX-X<br />
                	<span style="font-size:9px; position:relative; top:-6px;">new PHP MVC framework</span></a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                	<?php if(isset($languges_partial)){echo $languges_partial;}?>
                    <?php if(isset($partialLogin)){echo $partialLogin;}?>
                </ul>
            </div>
        </div>
        <div id="blackBar"></div>
    </div>
    <div class="container body-content">
    	<?php if(isset($message)){?>
			<div class="alert alert-<?php echo $message[0];?>" role="alert" style="margin-top:20px;"><strong><?php echo $message[1];?>:</strong> <?php echo $message[2];?>
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			</div>
		<?php }?>
		<?php echo $RenderBody;?>
	</div>
	<div class="container">

	</div>
	<div class="container">
	<div class="row"><div class="col-md-4" style="padding-top:10px;">Six-X, <?php if(isset($core_version)){ echo $core_version;}?>: <?php echo VERSION;?></div>
		<div class="col-md-4 col-md-offset-4"></div>
			<ol class="breadcrumb pull-right">
				<li><?php if(isset($name_controller)){ echo $name_controller;}?>: <?php if(isset($ControllerName)){ echo $ControllerName;}?></li>
				<li><?php if(isset($name_action)){ echo $name_action;}?>: <?php if(isset($ActionName)){ echo $ActionName;}?></li>
			</ol>
		</div>
	</div>
</body>
</html>