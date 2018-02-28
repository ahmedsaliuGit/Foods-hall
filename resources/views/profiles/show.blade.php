@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div  class="page-header">

                <avatar-form :user="{{ $profileUser }}"></avatar-form>
                
            </div>
            
            @forelse($activities as $date => $activity)
                <h1 class="page-header">{{ $date }}</h1>
                @foreach($activity as $occur_activity)
                    @if(view()->exists("profiles.activities.{$occur_activity->type}"))
                        @include ("profiles.activities.{$occur_activity->type}", ['activity' => $occur_activity])
                    @endif
                @endforeach
            @empty
                <p>There is no activity for this user now.</p>
            @endforelse

        </div>
    </div>
</div>
@endsection
