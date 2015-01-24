<?php echo Bundles::getValue('awesome');?>
<?php echo Bundles::getValue('payment');?>
<div class="row">
	<div class="col-md-7 col-xs-7" id="for_form_payment">
    <form novalidate autocomplete="on" method="POST" id="payment_form" class="payments">
    	<div class="row">
    		<div class="col-md-10 col-xs-8" style="min-height:40px;">
    			<h2><?php if(isset($personal_card)){ echo $personal_card;}?></h2>
    		</div>
    		<div class="col-md-2 col-xs-2" id="brand"></div>
    	</div>
    	<div class="row">
    		<div class="col-md-10">
		    	<div class="form-group">
			        <label for="cc-number" class="control-label"><?php if(isset($number)){ echo $number;}?></label>
			        <input id="cc-number" name="cc-number" type="text" class="form-control input-lg cc-number card-filds" pattern="\d*" autocomplete="cc-number" placeholder="<?php if(isset($number)){ echo $number;}?>"  value="<?php if(isset($values['cc-number'])){ echo $values['cc-number'];}?>" required>
		      	</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
		    	<div class="form-group">
			        <label for="cc-exp" class="control-label"><?php if(isset($expiry)){ echo $expiry;}?></label>
			        <input id="cc-exp" name="cc-exp" type="text" class="form-control cc-exp card-filds input-lg" pattern="\d*" autocomplete="cc-exp" placeholder="<?php if(isset($expiry_holder)){ echo $expiry_holder;}?>" value="<?php if(isset($values['cc-exp'])){ echo $values['cc-exp'];}?>" required>
		    	</div>
			</div>
			<div class="col-md-6">
				<a href="#" class="fa fa-bug pull-right" data-toggle="popover" title="<?php if(isset($help_cvc_title)){ echo $help_cvc_title;}?>" data-content="<?php if(isset($help_cvc_content)){ echo $help_cvc_content;}?>" data-trigger="focus" data-container="body" data-placement="top"></a>
				<div class="form-group">
			        <label for="cc-cvc" class="control-label"><?php if(isset($cvc)){ echo $cvc;}?> 			        	
			        </label>			        
			        <input id="cc-cvc" name="cc-cvc" type="text" class="form-control cc-cvc card-filds input-lg" pattern="\d*" autocomplete="off" placeholder="<?php if(isset($cvc)){ echo $cvc_holder;}?>" value="<?php if(isset($values['cc-cvc'])){ echo $values['cc-cvc'];}?>" required>					
				</div>
			</div>
		</div>     
		<div class="row">
			<div class="col-md-12">
		    	<div class="form-group">
			        <label for="numeric" class="control-label"><?php if(isset($card_holder)){ echo $card_holder;}?></label>
			        <input id="holder" name="holder" type="text" class="form-control input-lg card-filds" placeholder="<?php if(isset($card_holder)){ echo $card_holder;}?>" value="<?php if(isset($values['holder'])){ echo $values['holder'];}?>">
		    	</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
		    	<button type="submit" id="button_submit_payment" class="btn btn-lg btn-primary pull-right"><?php if(isset($button_submit)){ echo $button_submit;}?></button>
			</div>
		</div>      
    </form>
    </div>
    <div class="col-md-5 col-xs-5">
    	<div class="payments" id="payment_erros">
    		<h2 id="error_title"><?php if(isset($error_main)){ echo $error_main;}?></h2>
    		<ol id="all_errors">
    			<li><?php if(isset($error_number)){ echo $error_number;}?></li>
    			<li><?php if(isset($error_expiry)){ echo $error_expiry;}?></li>
    			<li><?php if(isset($error_cvc)){ echo $error_cvc;}?></li>
    		</ol>
    	</div>
    </div>
