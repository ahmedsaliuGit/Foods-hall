<reply :attributes="{{ $reply }}" inline-template v-cloak>
    <div  id="#reply-{{ $reply->id }}" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a href="{{ route('profileUser', [$reply->owner->name]) }}">{{ $reply->owner->name }}</a> 
                    replied {{ $reply->created_at->diffForHumans() }}
                </h5>
                @if (Auth::check())
                    <favorite :reply="{{ $reply }}"></favorite>
                @endif
            </div>
        </div>

        <div class="panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea rows="8" class="form-control" v-model="body"></textarea>
                </div>
                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>

        @can('update', $reply)
            <div class="panel-footer level">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>

                <form action="{{ url('replies/'.$reply->id) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <button type="submit" class="btn btn-danger btn-xs" style="text-align: right;">Delete</button>
                </form>
            </div>
        @endcan
    </div>
</reply>