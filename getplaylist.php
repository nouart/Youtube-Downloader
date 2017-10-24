<?php

require "initialization/init.php";

$url = $_GET['url'];
$json = $processor->getJSON($url);
$formatsAllowed = $config->get('formatsToShow.playlist');

$playlist = [];
$playlists = [];
foreach ($json->entries as $video_entry) {
	$video_info = $abstractor->refineVideoJSON($video_entry);
	$allowed_formats = [];
	foreach ($formatsAllowed as $index => $format_id) {
		if (isset($video_info['files'][$format_id])) {
			$allowed_formats['allowed_formats'][$index] = $video_info['files'][$format_id];
			$linklists[$format_id]['info'] = $video_info['files'][$format_id]['extension'].'-'.$video_info['files'][$format_id]['additional'];
			$linklists[$format_id]['links'][] = $video_info['files'][$format_id]; 
		}
	}
	$playlist[] = array_merge($video_info, $allowed_formats);
}

echo $twig->render('playlist/getplaylist.twig', ['playlist'=>$playlist, 'title'=>$json->title, 'linklists'=>$linklists]);
die();

