<?php
/**
 * Markdown 解析增强插件，目前已增加解析bilibili视频以及steam游戏解析
 *
 * @package KMarkdownPlus
 * @author 桐谷咸鱼
 * @version 1.0.1
 * @link https://www.kiripa.com/article/2018111432
 */

class KMarkdownPlus_Plugin implements Typecho_Plugin_Interface
{

    

    /**
     * 激活插件
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->markdown = array('KMarkdownPlus_Plugin', 'contentsMarkdown');
        Typecho_Plugin::factory('Widget_Abstract_Comments')->markdown = array('KMarkdownPlus_Plugin', 'commentsMarkdown');

        /**
         * 以下为待开发功能
         */
        // Helper::addRoute('getSteamGameDetail', '/getGameDetail', 'KMarkdownPlus_Action', 'action');
        // Helper::addRoute('deleteGameDetail', "/deleteGameDetail", 'KMarkdownPlus_Action', 'delete');
        // $db= Typecho_Db::get();
		// $prefix = $db->getPrefix();
        // $db->query('CREATE TABLE IF NOT EXISTS '.$prefix.'SteamGameDetailOption ("key"  TEXT NOT NULL,"value"  TEXT NOT NULL,PRIMARY KEY ("key"))');
    }
    /**
     * 禁用插件
     */
    public static function deactivate()
    {
        // Helper::removeRoute('getSteamGameDetail');
        // Helper::removeRoute('deleteGameDetail');
	}
    /**
     * 插件设置
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {}
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {}

    public static function contentsMarkdown($text) {

        $text = Markdown::convert($text);
        $text = KMarkdownPlus_Plugin::markdownSteam($text);
        $text = KMarkdownPlus_Plugin::markdownBili($text);
        return $text;
    
    }

    public static function commentsMarkdown($text) {

        $text = Markdown::convert($text);
        $text = KMarkdownPlus_Plugin::markdownSteam($text);
        return $text;
    
    }

    public static function markdownSteam($text)
    {
		
		
		// https://store.steampowered.com/app/
        // https://store.steampowered.com/api/appdetails/?appids=637650
        
		preg_match_all("/\[steam +?id=([0-9]*?) *?\/\]/", $text, $result, PREG_SET_ORDER);
		foreach  ($result as $res) {
			$text = str_replace($res[0], "<iframe src=\"https://store.steampowered.com/widget/".$res[1]."/\" style=\"border:none;height:190px;width:100%;\"></iframe>", $text);
        }
        return $text;
    }

    public static function markdownBili($text) {
        preg_match_all("/\[bili +?id=([0-9]*?) *?\/\]/", $text, $result, PREG_SET_ORDER);
		foreach  ($result as $res) {
            $iframe = "<div style='width:100%;height:0;padding-bottom:75%;position: relative;'>";
            $iframe = $iframe . "<iframe src='//player.bilibili.com/player.html?aid=".$res[1]."' class='bili-iframe-kmp' style=\"width:100%;height:100%;position: absolute;top:0;left:0;\" scrolling=\"no\" border=\"0\" frameborder=\"no\" framespacing=\"0\" allowfullscreen=\"true\"> </iframe>";
            $iframe = $iframe . "</div>";
            $text = str_replace($res[0], $iframe, $text);
        }
        
        return $text;
    }
	
	
	
	
}







