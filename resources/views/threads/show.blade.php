@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">

    <script>
        window.thread = <?= json_encode($thread)  ?>
    </script>
@endsection

@section('content')
    <thread-view  :thread="{{$thread}}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="level">

                                <img src="{{$thread->creator->avatar_path}}" alt="{{$thread->creator->name}}" width="25" height="25" class="mr-1">

                            <span class="flex">
                                  
                                  <a href="{{route('profile',$thread->creator->name)}}">{{$thread->creator->name}}({{$thread->creator->reputation}})</a>
                        posted:
                                {{$thread->title}}
                            </span>

                                @can('update',$thread)
                                    <span>
                                <form action="{{$thread->path()}}" method="post">
                                    {{csrf_field()}}
                                    {{method_field('DELETE')}}
                                    <button type="submit" class="btn btn-link">Delete</button>
                                </form>
                            </span>
                                @endif
                            </div>

                        </div>

                        <div class="card-body">
                            {{$thread->body}}
                        </div>
                    </div>

                    <replies  @remove="repliesCount--" @add="repliesCount++"></replies>

                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <p>
                                This thread was published {{$thread->created_at->diffForHumans()}} by
                                <a href="#">{{$thread->creator->name}}</a>,
                                and currently has <span v-text="repliesCount"></span> {{str_plural('comment',$thread->replies_count)}}.

                            </p>
                            <p>
                               <dingyue :active="{{json_encode($thread->isSubscribedTo)}}" v-if="signedIn"></dingyue>

                                <button class="btn btn-primary" v-if="authorize('isAdmin')" @click="toggleLock" v-text="locked ? 'Unlock' : 'Lock'"></button>
                            </p>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </thread-view>














@endsection
