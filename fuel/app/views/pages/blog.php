<div id="blog" class="<?php echo (empty($blog_id)) ? 'toc' : 'no-toc'; ?>">
<?php 
if (!empty($description)) { 
    echo $description;
}
?>
   
<div id="blog-list">
<?php if (!empty($blogs)) { ?>
    <?php foreach ($blogs as $blog) { 
    $editable = $can_edit_any || ($can_edit_own && ($user_id == $blog['user_id']) && $is_confirmed);
    $deletable = $can_delete_any || ($can_delete_own && ($user_id == $blog['user_id']) && $is_confirmed);
    $can_make_public = $can_make_any_public || ($can_make_own_public && ($user_id == $blog['user_id']) && $is_confirmed);
    ?>
    <div id="blog-post-<?php echo $blog['id']; ?>" class="blog-post" user_id="<?php echo $blog['user_id']; ?>" public="<?php echo $blog['public_flag'] ? 1 : 0; ?>" publish="<?php echo $blog['publish_flag'] ? 1 : 0; ?>">       
        <?php if (empty($blog_id)) { ?>
            <a href="<?php echo Uri::create('blog/view/' . $blog['id']); ?>"><div class="title"><?php echo $blog['title']; ?></div></a>
        <?php } else { ?>
            <div class="title"><?php echo $blog['title']; ?></div>
        <?php } ?>
        <div class="author">Posted on <?php echo $blog['updated_at']; ?> 
            by <a class="name" href="<?php echo \Uri::create('/member/view/' . $blog['user_id']); ?>"><?php echo $blog['author']; ?></a>
        </div>
        <div class="post"><?php echo $blog['post']; ?></div>
        
        <?php if (!empty($editable)) { ?>
            <div class="status">Is Published: <b class="publish-status"><?php echo $blog['publish_flag'] ? 'Yes' : 'No'; ?></b></div>           
            <?php if (!empty($can_make_public) && empty($force_public)) { ?>
            <div class="status">Is Public: <b class="public-status"><?php echo $blog['public_flag'] ? 'Yes' : 'No'; ?></b></div>
            <?php } ?>
        <?php } ?>
            
        <div class="buttons">
            <?php if (!empty($editable)) { ?>
            <a href="javascript:void(0);" class="edit-blog-button button" blog_id="<?php echo $blog['id']; ?>">Edit</a>
            <?php } ?>
            <?php if (!empty($deletable)) { ?>
            <a href="javascript:void(0);" class="delete-blog-button button" blog_id="<?php echo $blog['id']; ?>">Delete</a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
<?php } ?>   
</div>
</div>

<?php if ($include_edit_form) { ?>
<?php if (!empty($addable)) { ?>
<input id="add-blog-button" type="button" class="button" value="Add Blog Post +" />
<?php } ?>  
<form blog_id="" id="blog-form" style="display: none;" title="Add/Edit Blog Post">
    <input type="hidden" id="blog-form-user_id" name="user_id" value="" />
    <label for="blog-form-title">Title:</label><br/>
    <input type="text" class="text" id="blog-form-title" name="title" value="" /><br/>
    <label for="blog-form-post">Info:</label><br/>
    <textarea id="blog-form-post" name="post"></textarea><br/>
    <div class="checkboxes">
        <div><input type="checkbox" id="blog-form-publish" name="publish_flag" value="1" /> <label for="blog-form-publish">Publish</label></div>
        <?php if ($force_public) { ?>
        <input type="hidden" name="public_flag" value="1" />
        <?php } else { ?>
        <div id="blog-form-public-field"><input type="checkbox" id="blog-form-public" name="public_flag" value="1" /> <label for="blog-form-public">Make public</label></div>
        <?php } ?>
    </div>
</form>

<script type="text/javascript">
    $(function(){   
        var data_id, user_id;
        
        var addPost = function(data) {
            var editable = <?php echo $can_edit_any ? 'true' : 'false'; ?> || (<?php echo $can_edit_own && $is_confirmed ? 'true' : 'false'; ?> && (data.user_id == '<?php echo $user_id; ?>'));
            var deletable = <?php echo $can_delete_any ? 'true' : 'false'; ?> || (<?php echo $can_delete_own && $is_confirmed ? 'true' : 'false'; ?> && (data.user_id == '<?php echo $user_id; ?>'));
            var can_make_public = <?php echo $can_make_any_public ? 'true' : 'false'; ?> || (<?php echo $can_make_own_public && $is_confirmed ? 'true' : 'false'; ?> && (data.user_id == '<?php echo $user_id; ?>'));

            var public_status = data.public_flag ? 'Yes' : 'No';
            var publish_status = data.publish_flag ? 'Yes' : 'No';
            var html = '<div id="blog-post-' + data.id + '" class="blog-post" user_id="' + data.user_id + '" public="' + data.public_flag + '" publish="' + data.publish_flag + '">'
                <?php if (empty($blog_id)) { ?>
                + '<a href="<?php echo Uri::create('blog/view/'); ?>' + data.id + '"><div class="title">' + data.title + '</div></a>'
                <?php } else { ?>
                + '<div class="title">' + data.title + '</div>'
                <?php } ?>
                + '<div class="author">Posted on ' + data.updated_at + ' by <a class="name" href="<?php echo \Uri::create('/member/view/' . $current_user->id); ?>"><?php echo $current_user->username; ?></a></div>'
                + '<div class="post">' + data.post + '</div>';
            
            if (editable) {
                html += '<div class="status">Is Published: <b class="publish-status">' + publish_status + '</b></div>';

                if (can_make_public) {
                    html += '<div class="status">Is Public: <b class="public-status">' + public_status + '</b></div>';
                }
            }
            
            html += '<div class="buttons">';
            
            if (editable) {
                html += '<a href="javascript:void(0);" class="edit-blog-button button" blog_id="' + data.id + '">Edit</a> ';
            }
            
            if (deletable) {
                html += '<a href="javascript:void(0);" class="delete-blog-button button" blog_id="' + data.id + '">Delete</a>';
            }

            html += '</div></div>';
            
            $('#blog-list').prepend(html);
        };
        
        var updatePost = function(data) {
            var id = data.id;
            var publish_status = data.publish_flag ? 'Yes' : 'No';
            var public_status = data.public_flag ? 'Yes' : 'No';
            
            $('#blog-post-' + id + ' .title').html(data.title);
            $('#blog-post-' + id + ' .post').html(data.post);
            $('#blog-post-' + id + ' .publish-status').text(publish_status);
            $('#blog-post-' + id + ' .public-status').text(public_status);
            $('#blog-post-' + id).attr('publish', data.publish_flag);
            $('#blog-post-' + id).attr('public', data.public_flag);
        };
        
        var deletePost = function(id) {
            $('#blog-post-' + id).remove();
        };
        
        var enableFormControls = function(user_id) {
            var can_make_public = <?php echo $can_make_any_public ? 'true' : 'false'; ?> || (<?php echo $can_make_own_public && $is_confirmed ? 'true' : 'false'; ?> && (user_id == '<?php echo $user_id; ?>'));
            
            if (can_make_public) {
                $('#blog-form-public-field').show();
            }
            else {
                $('#blog-form-public-field').hide();
            }
        };
                      
        //blog add/edit form validation
        $("#blog-form").validate({
            ignore: "",
            rules: {
                title: {
                    required: true
                },
                post: {
                    required: true
                }
            }
        });
        
        //blog add/edit popup dialog
        $("#blog-form").dialog({
            autoOpen: false,
            width: 550,
            height: 550,
            modal: true,
            show: {
                effect: "fade",
                duration: 1000
            },
            resizable: false,
            buttons: {
                "Save": function() {
                    if ($(this).valid()) {
                        $( this ).submit();
                    }
                },
                "Cancel": function() {
                    $(this).dialog('close');
                }
            }
        });
        
        //blog add/edit form submit handler
        $('#blog-form').on('submit', function(e){
            var id = $("#blog-form").attr('blog_id');
            var post = $("#blog-form-post").html().replace(/^\s+|\s+$/g,'');
            e.preventDefault(); // this will prevent form from actually submitting.
            
            $("#blog-form-post").html(post);
            
            $.post('<?php echo Uri::create('member/ajax/blog/edit/'); ?>' + id, $(this).serialize(), function(res){
                if (res.status) {
                    if (id && id.length) { //if we just edited a post
                        updatePost(res.data);
                    }
                    else { //if we just added a post
                        addPost(res.data);
                    }
                }
                else {
                    //error occurred
                    flashErrorMsg(res.error || 'Unknown error', true/*clear previous error*/);
                }
            }, "json");                                                        

            $("#blog-form").dialog('close');
        });
        
        //add blog button click event
        $("#add-blog-button").click(function() {
            var user_id = '<?php echo $user_id; ?>';
            
            $("#blog-form").attr('blog_id', '');
            $("#blog-form-user_id").val(user_id);
            $("#blog-form-title").val('');
            $("#blog-form-post").val('');
            $("#blog-form-publish").prop('checked', false);
            $("#blog-form-public").prop('checked', false);
            enableFormControls(user_id);
            $("#blog-form").dialog("option", "title", "Add Blog Post");
            $("#blog-form").dialog( "open" );
        });
        
        //edit blog button click event
        $('#blog-list').on('click', '.edit-blog-button', function(event){
            var id = $(event.target).attr('blog_id');
            var elem = $('#blog-post-' + id);
            var user_id = elem.attr('user_id');
            
            $("#blog-form").attr('blog_id', id);
            $("#blog-form-user_id").val(user_id);
            $("#blog-form-title").val($('#blog-post-' + id + ' .title').text());
            $("#blog-form-post").val($('#blog-post-' + id + ' .post').html());
            $("#blog-form-publish").prop('checked', parseInt(elem.attr('publish')) ? true : false);
            $("#blog-form-public").prop('checked', parseInt(elem.attr('public')) ? true : false);
            enableFormControls(user_id);
            $("#blog-form").dialog("option", "title", "Edit Blog Post");
            $("#blog-form").dialog( "open" );
        });      
        
        //delete blog button click event
        $('#blog-list').on('click', '.delete-blog-button', function(event){
            var id = $(event.target).attr('blog_id');

            if (confirm("Are you sure you want to delete this blog?")) {
                $.post('<?php echo Uri::create('member/ajax/blog/delete'); ?>', {id: id}, function(res){
                    if (res.status) {
                        deletePost(id);
                    }
                    else {
                        //error occurred
                        flashErrorMsg(res.error || 'Unknown error', true/*clear previous error*/);
                    }
                }, "json"); 
            }
        });               
    });
</script>
<?php } ?>
