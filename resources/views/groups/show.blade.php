<?php 
$group_id = isset($group) ? $group->id : 0;
$group_name = isset($group) ? $group->name.' - ' : '';
$private = isset($group) ? $group->private : 0;

$owner = (isset($group) && $user) ? ($user->id == $group->user_id) ? true : false : false;

$curator = (isset($group) && $user) ? $user->curator($group->id) : false;

if(!$private || $owner || $curator) {
    $can_access = true;
} else {
    $can_access = false;
}
?>

@extends('master')
 
@section('title') {{$group_name}}inDalist @stop

@section('content')



<div class="row collapse">
    <div class="columns medium-4 fija">
        
        <div class="tabs-content panel">

            <div class="header">


        <img src="{{url()}}/logo.png" class="logo">
                <h1>{{$group_name}}inDalist</h1></div>

          <div class="content active" id="panel1">
            @if($can_access)
                <div>Search:</div>
                <input type="text" id="search-button">
                <div id="search-container"></div>    
                <div class="response">Search a YouTube url to add it to the queue</div>
            @else
                This list is not collaborative
            @endif
         </div>    
          <div class="content" id="panel2">
            @if($can_access)
                <div>Paste:</div>
                <input class="url" type="text" placeholder="youtube url">
                <div class="button secondary add small-12">Add to queue</div>
                <div class="response">Paste a YouTube url to add it to the queue</div> 
            @endif
         </div>

          <div class="content" id="panel3">
            <div class="group row collapse">
                <div>
                  Create a list
                </div>
              <div class="columns medium-12 large-8">
                <input type="text" id="group-create" class="error">
                <span class="group-status"></span>
              </div>
              <div class="columns medium-12 large-4">
                <div class="postfix button disabled group-create secondary">Create</div>
              </div>

            </div>
            <div class="browse-groups">
                @foreach($groups as $gr)
                    <a href="{{$gr->slug}}"><li>{{$gr->name}}</li></a>
                @endforeach
            </div>
          </div>
        </div>
        <div class="columns ">
        @if( isset($group) )
            @if(count($list) > 0)
            <h2>Play again:</h2>
            <div class="browse-list">
                <?php foreach ($list as $l) {
                    echo '<li data-id="'.$l->id.'"><small>'.$l->order.' <i class="fa fa-thumbs-up up"></i></small> '.$l->name.'</li>';
                    # code...
                } ?>
            </div>
            @endif
        @else
            <h2>Now playing:</h2>
            <dl class="sub-nav active-list"></dl>
            </dl>
        @endif
        </div>
        <div class="columns"><br>

            @if($can_access)
            <h2>Might also like:</h2>
            <div class="related"></div>
            @endif
        </div>

    </div>
<!-- </div> -->
<div class="columns medium-8">
    <div id="player"></div>
    <div id="curr_title">
        @if($user)
        <div class="left"><i class="add_lib fa fa-plus" title="Add to your main list"></i><i class="fa-spin fa fa-circle-o-notch add-load"></i></div>
        &nbsp;
        @endif
        <span></span>

        @if($can_access)
        <div class="right"><i class="skip fa fa-forward"></i><i class="fa-spin fa fa-circle-o-notch skip-load"></i></div>
        @endif
    </div>
    <div data-alert class="alert-box">
      <span>Video added to your main list</span>
      <!-- <button tabindex="0" class="close" aria-label="Close Alert">&times;</button> -->
    </div>

    <!-- <div class="row"> -->
        <!-- <div class="columns medium-10 small-centered"> -->
            <div id="list_tits"></div>
        <!-- </div> -->
    </div>
</div>

@stop

@section('scripts')

<script type="text/javascript">

var group_orig = '{{$group_id}}', group_list;
var user_lib;
<?php if($user && $user->library()): ?>
var user_lib = '{{$user->library()->id}}';
<?php endif ?>

var groups = window.location.hash.toString().split('/')[1];
    group_list = groups ? groups : '{{$group_id}}';
    // group_orig = groups;

$(window).bind('hashchange', function() {
    groups = window.location.hash.toString().split('/')[1];
    group_list = groups;
    // getList();
});

