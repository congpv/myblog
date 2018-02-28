<div class="comment-list">
    <ul class="comments" id="comments_list">

        @foreach($comments as $comment)
            <li>
                <div class="comment-info">
                    <span class="comment-author">{{$comment->name}}</span>
                    <span class="comment-time">{{$comment->updated_at}}</span>
                </div>
                <div class="comment-text">{{$comment->comment_text}}</div>
            </li>
        @endforeach
    </ul>
</div>
{{ $comments->links() }}