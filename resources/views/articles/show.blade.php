@extends('layouts.app')
@section('title', $article->title)

@section('styles')
     <!--高亮代码引入-->
    <link href="https://cdn.bootcss.com/highlight.js/9.15.6/styles/a11y-dark.min.css" rel="stylesheet">
     <!--toastr提示框引入-->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
@stop

@section('content')

    @include('shared._messages')

    @if ($article->category->is_show == 1 && $article->is_show == 1)
        <div class="col-lg-9 col-md-9 article-content">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-center">
                        {{ $article->title }}
                    </h1>
                    <p class="article-time">{{ $article->created_at->toDateString() }}</p>
                    <hr>

                    <div class="article-body" id="content">
                        {!! Parsedown::instance()->text($article->body) !!}
                    </div>
                </div>
            </div>
            <div class="article-page">
                <ul class="pager">
                    @if ( ! is_null($previousArticleID) )
                        <li class="previous">
                            @if (!isset($category))
                                <a href="{{ route('articles.show', [$previousArticleID]) }}">
                            @else
                                <a href="{{ route('articles.showWithCategory', [$category->slug, $previousArticleID]) }}">
                            @endif
                                上一篇
                            </a>
                        </li>
                    @endif

                    @if ( ! is_null($nextArticleId) )
                        <li class="next">
                            @if (!isset($category))
                                <a href="{{ route('articles.show', [$nextArticleId]) }}">
                            @else
                                <a href="{{ route('articles.showWithCategory', [$category->slug, $nextArticleId]) }}">
                            @endif
                                下一篇
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- 评论 -->
            @include('articles.reply')

            <!-- 评论表单 -->

            <div class="panel panel-default">
                @include('shared._errors')
                <form method="post" action="{{ route('reply.store',  [$article->id]) }} ">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="form-group">
                            昵称 <input class="reply-nickname" type="text" name="nickname" value="{{ old('nickname') }}" required>
                            邮箱 <input class="reply-email" type="text" name="email" value="{{ old('email') }}" required>
                        </div>
                       <div class="form-group">
                            <textarea class="form-control reply-content" name="content" rows="4" required></textarea>
                       </div>
                        <button type="submit" class="btn btn-default">提交</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- 目录 -->
        <div class="col-xs-3 article-affix">
            <ul class="nav" data-spy="affix" data-offset-top="0" id="toc">

            </ul>
        </div>

        <!--遮罩层-->
        <div class="mask-layer" ></div>
        <!--放大图片-->
        <div class="large-pic" >
            <img id="pic" src="">
        </div>

    @else
        无数据
    @endif

@stop

@section('scripts')
    <!--复制js引入-->
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
    <!--高亮代码引入-->
    <script src="https://cdn.bootcss.com/highlight.js/9.15.6/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <!--toastr提示框引入-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js">

    </script>

    <script type="text/javascript">
        // 文章侧栏导航栏
        var toc = $("#toc")
        $(document).ready(function(){
            var h2_length = $("#content > h2").length;
            console.log(h2_length)
            $("#content > h2, #content > h3").each(function(i,item){
                // 添加 id 导航
                var num = i + 1;
                $(item).attr("id", "header_" + num);
                var tagName = $(item).prop("tagName");
                if (i == 0) {
                    // 显示文章侧栏导航栏
                    $(".article-affix").show();
                   // 默认选择第一个
                    if  (tagName == "H3" && h2_length != 0) {
                        //  增加下级样式
                        toc.append('<li class="active"><a class="subordinate" href="#header_'+num+'">' + $("#header_" + num).text() + '</a></li>');
                    } else {
                        toc.append('<li class="active"><a href="#header_'+num+'">' + $("#header_" + num).text() + '</a></li>');
                    }
                } else {
                    if  (tagName == "H3" && h2_length != 0) {
                        //  增加下级样式
                        toc.append('<li><a class="subordinate" href="#header_'+num+'">' + $("#header_" + num).text() + '</a></li>');
                    } else {
                        toc.append('<li><a href="#header_'+num+'">' + $("#header_" + num).text() + '</a></li>');
                    }
                }

            })

            // 选中
            $('ul li').click(function(){
                $(this).addClass('active').siblings().removeClass('active');
            })
        });

        // 图片点击放大
        $(".article-body > img, .article-body > p >img").click(function(){
            console.log(11111);
            $("#pic").attr('src', $(this).attr('src'));
            $(".mask-layer").show()
            $(".large-pic").show()
        });

        // 遮罩层隐藏 图片隐藏
        $(".mask-layer,.large-pic").click(function (){
            $(".mask-layer").hide()
            $(".large-pic").hide()
        })

        // 回复分页跳转
        $("ul.pagination>li>a").on("click", function(event) {
            // 禁止分页a标签跳转
            event.preventDefault();
            var href = $(this).attr('href');
            window.location.href = href+"#reply";
        });

        // 代码段加复制按钮
        var clipboard = new ClipboardJS('.copy-btn');
        clipboard.on('success', function(e) {
            toastr.success('已经复制到粘贴板');
        })
        // 提示框配置
        toastr.options = {
            closeButton: false,
            debug: false,
            progressBar: true,
            positionClass: "toast-top-center",
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "2000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };
        $(document).ready(function(){
            $("pre").each(function(i,item){
                $(item).children('code').attr("id", "code_" + i);
                // 添加复制按钮
                $(item).prepend('<button class="copy-btn" data-clipboard-target="#code_'+i+'"><span class="glyphicon glyphicon-file"></span></button>');

                // 移到代码段显示复制按钮
                $(item).hover(function(){
                    $(item).children('button').css("opacity","1");
                    },function(){
                    $(item).children('button').css("opacity","0");
                });
            })
        })




    </script>

@stop