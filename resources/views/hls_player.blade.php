<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HLS Player</title>
<link href="{{ asset('videojs/video-js.css') }}" rel="stylesheet" />
</head>
<body>
    <h2>HLS Video Test</h2>

    <video
        id="my-video"
        class="video-js vjs-default-skin"
        controls
        width="640"
        height="360"
    ></video>
    <!-- Video.js JS (body ke end me) -->
<script src="{{ asset('videojs/video.min.js') }}"></script>

    <script>
        // ensure DOM and video.js loaded
        window.addEventListener('load', function() {
            if (typeof videojs === 'undefined') {
                console.error('Video.js is not loaded!');
                return;
            }

            var player = videojs('my-video');
            player.src({
                src: "http://127.0.0.1:8000/storage/5ab6b1ce-f75b-41e5-bc60-e0d6c1f33431/hls/playlist.m3u8",
                type: 'application/x-mpegURL'
            });
            player.play();
        });
    </script>
</body>
</html>
