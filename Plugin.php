<?php
/**
 * Markdown 解析增强插件，目前已增加解析bilibili视频以及steam游戏解析
 *
 * @package KMarkdownPlus
 * @author 桐谷咸鱼
 * @version 1.1.0
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

        Helper::addRoute('steamrewrite', '/steamrewrite.kmp', 'KMarkdownPlus_Action', 'steam');
        
        return "插件启用成功，请先设置插件！";

        /**
         * 以下为待开发功能
         */
        // $db= Typecho_Db::get();
		// $prefix = $db->getPrefix();
        // $db->query('CREATE TABLE IF NOT EXISTS '.$prefix.'SteamGameDetailOption ("key"  TEXT NOT NULL,"value"  TEXT NOT NULL,PRIMARY KEY ("key"))');
    }
    /**
     * 禁用插件
     */
    public static function deactivate()
    {
        Helper::removeRoute('steamrewrite');
	}
    /**
     * 插件设置
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $options = Helper::options();
        $element1 = new Typecho_Widget_Helper_Form_Element_Radio('steamProxy', array('y' => '开启','n' => '关闭'), 'n', _t('是否启用steam引用代理加速？'), '开启后能加速引用Steam，<a href="https://www.kiripa.com/article/2018111432#%E8%AE%BE%E7%BD%AE">详细请看文档</a>，请看文档后决定是否开启！');
        $form->addInput($element1);
        $element2 = new Typecho_Widget_Helper_Form_Element_Radio('commentSteam', array('y' => '开启','n' => '关闭'), 'n', _t('是否开启评论区解析？'), '开启后评论区也将支持Steam引用，需要进行一些设置，<a href="https://www.kiripa.com/article/2018111432#%E8%AE%BE%E7%BD%AE">详细请看文档</a>');
        $form->addInput($element2);
        $element3 = new Typecho_Widget_Helper_Form_Element_Text('refer', null, null, _t('其否开启请求来源判断？'), '只在启用steam加速后有效，如果开启了steam加速引用，<a href="https://www.kiripa.com/article/2018111432#%E8%AE%BE%E7%BD%AE">请先看文档</a>后再配置此项！');
        $form->addInput($element3);
    }
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
        $options = Helper::options();
        if( $options->plugin('KMarkdownPlus')->commentSteam == 'y' ){
            $text = KMarkdownPlus_Plugin::markdownSteam($text);
        }
        return $text;
    
    }

    public static function markdownSteam($text)
    {
		
		
		// https://store.steampowered.com/app/
        // https://store.steampowered.com/api/appdetails/?appids=637650
        
        preg_match_all("/\[steam +?id=([0-9]*?) *?\/\]/", $text, $result, PREG_SET_ORDER);
        
		foreach  ($result as $res) {
            $url = "https://store.steampowered.com/widget/".$res[1];
            $options = Helper::options();
            if( $options->plugin('KMarkdownPlus')->steamProxy == 'y' ){
                $url = $options->index."/steamrewrite.kmp?id=".$res[1];
            }
			$text = str_replace($res[0], "<iframe src=\"".$url."/\" style=\"border:none;height:190px;width:100%;\"></iframe>", $text);
        }
        return $text;
    }

    public static function markdownBili($text) {
        preg_match_all("/\[bili +?id=([0-9a-zA-Z]*?) *?\/\]/", $text, $result, PREG_SET_ORDER);
	foreach  ($result as $res) {
            $url = "//player.bilibili.com/player.html?".(strpos($res[1], "BV") === 0 ? 'bvid' : 'aid')."=".$res[1];
            $iframe = "<div style='width:100%;height:0;padding-bottom:75%;position: relative;'>";
            $iframe = $iframe . "<iframe src='" . $url . "' class='bili-iframe-kmp' style=\"width:100%;height:100%;position: absolute;top:0;left:0;\" scrolling=\"no\" border=\"0\" frameborder=\"no\" framespacing=\"0\" allowfullscreen=\"true\"> </iframe>";
            $iframe = $iframe . "</div>";
            $text = str_replace($res[0], $iframe, $text);
        }
        
        return $text;
    }
	
	
	
	
}







