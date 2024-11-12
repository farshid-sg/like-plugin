jQuery(document).ready(function($){
    let btnLike = $('.btn-like');
    if(btnLike.length){
        btnLike.click(function(){
            let postCount = $(this).find('.post-count');
            let postId = $(this).attr('data-id');
            $.ajax({
                url : app_script.site_url,
                type : "post",
                data : {
                    action : 'like_plugin',
                    postId
                },
                success : function(response){
                    console.log(response)
                    if(response.error){
                        alert(response.error);
                        return
                    }
                    if(response.liked){
                        btnLike.addClass('liked');
                    }else{
                        btnLike.removeClass('liked');
                    }
                    postCount.html(`(${response.postCount})`)
                },
                error : function(response){
                    alert('خطا در ارتباط با سرور رخ داده است');
                }
            })
        })
    }
})