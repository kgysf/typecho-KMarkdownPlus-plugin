<?php
class KMarkdownPlus_Action implements Widget_Interface_Do {
	

    public function execute() {
		//Do
	}

	/**
	 * 爬取游戏信息，第一次获取存储到数据库，后期从数据库获取
	 */
    public function action(){
		header("Content-Type:application/json");
		// header("Access-Control-Allow-Origin: *");  // 调试时开启
		
		$url = 'https://store.steampowered.com/api/appdetails/?appids=';
		$db= Typecho_Db::get();
		
        $curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2000);
		curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7,uz;q=0.6,ja;q=0.5"));
		
		if (isset($_GET['gid'])) {
			$gid = $_GET['gid'];

			$query= $db->select('value')->from('table.SteamGameDetailOption')->where('key = ?', $gid);
			$value = $db->fetchRow($query);
			
			if (count($value) > 0) {
				$res = $value['value'];
				echo $res;
			} else {
				curl_setopt($curl, CURLOPT_URL, $url . $gid);
				$res = curl_exec($curl);
				$res = Json::decode($res);
				if (isset($res->$gid)) {
					// echo Json::encode($res);
					$res = $res->$gid;
					try {
						if ($res->success == true) {
							$res->data->detailed_description = '';
							$res->data->dlc = '';
							$res->data->legal_notice = '';
							$res->data->metacritic = '';
							$res->data->movies = '';
							$res->data->packages = '';
							$res->data->reviews = '';
							$res->data->screenshots = '';
							$res->data->about_the_game = '';
							$res->data->supported_languages = '';
							$res->data->achievements = '';
							$res->data->categories = '';
							$res->data->content_descriptors = '';
							$res->data->pc_requirements = '';
							$res->data->package_groups = '';
							$res->data->background = '';
							$insert = $db->insert('table.SteamGameDetailOption') ->rows(array('key' => $gid, 'value' => Json::encode($res)));
							$insertId = $db->query($insert);
						}
					} catch (Exception $e) {
						
					}
				}
				echo Json::encode($res);
			}
			
		}
		
		
		
		
		
        curl_close($curl);

	}
	

	public function steam() {

		$options = Helper::options();
        if( !is_null($options->plugin('KMarkdownPlus')->refer) && $options->plugin('KMarkdownPlus')->refer != '' ){
			$num = 0;
			$refers = split(',',$options->plugin('KMarkdownPlus')->refer);
			foreach($refers as $refer) {
				if (strpos($_SERVER['HTTP_REFERER'], $refer)!=false) {
					$num += 1;
				}
			}
			if ($num <= 0) {
				http_response_code(404);
				exit;
			}
        }

		try {
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
			} else {
				return;
			}
	
			$options = Helper::options();
			$url = "https://store.steampowered.com/widget/".$id."/";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 500);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7,uz;q=0.6,ja;q=0.5"));


			$res = curl_exec($curl);
			
			preg_match_all("/<(link|script|img)(.*?)(href|src)=\"(http.*?)\"(.*?)>/",$res,$result,PREG_SET_ORDER);
			
			foreach($result as $item) {
				//echo "<".$item[1].$item[2].$item[3].'="'.$item[4].'"'.$item[5].'>';
				preg_match_all("/(.*?)\/([a-zA-Z0-9_\-\.]*?)\.(js|css|jpg|png)/",$item[4],$name,PREG_SET_ORDER);
				
				if ($name[0][3] == 'jpg' || $name[0][3] == 'png') {
					$name = $id . '_' . $name[0][2].".".$name[0][3];
				} else {
					$name = $name[0][2].".".$name[0][3];
				}
				if (!$this->isExists($name)) {
					// code...
					if ($this->download($item[4],$name)) {
						if($this->isImg($name)) {
							$path = $options->index.'/usr/uploads/steam/imgs/'.$name;
						} else {
							$path = $options->index.'/usr/uploads/steam/'.$name;
						}
						$res = str_replace($item[4],$path,$res);
					}
				} else {
					if($this->isImg($name)) {
						$path = $options->index.'/usr/uploads/steam/imgs/'.$name;
					} else {
						$path = $options->index.'/usr/uploads/steam/'.$name;
					}
					$res = str_replace($item[4],$path,$res);
				}
				
				// print_r($name);
			}
	
			echo $res;
			
			curl_close($curl);
		} catch (Exception $e) {
			print_r($e);
		}

	}

	private function isExists($file) {
		$path = $this->getFilePath($file);

		if (file_exists($path)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function download($url,$file) {

		try {
			$path = $this->getFilePath($file);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 500);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7,uz;q=0.6,ja;q=0.5"));

			$res = curl_exec($curl);

			if (stripos($file,'.css') != false) {
				$res = $this->parseCss($res);
			}


			if (file_put_contents($path,$res)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}


	private function parseCss($css) {
		preg_match_all("/url\([ ']*?\/(.*?)[ ']*?\)/",$css,$urls,PREG_SET_ORDER);
		foreach($urls as $url) {
			preg_match_all("/(.*?)\/([a-zA-Z0-9_\-\.]*?)\.(js|css|jpg|png|gif)/",$url[1],$name,PREG_SET_ORDER);

			$name = $name[0][2].".".$name[0][3];

			// $css = $css . $url[1] . '|';

			if (!$this->isExists($name)) {
				// code...
				if ($this->download('https://store.steampowered.com/'.$url[1],$name)) {
					$path = '/usr/uploads/steam/imgs/'.$name;
					$css = str_replace('/'.$url[1],$path,$css);
				}
			} else {
				$path = '/usr/uploads/steam/imgs/'.$name;
				$css = str_replace('/'.$url[1],$path,$css);
			}
		}
		return $css;
	}

	private function getPath() {
		$options = Helper::options();
		$prefix = $options->themeFile('REPLSTR');
		$prefix = str_replace('/themes/REPLSTR/','/',$prefix);
		$prefix = $prefix . 'uploads/steam/';
		if (!is_dir($prefix)) {
			mkdir($prefix);
		}
		if (!is_dir($prefix.'imgs/')) {
			mkdir($prefix.'imgs/');
		}
		return $prefix;
	}

	private function getFilePath($file) {
		$prefix = $this->getPath();
		if (!$this->isImg($file)) {
			$path = $prefix.'/'.$file;
		} else {
			$path = $prefix.'/imgs/'.$file;
		}
		return $path;
	}

	private function isImg($file) {
		if (stripos($file,'.jpg') == false && stripos($file,'.png') == false && stripos($file,'.gif') == false) {
			return false;
		} else {
			return true;
		}
	}
	

	
	
}