</div>
<script>
    $(function($) {
    	$('#payment_erros').hide();
    	$('#button_submit_payment').attr('disabled','disabled');
    	
    	var error_list = {'cc-number' : '<?php if(isset($error_number)){ echo $error_number;}?>', 
    					'cc-exp' : '<?php if(isset($error_expiry)){ echo $error_expiry;}?>',
    					'cc-cvc' : '<?php if(isset($error_cvc)){ echo $error_cvc;}?>',
    					'holder' : "<?php if(isset($error_cardholder)){ echo $error_cardholder;}?>"};
    	var errors = Array();
    	$('.fa-bug').popover().click(function(e){e.preventDefault();});
    	$('.fa-bug').popover({trigger : 'focus'});
    	
    	Enter_on_input();
		$('.cc-number').payment('formatCardNumber');
		$('.cc-exp').payment('formatCardExpiry');
		$('.cc-cvc').payment('formatCardCVC');
		
		$.fn.toggleInputError = function(erred) {
		  this.parent('.form-group').toggleClass('has-error', erred);
		  return this;
		};
		
		$('#payment_form').submit(function(e) {
			//e.preventDefault();	
				
		});
		$('.cc-number').keyup(function() {
		  	showBrand();
		});
		$('.card-filds').focusout(function(e) {
			validate_fild(this);			
		});	
		if($('#cc-number').val().length > 0)
		{	
			validate_fild($('.card-filds'));
			showBrand();
		}
		///////////////////////////////////////////////////////
	    ////////            Enter on input              ///////
	    ///////////////////////////////////////////////////////
	    function Enter_on_input() {
	        var myInputs = $("input:text");
	        if (myInputs.length > 0) {
	            myInputs[0].focus();
	        }
	        myInputs.keydown(function (e) {
	            if (e.keyCode == 13) {	              
	                for (i = 0; i < myInputs.length; i++) {
	                    if (myInputs[i].name == $(this).attr("name")) {	                    	
	                        try { myInputs[i + 1].focus(); e.preventDefault(); 
	                        } catch (event) { validate_fild(myInputs[i]); checkNeedDisabledButton();}
	                    }
	                }	
	            }
	        });
	    }
	    function showBrand()
	    {
	    	var number = $('.cc-number').val(); 
		  	if (number.length > 0) {
		  		var cardType = $.payment.cardType($('.cc-number').val());
		  		if (cardType != null) {
					$('#brand').html('<img src="<?php echo HTTP_IMAGE;?>cc/' + cardType + '.png" title="' + cardType + '" />');
				} else {
					$('#brand').html('');
				}
		  	} else {
		  		$('#brand').html('');
		  	}
	    }
	    function validate_fild(obj)
	    {
	    	var nedRefresh = false;
	    	$(obj).each(function(){
	    		var bools = false;
		    	var id = $(this).attr('id');
				switch(id)
				{
					case 'cc-number': 
						bools = $.payment.validateCardNumber($(this).val());
						if(bools && $('#cc-cvc').val().length > 0){
							validate_fild($('#cc-cvc'));
						}
						break;
					case 'cc-exp': 
						bools = $.payment.validateCardExpiry($(this).payment('cardExpiryVal'));
						break;
					case 'cc-cvc': 
						bools = $.payment.validateCardCVC($(this).val(), $.payment.cardType($('.cc-number').val()));
						break;
					case 'holder': 
						$(this).val($(this).val().trim());
						bools = validate_string($(this).val());
						break;
				}	
				$(this).toggleInputError( ! bools);
				if (bools == false && ! errors[id]) {
					errors[id] = error_list[id];	
					nedRefresh = true;
				} else if(bools == true && errors[id]) {
					delete errors[id];
					nedRefresh = true;
				}		
	    	});
	    	
			if(nedRefresh == true){
				showErrors();
			}	
			checkNeedDisabledButton();
	    }
	    function showErrors()
	    {
	    	if (Object.keys(errors).length > 0) {
	    		
	    		
	    		if ($("#payment_erros").is(':hidden')) {  
	    			$("#all_errors").html("");	    		
		    		var html = "";
	    			for(var i in errors) {
	    				html += "<li>" + errors[i] + "</li>";
	    			}
	    			$("#all_errors").html(html);
	    			$("#payment_erros").fadeIn("slow");
	    		} else {
	    			$("#payment_erros").fadeOut("slow", function(){ showErrors();});
	    		};
	    	} else if( ! $("#payment_erros").is(':hidden')) {
	    		$("#payment_erros").fadeOut("slow"); 
	    	}
	    }
	    $('#holder').keydown(function(e){
	    	var code = e.keyCode;
	    	var code_array = [8, 9, 13, 16, 20, 27, 32, 35, 36, 37, 38, 39, 40, 45, 46, 116, 117];
	    	if (code_array.indexOf(code) != -1 || (code > 64 && code < 91)) {
	    		
	    	} else {
	    		e.preventDefault();
	    	}
	    });
	    $('#holder').bind('paste', function(e){
	    	//e.preventDefault();
	    });
	    function validate_string(str) {
	    	var r = true;
	    	if(str.length > 0) {
	    		var regexp = /^([a-z ])+$/i;
	    		r = regexp.test(str);
	    	}
	    	return r;
	    }
	    function checkNeedDisabledButton() {
	    	if (Object.keys(errors).length == 0) {
				var notNull = true;
				$('.card-filds').each(function(){
					if($(this).attr('id') != 'holder' && $(this).val().length == 0) {
						notNull = false;
					}
				});
				if(notNull == true) {
					$('#button_submit_payment').removeAttr('disabled');
				}					
			} else if($('#button_submit_payment').attr('disabled') != 'disabled') {
				$('#button_submit_payment').attr('disabled','disabled');
			}	
	    }
    });
  </script>