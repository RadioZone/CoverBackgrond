<?php
$url = 'https://www.radiozone.fr/';
$stations = array('80s', '90s', '00s', '10s');
$station = $stations[rand(0, count($stations)-1)];
$station = '00s';

$json = json_decode(file_get_contents($url.$station.'/meta'), true);
$cover = $json['current']['cover'];
$artist = $json['current']['artist'];
$title = $json['current']['title'];

$sentens = 'A l\'antenne de RadioZone '.ucfirst($station).' : '.$artist.' - '.$title;
$home_path = '/home/choiz/';
$display_width = 1920;
$display_height = 1080;
$font_size = 18;

if (file_exists('config.php')) {
    include_once('config.php');
}

$cover_width = 300;
$cover_height = 300;
$coef_width = $display_width / $cover_width;
$coef_height = $display_height / $cover_height;
$coef = $coef_height*100;
if ($coef_width>$coef_height) {
    $coef = $coef_width*100;
}
$cover_position = ($display_height - $cover_height)/2;
$text_position = ($display_height - $cover_height)/2 + $cover_height + ($font_size*2);

$coverurl = $url.'static/cover/'.$station.'/'.$cover;
exec('wget -q -O '.$home_path.'radiozone.jpg "'.$coverurl.'"');
sleep(1); // wait we get image
exec('convert '.$home_path.'radiozone.jpg '.$home_path.'radiozone.png');
exec('rm '.$home_path.'radiozone.jpg');
exec('convert -resize '.$coef.'% '.$home_path.'radiozone.png -alpha on -channel A -evaluate set 50% +channel -gravity center -extent '.$display_width.'x'.$display_height.' '.$home_path.'radiozoneBG.png');
exec('convert '.$home_path.'radiozone.png -alpha on \
            -gravity north -crop '.$display_width.'x'.$display_height.'+0-'.$cover_position.'\! \
                -background none -compose Over -flatten \
            -gravity south -fill black -pointsize '.$font_size.' -annotate +0+'.$text_position.' "'.$sentens.'" '.$home_path.'radiozone.png');
exec('convert '.$home_path.'radiozoneBG.png '.$home_path.'radiozone.png -gravity center -composite '.$home_path.'radiozone.png');
exec('rm '.$home_path.'radiozoneBG.png');
