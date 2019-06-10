
    @foreach ($article->replies as $reply)
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ $reply->nickname}} 回复于
                    {{ $reply->created_at->diffForHumans() }}
                </div>

                <div class="panel-body">
                    {{ $reply->content }}
                </div>
            </div>
        </div>
    @endforeach

