@component('profiles.activities.activity')
    @slot('heading')
        {{$profileUser->name}} published a thread
    @endslot

    @slot('body')
        {{$activity->subject->body}}
    @endslot
@endcomponent




