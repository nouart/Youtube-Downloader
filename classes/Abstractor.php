<?php
namespace myPHPnotes;

/**
 * Processing Data
 */
class Abstractor{

	function checkValidYoutubeLink($url)
	{
		$pattern = '~
					^(?:https?://)?
					(?:www[.])?
					(?:youtube[.]com/watch[?]v=|youtu[.]be/)
					([^&]{11})
					~x';
		if (preg_match($pattern, $url, $match))
		{
			return true;
		}
		return false;
	}

	protected function arrayCreator($video, $audio, $extension, $additional, $format_code) {
		return [
			'format_code' => $format_code,
			'video' => $video,
			'audio' => $audio,
			'extension' => $extension,
			'additional'=> $additional
		];
	}

	protected function fetchArrayFromFormatCode($format_code) {
		switch ($format_code) {
			// Audio Only
			case '139' :
				return $this->arrayCreator(false, true, "m4a", "48k", $format_code);	
				break;
			case '249' :
				return $this->arrayCreator(false, true, "webm", "58k", $format_code);	
				break;
			case '250' :
				return $this->arrayCreator(false, true, "webm", "90k", $format_code);	
				break;
			case '140' :
				return $this->arrayCreator(false, true, "m4a", "128k", $format_code);	
				break;
			case '251' :
				return $this->arrayCreator(false, true, "webm", "165k", $format_code);	
				break;
			case '171' :
				return $this->arrayCreator(false, true, "webm", "171k", $format_code);	
				break;
			
			
			// Video Only
			case '160' :
				return $this->arrayCreator(true, false, "mp4", "144p", $format_code);	
				break;
			case '278' :
				return $this->arrayCreator(true, false, "webm", "144p", $format_code);	
				break;
			case '133' :
				return $this->arrayCreator(true, false, "mp4", "240p", $format_code);	
				break;
			case '242' :
				return $this->arrayCreator(true, false, "webm", "240p", $format_code);	
				break;
			case '243' :
				return $this->arrayCreator(true, false, "webm", "360p", $format_code);	
				break;
			case '134' :
				return $this->arrayCreator(true, false, "mp4", "360p", $format_code);	
				break;	
			case '244' :
				return $this->arrayCreator(true, false, "webm", "480p", $format_code);	
				break;	
			case '135' :
				return $this->arrayCreator(true, false, "mp4", "480p", $format_code);	
				break;	
			
			case '136' :
				return $this->arrayCreator(true, false, "mp4", "720p", $format_code);	
				break;	
			case '247' :
				return $this->arrayCreator(true, false, "webm", "720p", $format_code);	
				break;	
			case '137' :
				return $this->arrayCreator(true, false, "mp4", "1080p", $format_code);	
				break;
			case '248' :
				return $this->arrayCreator(true, false, "webm", "1080p", $format_code);	
				break;
			case '264' :
				return $this->arrayCreator(true, false, "mp4", "1440p", $format_code);	
				break;
			case '271' :
				return $this->arrayCreator(true, false, "webm", "1440p", $format_code);	
				break;
			case '266' :
				return $this->arrayCreator(true, false, "mp4", "2160p", $format_code);	
				break;
			case '138' :
				return $this->arrayCreator(true, false, "mp4", "2160p", $format_code);	
				break;
			case '313' :
				return $this->arrayCreator(true, false, "webm", "2160p", $format_code);	
				break;
				// Video + audio
			case '17' :
				return $this->arrayCreator(true, true, "3gp", "144p", $format_code);	
				break;
			case '36' :
				return $this->arrayCreator(true, true, "3gp", "180p", $format_code);	
				break;
			case '43' :
				return $this->arrayCreator(true, true, "webm", "360p", $format_code);	
				break;
			case '18' :
				return $this->arrayCreator(true, true, "mp4", "360p", $format_code);	
				break;
			case '22' :
				return $this->arrayCreator(true, true, "mp4", "720p", $format_code);	
				break;

			default:
			throw new \Exception("Error Processing Request: Format not found", 1);
			    break;			
		}
	}

	public function refineVideoJSON($json)
	{
		$title = $json->title;
		$uploader = $json->uploader;
		if(!$uploader) {
			$_SESSION['message'] = "Cannot fetch the video data";
			header("location: index.php");
		}
		$uploader_url = $json->uploader_url;
		$views = $json->view_count;
		$duration = $json->duration;
		$thumbnail =$json->thumbnail;
		$formats = $json->formats;
		$files = [];
		foreach($formats as $format) {
			$formatData = $this->fetchArrayFromFormatCode($format->format_id);
			$files[$format->format_id] = array_merge(['url' => $this->URLTweaker($format->url)."&title={$title}"], $formatData);
		}

		$data = ['title' => $title, 'uploader_url' => $uploader_url, 'uploader' => $uploader, 'views' => $views, 'duration' => $this->duration($duration), 'thumbnail' => $thumbnail, 'files' => $files];
		return $data;
	}

	public function duration($seconds) {
		return gmdate("H:i:s", $seconds);
	}
	public function URLTweaker($url){
		$pattern = '/^(https:\/\/)[a-zA-Z0-9.]+.googlevideo.com\//';
		return preg_replace($pattern, "https://redirector.googlevideo.com/", $url);
	}
}