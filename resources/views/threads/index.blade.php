@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            
            @include('threads._list')

            {{ $threads->render() }}
            
        </div>

        <div class="col-md-4">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    Trending Threads
                </div>

                <div class="panel-body">
                    <ul class="list-group">
                        @forelse( $trending as $thread)
                            <li class="list-group-item">
                                <a href="{{ url($thread->path) }}">{{ $thread->title }}</a>
                            </li>
                        @empty
                            <li class="list-group-item">Nothing is Trendings at the moment</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection