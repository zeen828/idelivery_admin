
    <div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

        <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

        <div class="{{$viewClass['field']}}">

            @include('admin::form.error')

            <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" />

            <textarea class="form-control summernote" id="{{$id}}" name="{{$name}}" placeholder="{{ trans('admin::lang.input') }} {{$label}}" {!! $attributes !!} >{{ old($column, $value) }}</textarea>
        </div>
    </div>
