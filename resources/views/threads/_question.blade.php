{{--  Editing  --}}
<div class="panel panel-default" v-if="editing">
    <div class="panel-heading">
        
        <div class="level">
            <input class="form-control" v-model="form.title">
        </div>
    </div>

    <div class="panel-body">
        <div class="body">
            <div class="form-group">
                <textarea class="form-control" rows="5" v-model="form.body"></textarea>
            </div>
        </div>
    </div>

    <div class="panel-footer">
        <div class="level">
            <button class="btn btn-xs level-item" @click="editing = true" v-show="! editing">Edit</button>
            <button class="btn btn-xs level-item" @click="resetForm">Cancel</button>
            <button class="btn btn-xs level-item btn-primary" @click="update()">Update</button>

            @can ('delete', $thread)
                <form action="{{ url($thread->path() ) }}" method="POST" class="ml-a">
                    {{ csrf_field() }} {{ method_field('DELETE') }}
                    <button class="btn btn-link" type="submit">Delete Thread</button>
                </form>
            @endcan
        </div>
    </div>
</div>

{{--  Viewing  --}}
<div class="panel panel-default" v-else>
    <div class="panel-heading">
        
        <div class="level">
            <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" class="mr-1" />

            <span class="flex">
                <a href="{{ route('profileUser', [$thread->creator->name]) }}">{{ $thread->creator->name }}</a> 
                posted: <span v-text="title"></span>
            </span>
            
        </div>
    </div>

    <div class="panel-body">
        <div class="body" v-text="body"></div>
    </div>

    <div class="panel-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-xs" @click="editing = true">Edit</button>
    </div>
</div>