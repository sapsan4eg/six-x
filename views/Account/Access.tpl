<?php echo Bundles::getValue('awesome');?>
<div class="panel panel-default">
    <div class="panel-heading title">
        <div class="row">
            <div class="col-md-9" id="panel_title">
                <?php echo _('permissions');?>
            </div>
            <div class="col-md-2 col-xs-2">
                <div class="dropdown">
                    <button id="dLabel" class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo _('text_name_controller');?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <?php foreach($controllers As $controller) { ?>
                        <li><a href="#" class="controller_button"><?php echo $controller;?></a></li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="col-md-1 col-xs-1">
                <button class="btn btn-primary" title="<?php echo _('save')?>"><i class="fa fa-save"></i></button>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li class="active"><a href="#controller_permissions" role="tab" data-toggle="tab"><?php echo _('controller');?></a></li>
            <li><a href="#action_permissions" role="tab" data-toggle="tab"><?php echo _('action');?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="controller_permissions" style="padding-top: 20px;">
                <div class="row">
                    <div class="col-md-10">
                        <h5 id="controller_permission_name"><?php echo _('permissions') . ' ' . _('controller');?></h5>
                        <div id="controller_labels"></div>
                    </div>
                    <div class="col-md-2">
                        <?php if(count($groups)) { ?>
                        <h5><?php echo _('groups');?></h5>
                        <ul class="ul-treefree ul-dropfree">
                            <?php foreach($groups As $group) {
                                if(($group['role_from'] - $pervios) > 2) {
                                    for($i = 1; $i <=  ($group['role_from'] - $pervios - 2); $i++) {
                                        echo '</li></ul>';
                                    }
                                }
                                if(($group['role_from'] - $pervios) == 1) {
                                    echo '<ul>';
                                }
                                if((($group['role_from'] - $pervios) == 2 || $to < $group['role_to']) && $pervios > 0) {
                                echo '</li>';
                                }

                                echo '<li><a href="#" class="groups">' . $group['role_name'] . '</a>';
                                $pervios = $group['role_from'];
                                if($to < $group['role_to']) {
                                    $to = $group['role_to'];
                                }
                            }
                             for($i = 1; $i <= ($to - $pervios - 1); $i++ ) {
                                echo '</li></ul>';
                             }?>
                            </li>
                        </ul>
                        <?php }?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="action_permissions" style="padding-top: 20px;">
                <div class="row">
                    <div class="col-md-10">
                        <h5 id="action_permission_name"><?php echo _('permissions') . ' ' . _('actions');?></h5>
                        <div id="action_labels"></div>
                    </div>
                    <div class="col-md-2">
                        <?php if(count($groups)) { ?>
                        <h5><?php echo _('groups');?></h5>
                        <ul class="ul-treefree ul-dropfree">
                            <?php foreach($groups As $group) {
                                if(($group['role_from'] - $pervios) > 2) {
                                    for($i = 1; $i <=  ($group['role_from'] - $pervios - 2); $i++) {
                                        echo '</li></ul>';
                                    }
                                }
                                if(($group['role_from'] - $pervios) == 1) {
                                    echo '<ul>';
                                }
                                if((($group['role_from'] - $pervios) == 2 || $to < $group['role_to']) && $pervios > 0) {
                                    echo '</li>';
                                }
                                echo '<li><a href="#" class="groups">' . $group['role_name'] . '</a>';
                                $pervios = $group['role_from'];
                                if($to < $group['role_to']) {
                                    $to = $group['role_to'];
                                }
                            }
                            for($i = 1; $i <= ($to - $pervios - 1); $i++ ) {
                                echo '</li></ul>';
                            }?>
                        </li>
                        </ul>
                <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        var panel_title = '<?php echo _('permissions');?>';
        var contr_title = '<?php echo _('permissions') . ' ' . _('controller');?>';
        var users = [];
        <?php foreach($users As $user){
            echo 'users['. $user["user_id"]. '] = "'. $user['logon_name'] .'";';
        }?>
        $('.panel-body').hide();
        $('.controller_button').click(function(e){
            e.preventDefault();
            $('.panel-body').hide('slow');
            $('#panel_title').html(panel_title + " " + $(this).html());
            $('#controller_permission_name').html(contr_title + " " + $(this).html());
            $('#action_permission_name').html("<?php echo _('permissions') . ' ' . _('actions') . ' ' . _('controller');?>" + " " + $(this).html());

            $.ajax({
                url: "<?php echo $getPermissions;?>",
                method: "POST",
                data: {controller_name: $(this).html()},
                dataType: "json",
                beforeSend: function(){
                    $('#myTab a[href="#controller_permissions"]').tab('show');
                    $(".ul-dropfree").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});
                    $('#controller_labels').html("");
                }
            }).done(function(json) {
                FillPanell(json);
            }).fail(function(e, text, er) {
                alert("Error: " + er);
            });
        });
        function FillPanell(data) {

            if(data.pemissions.controller != undefined) {
                fillSomthink(data.pemissions.controller, 0);
            }
            if(data.pemissions.actions != undefined) {
               // fillSomthink(data.pemissions.actions, 1);
            }
            $('.panel-body').show('slow');
        }
        function fillSomthink(data, to) {
            if(typeof data == "string") {
                appendPermission(0, data);
            } else if(typeof data == "object") {
                if(data.Roles != undefined) {
                    for(var a = 0; a < data.Roles.length; a++) {
                        appendPermission(1, data.Roles[a], to);
                    }
                }
                if(data.Users != undefined) {
                    for(var a = 0; a < data.Users.length; a++) {
                        appendPermission(2, users[data.Users[a]], to);
                    }
                }
            }
        }
        function appendPermission(type, name, to) {
            switch (to) {
                case 1:
                    iddiv = 'action_labels'
                        break
                default:
                    iddiv = 'controller_labels'
                        break;
            }
            switch (type){
                case 1:
                    typename = "info"
                    break
                case 2:
                    typename = "success"
                    break
                default:
                    typename = "default"
            }
            $('#' + iddiv).append('<span class="label label-' + typename + '">' + name + '</span> ');
        }
        $('.groups').click(function(e){
            e.preventDefault();
        });
        /// Tree of groups
        $(".ul-dropfree").find("li:has(ul)").prepend('<div class="drop"></div>');
        $(".ul-dropfree div.drop").click(function() {
            if ($(this).nextAll("ul").css('display')=='none') {
                $(this).nextAll("ul").slideDown(400);
                $(this).css({'background-position':"-11px 0"});
            } else {
                $(this).nextAll("ul").slideUp(400);
                $(this).css({'background-position':"0 0"});
            }
        });
        $(".ul-dropfree").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});
    });
</script>