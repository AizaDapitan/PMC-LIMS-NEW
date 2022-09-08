@extends('layouts.app')
@section('content')
<edit_analyst-component :worksheet = "{{ $worksheet }}"></edit_analyst-component>
@endsection
@section('pagejs')
<script>
      var dateFormat = 'mm/dd/yy';   	

      let dateAnalyzedItems = [
        "#date-shift-assayed",
        "#date-shift-weighed1",
        "#date-analyzed1",
        "#date-analyzed2",
      ];

      dateAnalyzedItems.forEach((item) => {
        $(item).datepicker({
          dateFormat: dateFormat,
          defaultDate: "+1w"
        });
        $(item).datepicker("setDate", $(item).attr("data-date-value"));
      })
      
    </script>
@endsection