@extends('master')
 
@section('title') Groups - indalist @stop

@section('content')

<div class="row main">
  <div class="columns medium-10 small-centered">
    <div class="group row collapse">
    	<div>
	      Create a group
	    </div>
      <div class="columns medium-10">
        <input type="text" id="group-create" class="error">
        <span class="group-status"></span>
      </div>
      <div class="columns medium-2">
      	<div class="postfix button disabled group-create">Create</div>
      </div>

    </div>
	  <div class="row collapse">
	    @foreach($groups as $group)
	    	<a href="{{$group->slug}}"><div class="list row">{{$group->name}}</div></a>
	    @endforeach
	  </div>

  </div>
</div>

@stop

@section('scripts')

<script type="text/javascript">
$(function() {
	$("#group-create").on('keyup',function(e){
		$.get('{{url("group")}}?name='+$(this).val(), function(data){
			console.log(data);

			$(".group-status").removeClass("error");
			$(".group-create").addClass("disabled");
			$(".group-status").html("");

			if(data == 'disabled') {
				$(".group-create").addClass("disabled");
			}
			else if ( data == 'Already taken') {
				$(".group-status").addClass("error").html(data);

			} else {
				$(".group-create").removeClass("disabled").on('click', function() {
					createGroup($("#group-create").val())
				});
				if(e.keyCode == 13) {
            createGroup($("#group-create").val());
        }
				$(".group-status").html("");
			}

		})
	})
})
function createGroup(val) {
	$.get('{{url("group")}}?create=1&name='+val, function(r) {
						// console.log(r)
						if(r.success == 'ok') {
							window.location.href = '{{url()}}/'+r.slug
						} else {
							$(".group-status").addClass("error").html(r);
						}
					})
}
</script>

@stop