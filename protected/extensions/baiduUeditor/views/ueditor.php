<script type="text/plain" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>" style="width:<?php echo $this->width;?>;height:<?php echo $this->height;?>;"><?php echo $this->content; ?></script>

<script>
    $(function(){
        var <?php echo $this->id?> =UE.getEditor('<?php echo $this->id?>',<?php echo json_encode($this->config);?>
        );

        <?php echo $this->id?>.ready(function() {
            <?php echo $this->id?>.execCommand('serverparam', {
                '<?php echo  session_name();?>': '<?php echo session_id(); ?>'
            });
        });
    });
</script>