$(window).resize(function() {

    $("#player").height($("#player").width()*9/16)
})

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

    $("#player").height($("#player").width()*9/16)
    $(".active-list").on('click', 'dd', function() {
        $(this).toggleClass("active");
        $(this).find("i").toggleClass("fa-volume-up").toggleClass("fa-volume-off");
        if($(this).is('.active')) {
            listen[$(this).attr("data-id")] = 'on';
        } else {
            listen[$(this).attr("data-id")] = 'off';
        }
        getActGroups();
        window.location = '#!/'+listen_on.substr(1)
    })

    $('#search-button').on('keyup',search);

    handleAPILoaded();

    function handleAPILoaded() {
      $('#search-button').attr('disabled', false);
    }

    var results = '', items = []
    // Search for a specified string.
    function search() {

      var q = $('#search-button').val();

      if(q == '') {
        $("#search-container").html('');
      } else { 
          $.get('https://www.googleapis.com/youtube/v3/search?part=snippet&videoDuration=short&type=video&q='+q+'&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4', function(e){
            items['shortv'] = e.items;
            console.log(e);
          })
          $.get('https://www.googleapis.com/youtube/v3/search?part=snippet&videoDuration=medium&type=video&q='+q+'&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4', function(e){
            items['mediumv'] = e.items;
          })
          displayRes();
      }
    }

    function displayRes() {
        var results_short = '',
        results_med = '', results = ''
        // console.log(items);
        for(i=0;i<items.mediumv.length;i++) {
            results += '<li data-id="'+items.shortv[i].id.videoId+'"><img class="hide-for-medium left" src="'+items.shortv[i].snippet.thumbnails.default.url+'" height="50px">'+items.shortv[i].snippet.title+'</li>';
            results += '<li data-id="'+items.mediumv[i].id.videoId+'"><img class="hide-for-medium left" src="'+items.mediumv[i].snippet.thumbnails.default.url+'" height="50px">'+items.mediumv[i].snippet.title+'</li>';
       
        }
        // console.log(results_short);
        // $("#search-container").html('<div class="close right"><i class="fa fa-close"></i></div><br>up to 4min videos')
        $("#search-container").html(results)
        // $("#search-container").append('<br>form 4 to 20 min videos')
        // $("#search-container").append(results_med)

    }
    $("#search-container").on('click','.close',function(){
        $("#search-container").html('');
    });

    $("#search-container, .related").on('click','li',function(){
        $(".response").animate({opacity : 1});
        $(this).addClass("selected");
        // console.log($(this).attr('data-id'));
        id = $(this).attr('data-id');
        addtoQ(id)
        $("#search-container").html('');
    })
    $(".browse-list").on('click','li',function(){
        $(this).addClass('inactive');
        $(".response").animate({opacity : 1});
        id = $(this).attr('data-id');
        updateQ(id);
        // updateBrowse();
    })
    $("#curr_title, #list_tits").on('click', '.add_lib', function() {
        id = $(this).attr('data-id');
        $(this).hide();
        $(this).next().show();
        addtoLib(id);
    })

    $(".add").on('click',setVideo);
    $(".url").on('keyup',function(e) {
        if(e.keyCode == 13) {
            setVideo();
        }
    });

    $("#list_tits").on('click', '.up', function(e){
        var id = $(this).parent().parent().attr('id');
        $.get('{{url("video")}}/'+id+'/update?active=1&voteup=1', function(e) {
            getList();
        });
    })

    $("#list_tits").on('click', '.down', function(e){
        var id = $(this).parent().parent().attr('id');
        $.get('{{url("video")}}/'+id+'/update?active=1&votedown=1', function() {
            getList();
        });

    })

    $(".skip").on('click',function() {
        queueVideo();
        $(".skip-load").show();
        $(this).hide();
    })
})

function addtoQ(id) {
    $.get('{{url("video")}}/'+id+'?group='+group_orig, function(data){
                $(".response").text(data);
                setTimeout(function(){
                    $(".response").animate({opacity : 0})
                },5000);
                getList();
            });
    updateBrowse();
}

function updateQ(id) {
    $.get('{{url("video")}}/'+id+'/update?active=1', function(data){
                getList();
            });
    updateBrowse();
}

function addtoLib(id) {
    if(!user_lib) {
        alert('To add songs you must create your library list first');
        window.location = '{{url('home')}}'
    }
    $.get('{{url("video")}}/'+id+'?group='+user_lib, function(data){
                console.log(data);
                $('.add-load').hide();
                $('.alert-box').fadeIn();
                setTimeout(function() {
                    $('.add_lib').show();
                    $('.alert-box').fadeOut();
                },3000);
                // alert('video added to your library');
            });
}

function setVideo() {
    $(".response").text('Reading url');
    var id = getId($('.url').val());
    // console.log(id);
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
window.onYouTubeIframeAPIReady = function() {
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
        queueVideo()
    }
}
var video_played = false;
function queueVideo() {
    if(video_played) {
        $.get('{{url("video")}}/'+video_played+'/update?active=0', function(e) {
            // console.log(e);
            // if(e == 1) {
            //     $.post('https://graph.facebook.com', { id: '<?php echo $url; ?>', scrape: true });
            // }
            play();
        });
    } else{
        play();
    }
}

function play() {
     $.get('{{url("videos?take=1")}}&group='+group_list, function(data) {
                var video = data[0] ? data[0].video : 'L-6LXhFNeGw'
                var name = data[0] ? data[0].name : ''
                video_played = data[0] ? data[0].id : false;
                // console.log(data[0].video);
                player.cueVideoById({videoId:video});
                // event.target.playVideo();  
                player.playVideo();  
                $("#curr_title span").text(name); 

                $("#curr_title .add_lib").attr('data-id', video); 
                $("#player").css("visibility", "visible");
                $(".skip").show();
                $(".skip-load").hide();
                getList();
                if(data[0]) {
                    getRelated(video);
                }
            })
}
var list = '', br_list = '', ac_list = '';
var intervalo = setInterval(function(){
    getList();
    if(group_orig == 0) {
        activeGroups();
    } else {
        updateBrowse();
    }
},10000);

