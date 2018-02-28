@extends('layouts.app')
@section('content')
    <div class="container">
       
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                

                <div class="panel-body">
                   
                    
                    @if (count($posts) > 0)
                    <section class="posts">
                        @include('posts.load')
                    </section>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection