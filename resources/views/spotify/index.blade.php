@extends('master')
 
@section('title') upQueue @stop

@section('content')
<div class="row main">
    <div class="columns medium-10 small-centered">
        <div class="response">Paste a YouTube url to add it to the queue</div> 
        <input class="url" type="text" placeholder="youtube url">
        <div class="button add small-12">Add to queue</div>

        <div id="player"></div>
        <div id="curr_title"></div>
        <br>
        <div id="list_tits"></div>
    </div>
</div>

@stop

@section('scripts')



<script type="text/javascript">

$(function() {
    $(".add").on('click',setVideo);
    $(".url").on('keyup',function(e) {
        if(e.keyCode == 13) {
            setVideo();
        }
    });

    $("#list_tits").on('click', '.up', function(e){
        var id = $(this).parent().attr('id');
        $.get('{{url("video")}}/'+id+'/update?active=1&voteup=1', function(e) {
            getList();
        });
    })

    $("#list_tits").on('click', '.down', function(e){
        var id = $(this).parent().attr('id');
        $.get('{{url("video")}}/'+id+'/update?active=1&votedown=1', function() {
            getList();
        });

    })
})

function setVideo() {
    $(".response").text('Reading url');
    var id = getId($('.url').val());
    console.log(id);
        if(id != undefined) {
            $.get('{{url("video")}}/'+id, function(data){
                $(".response").text(data);
                setTimeout(function(){
                    $(".response").animate({opacity : 0})
                },5000);
            });
        } else {
            $(".response").text('Url not recognized');
            setTimeout(function(){
                $(".response").animate({opacity : 0})
            },5000);
        }
        $(".url").val("")
        $(".response").animate({opacity : 1})
}
function getId(pastedData) {
    if(pastedData.match(/youtu|vimeo/gi)) {
        var you = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        
        address = pastedData.replace(you, '$1')
            .replace(/(\r\n|\n|\r)/gm,"");  
        return address;
    }
}
// create youtube player
var player;    
function onYouTubePlayerAPIReady() {
    var video = 'L-6LXhFNeGw'
    player = new YT.Player('player', {
      height: '400',
      width: '100%',
      videoId: video,
      events: {
        'onReady': queueVideo,
        'onStateChange': onPlayerStateChange
      }
    });
    
}
function onPlayerStateChange(event) {        
    if(event.data === 0) {            
        queueVideo(event)
    }
}
var video_played = false;
function queueVideo(event) {
    if(video_played) {
        $.get('{{url("video")}}/'+video_played+'/update?active=0', function() {
            play(event);
        });
    } else{
        play(event);
    }
}
function play(event) {

     $.get('{{url("videos?take=1")}}', function(data) {
                    var video = data[0] ? data[0].video : 'L-6LXhFNeGw'
                    var name = data[0] ? data[0].name : ''
                    video_played = data[0] ? data[0].id : false;
                    // console.log(data[0].video);
                    player.cueVideoById({videoId:video});
                    event.target.playVideo();  
                    $("#curr_title").text(name); 
                    $("#player").css("visibility", "visible");
                    getList();
            })
}
var list = '';
var intervalo = setInterval(function(){
    getList();
},10000);

function getList() {

    $.get('{{url("videos?take=20")}}', function(data) {
                            for(i = 0 ;i < data.length;i++) {
                                list += '<div class="list row" id="'+data[i].id+'">'
                                list += '<div class="up small-1 columns "><i class="fa fa-arrow-up"> '+data[i].voteup+'</i></div>'
                                list += '<div class="down small-1 columns "><i class="fa fa-arrow-down"> '+data[i].votedown+'</i></div>'
                                list += '<div class="columns small-10">'+data[i].name+'</div>'
                                list += '</div>'
                            }
                            $("#list_tits").html(list);
                            list = '';
                        });
}


function post(url, success) {
    $.ajax({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        method: 'POST',
        url: url, 
        success: success
    })
}

</script>
@stop