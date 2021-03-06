@extends('layouts.app')

@section('header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create Thread</div>

                <div class="panel-body">
                    <form method="POST" action="{{ route('storeThread') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="title">Channel</label>
                            <select name="channel_id" id="channel_id" class="form-control" required>
                                <option value="">Choose a channel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{$channel->id}}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{$channel->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" id="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="body">Discussion</label>
                            <textarea rows="8" class="form-control" name="body" id="body" required>{{ old('body') }}</textarea>
                        </div>

                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6LcLG0EUAAAAAE-Fl1bbqby-KIVhrwDBFpquCYqj"></div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">Submit</button>
                        </div>
                    </form>
                    @if (count($errors))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection