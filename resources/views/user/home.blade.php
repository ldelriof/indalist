<?php $ids = '' ?>
@extends('user.master')
 
@section('content')
<div class="panel  columns small-10 small-centered" >
	<h2>Hi {{$user->name}}</h2>
	@if(!$library)
    <div class="group row collapse">
        <div>
        	Choose a main list name
        </div>
      <div class="columns medium-12 large-8">
        <input type="text" id="group-create" class="error">
        <span class="group-status"></span>
      </div>
      <div class="columns medium-12 large-4">
        <div class="postfix button disabled group-create secondary">Create</div>
      </div>
      <small>
        The main list is where all the videos you add from other lists will be dropped, 
        later you can drop them in other lists you have created
      </small>
    </div>
    @else 

<div class="row" data-equalizer>
    <div class="columns list-title medium-6" data-equalizer-watch>
        @foreach($lists as $list)
        <?php $ids .= '#'.$list->slug.', ' ?>

        @if($list->private == 2)

        <div class="columns list-title">
            <a href="{{url($list->slug)}}">{{$list->name}}</a>
            <small>(main list, not public)</small>
        </div>
        <div id="{{$list->slug}}" class="row list-sortable collapse main-list" data-id="{{$list->id}}">
            <!-- <div class="columns"> -->
            @foreach($list->videos() as $video)
            <div class="list row collapse icons" data-id="{{ $video->id }}">
            <i class="fa fa-sort left"></i>
            <i class="order">{{$video->order}}</i>
            @if($video->active)
                <!-- <i class="listen fa-volume-on fa"></i> -->
            @else
                <!-- <i class="listen fa-volume-off fa"></i> -->
            @endif

                {{$video->name}}
            <i class="fa fa-close right delete"></i>
            <i class="fa-spin fa fa-circle-o-notch add-load right"></i>
            </div>
            @endforeach
            <!-- </div> -->
        </div>



        @endif
        @endforeach
    </div>


    <div class="columns list-title medium-6" data-equalizer-watch>
        @foreach($lists as $list)
        <?php $ids .= '#'.$list->slug.', ' ?>

        @if($list->private != 2)

        <div class="columns list-title">
            <a href="{{url($list->slug)}}">{{$list->name}}</a>
            @if($list->private == 1)
                <small>(non-collaborative list)</small>
            @endif
        </div>
        <div id="{{$list->slug}}" class="row list-sortable collapse" data-id="{{$list->id}}">
            <!-- <div class="columns"> -->
            @foreach($list->videos() as $video)
            <div class="list row collapse icons" data-id="{{ $video->id }}">
            <i class="fa fa-sort left"></i>
            <i class="order">{{$video->order}}</i>
            @if($video->active)
                <!-- <i class="listen fa-volume-on fa"></i> -->
            @else
                <!-- <i class="listen fa-volume-off fa"></i> -->
            @endif

                {{$video->name}}
            <i class="fa fa-close right delete"></i>
            <i class="fa-spin fa fa-circle-o-notch add-load right"></i>
            </div>
            @endforeach
            <!-- </div> -->
        </div>



        @endif
        @endforeach
    </div>

</div>
    @endif

    <div class="clearfix"></div>
	<a href="{{url('auth/logout')}}" class="button secondary right">Log out</a>
	<div class="clearfix"></div>
</div>
@stop

@section('scripts')
<script type="text/javascript">

$(function() {
    $(".delete").on('click', function() {
        $(this).hide();
        $(this).next().show();
        line = $(this).parent();
        id = line.attr('data-id');
        console.log(id)
        url = '{{url()}}/video/'+id
        $.ajax({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            method: 'DELETE',
            url: url, 
            success: function(e) {
                if(e == 'ok') {
                    line.fadeOut();
                }
                console.log(e);
            }
        })

    })
    $( "{{$ids}} #none" ).sortable({
      connectWith: ".list-sortable",
      receive : function(e, ui) {
        // console.log(ui.item.attr('data-id'));
        // console.log($(e.target).attr('data-id'));
        group_id = $(e.target).attr('data-id')
        video_id = ui.item.attr('data-id')
        $.get('{{url('change-list')}}/'+video_id+'?group='+group_id, function(e) {
            console.log(e)
        })
      }
    }).disableSelection();

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
$.get('{{url("group")}}?create=1&user_id={{$user->id}}&private=2&name='+val, function(r) {
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

@stop