var listen = [], listen_on = '';
// console.log(group_list);
if(group_orig == 0) {
    activeGroups();
}

function getRelated(video) {
    $(".related").empty();
    // var data
    $.get('https://www.googleapis.com/youtube/v3/search?part=snippet&relatedToVideoId='+video+'&type=video&maxResults=20&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4', function(data) {
        // console.log(data.items);
        // data = data;
        var rI = data.items.length -1;
        for(r=rI; r>5; r--) {
            $.get('https://www.googleapis.com/youtube/v3/videos?key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4&part=contentDetails&id='+data.items[r].id.videoId, function(details) {
                duration = details.items[0].contentDetails.duration
                durationK = duration.split(/[\d]+/)
                duration = duration.split(/[\D]+/)
                if(durationK[1] != "H" && duration[durationK.indexOf("M")] < 20){
                    $(".related").append('<li data-id="'+data.items[rI].id.videoId+'">'+data.items[rI].snippet.title+'</li>')
                }
                rI--
            })
        }
    })

}

function activeGroups() {
    $.get('{{url("groups/active")}}', function(data) {
        // console.log(data);
        for(i = 0 ;i < data.length;i++) {

            g_id = data[i].group ? data[i].group.id : 0

            if(groups) {
                grAct = groups.split(',');
                // console.log(grAct);
                // console.log(grAct.indexOf('0'));

                if(grAct.indexOf(''+g_id) < 0) {
                    listen['g'+g_id] = 'off';
                } else {
                    listen['g'+g_id] = 'on';
                }
            } else {
                listen['g'+g_id] = 'on';
            }
            // console.log(listen);
            active = listen['g'+g_id] == 'on' ? 'active' : ''
            vol = listen['g'+g_id] == 'on' ? 'up' : 'off'
            if(data[i].group) {
                ac_list += '<dd class="'+active+'" data-id="g'+g_id+'"><a>'
                ac_list += data[i].group ? data[i].group.name : 'Home'
                ac_list += ' <i class="fa fa-volume-'+vol+'"></i></a></dd>'
            } else {
                ac_list += '<dd class="'+active+'" data-id="g'+g_id+'"><a>'
                ac_list += 'Home'
                ac_list += ' <i class="fa fa-volume-'+vol+'"></i></a></dd>'
            }
            
        }
        $(".active-list").html(ac_list);
        ac_list = '';
        getActGroups();
    })
}
function getActGroups() {
        listen_on = ''
        for(on in listen){
            if(listen[on] == 'on'){
                listen_on +=  on ;
            }
        }
        listen_on = listen_on.replace(/g/g, ',')
        group_list = '0,'+listen_on.substr(1)
        // console.log(group_list)
}
function updateBrowse() {
    $.get('{{url("videos?take=200")}}&inactive=1&group='+group_list, function(data) {
        // console.log(data)
        for(i = 0 ;i < data.length;i++) {
            br_list += '<li data-id="'+data[i].id+'">'
            br_list += '<small>' +data[i].order + ' <i class="fa fa-thumbs-up up"></i></small> ' + data[i].name
            br_list += '</li>'
        }

        $(".browse-list").html(br_list);
        br_list = '';
    })
    br_list = '';

}
function getList() {
    $.get('{{url("videos?take=20")}}&group='+group_list, function(data) {
        // console.log(data);
        if(data.length < 2) {
             $.get('{{url("video/random/".$group_id)}}');
         }
        for(i = 0 ;i < data.length;i++) {
            list += '<div class="list row collapse">'
            list += '<div class="small-2 columns icons" id="'+data[i].id+'">'
            @if($can_access)
                list += '<span>'+data[i].order+'</span>'
                list += '<span><i class="fa fa-thumbs-up up"></i></span>'
                list += '<span><i class="fa fa-thumbs-down down"></i></span>'
            @endif
            list += '</div>'
            list += '<div class="columns medium-8 small-7">'+data[i].name+'</div>'
            list += '<div class="columns medium-2 small-3">'
            list += data[i].group ? '<a href="{{url()}}/'+data[i].group.slug+'">' : ''
            list += data[i].group ? data[i].group.name : '&nbsp;'
            list += data[i].group ? '</a>' : ''

            list += '<span class="right icons">'
            @if($user)
                list += '<i class="fa fa-plus add_lib" data-id="'+data[i].video+'"></i>'
                list += '<i class="fa-spin fa fa-circle-o-notch add-list-load"></i>'
            @endif
            @if($owner)
                list += '<i class="fa fa-close delete" data-id="'+data[i].id+'"></i>'
            @endif
            list += '</span>'
            
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

setTimeout(function(){
    if (typeof(player) == 'undefined'){
        window.onYouTubeIframeAPIReady();
    }
}, 3000)


</script>

@stop