@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create a New Thread</div>

                <div class="panel-body">
                    <form method="POST" action="/threads">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="channel">Choose Channel:</label>
                            <select name="channel_id" id="channel" class="form-control">
                                <option value="">Choose one...</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
                        </div>

                        <div class="form-group">
                            <label>Boby:</label>
                            <textarea name="body" id="body" rows="8" class="form-control">{{ old('body') }}</textarea>
                        </div>

                        <input type="submit" name="Publish" class="btn btn-primary">
                        

                    </form>

                    @if (count($errors))
                        <ul class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul> 
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
