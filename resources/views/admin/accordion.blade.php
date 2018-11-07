<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
      <div id="accordion">
          @if (isset($qa_list))
            @foreach ($qa_list as $item)
              <div class="group">
                <h3>{{$item->question}}</h3>
                <div>
                  <p>{{$item->answer}}</p>
                </div>
              </div>
            @endforeach
          @endif
      </div>
    </div>
</div>
