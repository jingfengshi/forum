@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('threads._list')
                {{$threads->render()}}
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">

                            Trending Threads


                    </div>
                    @if(count($trending))
                        <div class="card-body">
                            @foreach($trending as $thread)
                                <li>
                                    <a href="{{url($thread->path)}}">
                                        {{$thread->title}}
                                    </a>

                                </li>
                            @endforeach
                        </div>
                    @endif
                </div>


            </div>

        </div>

    </div>
@endsection
