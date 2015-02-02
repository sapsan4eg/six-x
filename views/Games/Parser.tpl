<?php echo Bundles::getValue('awesome');?>
<?php echo Bundles::getValue('payment');?>
<h1>GAMES Controller</h1>
<div class="row">
	<div class="col-md-6">
		<div class="payments">
			<div class="btn btn-default btn-lg" id="teams"><i class="fa fa-comment"></i> New teams</div>
			<div class="btn btn-default btn-lg" id="teams_default"><i class="fa fa-cloud"></i> Teams defaults</div>
			<hr />			
			<div class="dropdown pull-right">
				<span class="badge btn-success likeButton  dropdown-toggle" data-toggle="dropdown" id="buttodrop">
					<i class="fa fa-bug"></i>
				</span>
				<ul class="dropdown-menu" role="menu" id="dropMenu"></ul>
			</div>
			<h1 id="nameclub">Name of club</h1>
			<div class="btn btn-info btn-lg" id="set_players"><i class="fa fa-child"></i> Set Players</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="payments" id="payment_erros">
			<h2 id="naswer_title">Events</h2>
			<ol id="all_errors">
			</ol>
	    </div>
    </div>
</div>
<script>
    $(function($) {
    	var teams = new Array();
    	var currentTeam = 0;
    	TakeTeams();
    	$('#payment_erros').hide();
    	$('#teams').click(function() {    		
			 sendAjax('?controller=Games&action=GetTeams', 'Try to find new team');
			 TakeTeams();
    	});
    	$('#teams_default').click(function() {    		
			 sendAjax('?controller=Games&action=GetTeamsDefaults', 'Try to find defaults to teams');
    	});
    	$('#set_players').click(function() { 
    		//sendAjax('?controller=Games&action=SetPlayers', 'Try insert player: ');
    		$(".btn").attr('disabled','disabled');   		
			$.ajax({
		      url: '?controller=Games&action=SetPlayers',
			  type: 'post',
			  data: {team : currentTeam},
			  dataType: 'json',
			  success: function (json) {
				if(json['answer']){	
					var s = '';
					/*for(var a = 0; a < json['answer'].length; a++)
					{
						s += " " + json['answer'][a]['id'] + " " + json['answer'][a]['name'] + " " + json['answer'][a]['birth'] + " " + json['answer'][a]['country'] + " " + json['answer'][a]['height'] + " " + json['answer'][a]['width'] + "<br />";
					}	*/			
					if ($("#payment_erros").is(':hidden')) {
						$( "#all_errors" ).append( "<li>Try insert players: " + json['answer'] + ".</li>" );
						$("#payment_erros").fadeIn("slow");
					} else {
    					$("#payment_erros").fadeOut("slow", function(){ 
    						$( "#all_errors" ).append( "<li>Try insert players: " + json['answer'] + ".</li>" );
    						$("#payment_erros").fadeIn("slow");
    					});
    				};
				}	
				$(".btn").removeAttr('disabled');				
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
			      alert(thrownError + " " + xhr.statusText + " " + xhr.responseText);
			      $(".btn").removeAttr('disabled');
			  }
			  });
    	});
    	function sendAjax(href, asnwer)
    	{
    		$(".btn").attr('disabled','disabled');
    		$.ajax({
		      url: href,
			  type: 'post',
			  dataType: 'json',
			  success: function (json) {
				if(json['answer']){
					
					if ($("#payment_erros").is(':hidden')) {
						$( "#all_errors" ).append( "<li>" + asnwer + ": " + json['answer'] + ".</li>" );
						$("#payment_erros").fadeIn("slow");
					} else {
    					$("#payment_erros").fadeOut("slow", function(){ 
    						$( "#all_errors" ).append( "<li>" + asnwer + ": " + json['answer'] + ".</li>" );
    						$("#payment_erros").fadeIn("slow");
    					});
    				};
				}	
				$(".btn").removeAttr('disabled');				
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
			      alert(thrownError + " " + xhr.statusText + " " + xhr.responseText);
			      $(".btn").removeAttr('disabled');
			  }
		  });			  
    	}
    	$('#buttodrop').click(function () {
            $('#dropMenu').html(TakeListTeams());
        });
        function TakeListTeams()
        {
        	var s = '';
        	for(var a = 0; a< teams.length; a++)
        	{
        		s += '<li><a role="menuitem" class="teamslist" href="#" tabindex="-1" id="teamid_' + teams[a]['id'] + '">' + teams[a]['name'] + '</a></li>';
        	}
        	return s;
        }
        function TakeTeams()
    	{
    		$.ajax({
		      url: '?controller=Games&action=GetListTeams',
			  type: 'post',
			  dataType: 'json',
			  success: function (json) {
				teams = json;
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
			      alert(thrownError + " " + xhr.statusText + " " + xhr.responseText);
			  }
		  });			  
    	}
    	$('body').delegate(".teamslist", "click", function (e) {
    		e.preventDefault();
    		currentTeam = getNumById($(this).attr("id"));
    		$('#nameclub').html($(this).html());
    	});
    	function getNumById(elem) {
            var str = elem;
            var start = str.indexOf("_") + 1;
            var end = str.length;
            var num = parseInt(str.substring(start, end));
            return num;
        }
    });
</script>