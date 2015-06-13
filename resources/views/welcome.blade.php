@extends('master')
 
@section('title') - uQueue - @stop

@section('content')

<div class="row main">
    <div class="columns medium-10 small-centered">
        <div class="row">
            <div class="response columns small-8 medium-9 large-10 ">Search or paste a YouTube url to add it to the queue</div> 
            <div class="columns columns small-4 medium-3 large-2 "><a href="{{url('groups')}}" class="small-12 button">Groups</a></div>
        </div>

        <div>Search:</div>
        <input type="text" id="search-button">
        <div id="search-container"></div>
        <br>
       
        <div>Paste:</div>
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

    $('#search-button').on('keyup',search);

    handleAPILoaded();

    function handleAPILoaded() {
      $('#search-button').attr('disabled', false);
    }

    var results = '', items = []
    // Search for a specified string.
    function search() {

      var q = $('#search-button').val();
        
      $.get('https://www.googleapis.com/youtube/v3/search?part=snippet&videoDuration=short&type=video&q='+q+'&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4', function(e){
        items['shortv'] = e.items;
      })
      $.get('https://www.googleapis.com/youtube/v3/search?part=snippet&videoDuration=medium&type=video&q='+q+'&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4', function(e){
        items['mediumv'] = e.items;
      })
      displayRes()
    }

    function displayRes() {
        var results_short = '',
        results_med = ''
        // console.log(items);
        for(i=0;i<items.mediumv.length;i++) {
            results_short += '<li data-id="'+items.shortv[i].id.videoId+'">'+items.shortv[i].snippet.title+'</li>';
            results_med += '<li data-id="'+items.mediumv[i].id.videoId+'">'+items.mediumv[i].snippet.title+'</li>';
       
        }
        // console.log(results_short);
        $("#search-container").html('<div class="close right"><i class="fa fa-close"></i></div><br>up to 4min videos')
        $("#search-container").append(results_short)
        $("#search-container").append('<br>form 4 to 20 min videos')
        $("#search-container").append(results_med)

    }
    $("#search-container").on('click','.close',function(){
        $("#search-container").html('');
    });

    $("#search-container").on('click','li',function(){
        $(".response").animate({opacity : 1});
        // console.log($(this).attr('data-id'));
        id = $(this).attr('data-id');
        addtoQ(id)
        $("#search-container").html('');

    })

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

function addtoQ(id) {
    $.get('{{url("video")}}/'+id, function(data){
                $(".response").text(data);
                setTimeout(function(){
                    $(".response").animate({opacity : 0})
                },5000);
                getList();
            });
}
function setVideo() {
    $(".response").text('Reading url');
    var id = getId($('.url').val());
    console.log(id);
        if(id != undefined) {
            addtoQ(id)
        } else {
            $(".response").text('Url not recognized');
            setTimeout(function(){
                $(".response").animate({opacity : 0});
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
        // console.log(data)
        if(data.length < 2) {
             $.get('{{url("video/random/0")}}', function(data) {
                rand = data;
                id = rand.video;
                // addtoQ(id)
             });
         }
                            for(i = 0 ;i < data.length;i++) {
                                
                                list += '<div class="list row" id="'+data[i].id+'">'
                                list += '<div class="up small-1 columns "><i class="fa fa-arrow-up"> '+data[i].voteup+'</i></div>'
                                list += '<div class="down small-1 columns "><i class="fa fa-arrow-down"> '+data[i].votedown+'</i></div>'
                                list += '<div class="columns small-8">'+data[i].name+'</div>'
                                list += '<div class="columns small-2">'
                                list +=  data[i].group ? data[i].group.name : '&nbsp;'
                                list += '</div>'
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