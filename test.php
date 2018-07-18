<?php
chdir('C:\ffmpeg\bin');
var_dump( shell_exec('ffmpeg -i C:\xampp\htdocs\videoCutting\SampleVideo.mp4 -ss 00:00:00 -t 00:00:05 -async 1 C:\xampp\htdocs\videoCutting\SampleVideoNew.mp4') );
?>