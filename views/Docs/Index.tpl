<?php echo Bundles::getValue('awesome');?>
<?php foreach($groups_docs As $group_doc) { ?>
<div class="panel panel-default" style="margin-top:20px;">
    <div class="panel-heading title">
        <div class="row">
            <div class="col-md-9">
                <h4><?php echo $group_doc["group"]["docs_group_title"];?></h4>
            </div>
            <div class="col-md-3 col-xs-3">
                <?php if( ! empty($group_doc["docs"])){ ?>
                <a href="<?php echo $ziplink . (strpos($ziplink, '?') === FALSE ? '?' : '&') . 'zip=' . $group_doc['group']['docs_group_id'];?>" class="btn btn-info pull-right" id="downloadzip" title="<?php echo _('button_download_zip');?>"><i class="fa fa-file-zip-o"></i></a>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <form action="" method="POST">
        </form>
        <?php if(isset($group_doc["docs"])){ foreach($group_doc["docs"] As $docs) { ?>
        <div class="row" style="padding: 10px;">
            <div class="col-md-9 col-xs-9">
                <a href="<?php echo $filelink . (strpos($filelink, '?') === FALSE ? '?' : '&') . 'file=' . $docs['doc_path'] . '&group=' . $group_doc['group']['docs_group_id'];?>"><?php echo $docs["doc_title"];?></a>
            </div>
            <div class="col-md-3 col-xs-3">
                <?php echo$docs["date"];?>
            </div>
        </div>
        <?php } }?>
        <div class="row">
            <div class="col-md-2 col-xs-2 col-md-offset-10" style="font-size: 10px;">
                <?php echo _("date_create") . ": " . $group_doc["group"]["date_create"];?>
            </div>
        </div>
    </div>
</div>
<?php }?>
<div class="row" style="padding: 10px;">
    <div class="col-md-3 col-xs-3 col-md-offset-9">
        <div class="btn-group" role="group" aria-label="pages">
            <?php if($pages['current'] > 0) { ?>
            <button type="button" id="page_<?php echo $pages['current'] - 1;?>" class="btn btn-default page"><?php echo _('button_back')?></button>
            <?php }
            for ($i = 0; $i < $pages['count'] / $pages['step']; $i++)  {
                $active = "";
                if ($i == $pages['current'])
                {
                    $active = "active";
                }
                if ($i <= $pages['current'] && ($pages['current'] - $i) <= 2 || $i >= $pages['current'] && ($i - $pages['current']) <= 2 || $pages['current'] == 0 && $i < 5 || $pages['current'] == 1 && $i < 5 || ($pages['current'] == ($pages['count'] / $pages['step'] - 1) && $i >= $pages['count'] / $pages['step'] - 5) || ($pages['current'] == ($pages['count'] / $pages['step'] - 2) && $i >= $pages['count'] / $pages['step'] - 5))
                { ?>
                <button type="button" id="page_<?php echo $i;?>" class="btn btn-default <?php echo $active;?> page"><?php echo $i + 1; ?></button>
                <?php
                }
             }
            if ($pages['count'] / $pages['step'] - $pages['current'] - 1 > 0) { ?>
            <button type="button" id="page_<?php echo $pages['current'] + 1;?>" class="btn btn-default page"><?php echo _('button_forward')?></button>
            <?php }?>
        </div>
    </div>
</div>
<script>
$(function(){
    $(".page").click(function () {
        window.location = "<?php echo $pages['link'] . (strpos($pages['link'], '?') === FALSE ? '?' : '&') . 'page=';?>" + getNumById($(this).attr("id"));
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