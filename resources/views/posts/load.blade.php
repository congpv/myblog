@foreach($posts as $post)
    <div class="post-block">
        <div class="post-title">{{ $post->title }}</div>
        <div class="post-teaser"> {{  str_limit($post->body, 200) }} {{-- Limit teaser to 200 characters --}}</div>
        <div class="post-more">
            <a class="view-more" href="{{ route('posts.show', $post->id ) }}">view more detail</a>
        </div> 
    </div>
@endforeach
<div class="paginator-list">{{ $posts->links() }}</div>
