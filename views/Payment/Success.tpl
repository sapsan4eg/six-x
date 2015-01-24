<div class="row">
	<div class="col-md-6">
		<div class="panel panel-success" style="margin-top:20px;">
			<div class="panel-heading">
				<h3><?php if(isset($answer_title)){ echo $answer_title;}?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6"><h4><?php if(isset($number)){ echo $number;}?></h4></div>
					<div class="col-md-6"><?php if(isset($card_data['number'])){ echo $card_data['number'];}?></div>
				</div>
				<div class="row">
					<div class="col-md-6"><h4><?php if(isset($expiry)){ echo $expiry;}?></h4></div>
					<div class="col-md-6"><?php if(isset($card_data['expire'])){ echo $card_data['expire'];}?></div>
				</div>
				<div class="row">
					<div class="col-md-6"><h4><?php if(isset($cvc)){ echo $cvc;}?></h4></div>
					<div class="col-md-6"><?php if(isset($card_data['cvc'])){ echo $card_data['cvc'];}?></div>
				</div>
				<div class="row">
					<div class="col-md-6"><h4><?php if(isset($card_holder)){ echo $card_holder;}?></h4></div>
					<div class="col-md-6"><?php if(isset($card_data['holder'])){ echo $card_data['holder'];}?></div>
				</div>
				<a href="<?php if(isset($linkBuck['link'])){ echo $linkBuck['link'];}?>" class="btn btn-success pull-right"> 				
					<?php if(isset($linkBuck['title'])){ echo $linkBuck['title'];}?>
				</a>
			</div>
		</div>
	</div>
</div>