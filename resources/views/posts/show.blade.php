@extends('layouts.app')

@section('title', '| View Post')

@section('content')

<div class="container">
    
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading panel-post-detail">
                    <div class="post-title">{{$post->title}}</div>
                    <div class="post-info">                        
                        <div class="post-created">{{$post->updated_at}}</div>

                        <div class="post-like">
                            @if($like && $like->like == 1)
                                <span class="article-like-up active" title="{{$numlike}}"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
                                <span class="article-like-down disabled" title="{{$numdislike}}"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
                            @elseif($like && $like->like == 0)
                                <span class="article-like-up disabled" title="{{$numlike}}"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
                                <span class="article-like-down active" title="{{$numdislike}}"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
                            @else
                                <span class="article-like-up"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
                                <span class="article-like-down"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
                            @endif
                            
                                
                        </div>
                        </div>
                        <div class="post-action">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['posts.destroy', $post->id] ]) !!}
                            @can('Edit Post')
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-info" role="button">Edit</a>
                            @endcan
                            @can('Delete Post')
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            @endcan
                            {!! Form::close() !!}
                        </div>
                    
                    </div>
                
                <div class="panel-body">
                    <div class="post-detail">                      
                        <div class="article-fullcontent">{{$post->body}}</div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">    
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Comment</div>
                <div class="panel-body">
                    @if (count($comments) > 0)
                    <section class="sec-comments">
                        @include('posts.commentlist')
                    </section>
                    @endif
                    {!! Form::open(array('route' => 'posts.storecomment','method'=>'POST', 'id'=>'comment_form')) !!}
                        @include('posts.commentform')
                    {!! Form::close() !!}
                    
                    <input type="hidden" value="{{$post->id}}" id="post_id">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="{{ asset('js/jquery.js') }}"></script>
<script>
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')}
        });
        $("#btn-comment").click(function(e) {
            var comment = $("#comment-text").val();
            var post_id = $("#post_id").val(); 
            var data = {'comment':$("#comment").val(), 'post_id':$("#post_id").val()}; 
            $.ajax({
              url: '{{ URL::to("posts/storecomment")}}',
              type: "POST",
              data:  {data, '_token': $('input[name=_token]').val()},
              success: function(res){
                if(res.code === 200) {
                    $("#comment").val('');
                    $(".sec-comments").html(res.data);
                }
                
              }
            });      
        });
        $(".article-like-up").click(function(e) {
            if($(".post-like").find(".active").length <= 0) {
                storelike(1);
            }
        });
        $(".article-like-down").click(function(e) {
            if($(".post-like").find(".active").length <= 0) {
                storelike(0);
            }
        });
        function storelike(like) {
            var data = {'like':like, 'post_id':$("#post_id").val()}; 
            $.ajax({
              url: '{{ URL::to("posts/storelike")}}',
              type: "POST",
              data:  {data, '_token': $('input[name=_token]').val()},
              success: function(res){
                console.log(res);
                if(res.code === 200) {
                    switch(res.data) {
                        case 1:
                            $(".article-like-up").addClass("active");
                            $(".article-like-down").addClass("disabled");
                            break;
                        case 0:
                            $(".article-like-up").addClass("disabled");
                            $(".article-like-down").addClass("active");
                            break;
                        default:
                        break;
                    }
                }
                
              }
            });  
        }
    });
        
</script>