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
	

	
	
}