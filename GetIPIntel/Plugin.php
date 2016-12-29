<?php
/**
 * 基于 GetIPIntel.net 的评论反欺诈服务
 * 
 * @package GetIPIntel
 * @author Lan Tian
 * @version 1.0.0
 * @link https://xuyh0120.win
 *
 */
class GetIPIntel_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {    
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('GetIPIntel_Plugin', 'getipintel');
		return _t('请进行插件设置');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
	{
        $apiEmail = new Typecho_Widget_Helper_Form_Element_Text('apiEmail', NULL, 'postmaster@example.com',
        	_t('邮件地址'), "你的邮件地址。GetIPIntel 可能会向你发信验证邮件地址。如果你未能在 48 小时内回复邮件，你的服务器 IP 可能被屏蔽。");
		$form->addInput($apiEmail);

        $apiMode = new Typecho_Widget_Helper_Form_Element_Radio('apiMode', array("m" => "仅黑名单", "b" => "快速检查", "" => "默认模式", "f" => "全面检查"), "m",
			_t('模式'), "设置检查的模式。<br />仅黑名单：快速（60ms），漏过率高，误报率低<br />快速检查：较快（130ms），漏过率低，误报率高<br />默认模式：首次评论快速检查，GetIPIntel 后台全面检查，短时间再次评论加载全面检查数据<br />全面检查：很慢（5s），漏过率低，误报率高");
        $form->addInput($apiMode);
        
        $apiThreshold = new Typecho_Widget_Helper_Form_Element_Text('apiThreshold', NULL, '0.99',
        	_t('过滤阈值'), "当 GetIPIntel 返回的代理概率大于此值时认为验证失败。");
		$form->addInput($apiThreshold);

        $apiAction = new Typecho_Widget_Helper_Form_Element_Radio('apiAction', array("n" => "不操作", "c" => "人工审核", "s" => "垃圾评论", "f" => "提交失败"), "s",
			_t('代理操作'), "发现用户使用代理时的操作");
        $form->addInput($apiAction);
	}
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 通过 GetIPIntel 的 API 判断并采取操作
     */
    public static function getipintel($comment, $post)
    {
		$pluginOptions = Typecho_Widget::widget('Widget_Options')->plugin('GetIPIntel');
		$email = $pluginOptions->apiEmail;
		$ip = $_SERVER['REMOTE_ADDR'];
        if($pluginOptions->apiMode == '') {
            $possibility = file_get_contents("https://check.getipintel.net/check.php?ip=$ip&contact=$email");
        } else {
            $possibility = file_get_contents("https://check.getipintel.net/check.php?ip=$ip&contact=$email&flags=" . $pluginOptions->apiMode);
        }
		if(!$possibility) {
			/* GetIPIntel service died */
			$possibility = 0;
		}

		if($possibility >= $pluginOptions->apiThreshold) {
			/* trigger failure */
			switch($pluginOptions->apiAction) {
				case 'n':
					break;
				case 'c':
					$comment['status'] = 'waiting';
					break;
				case 's':
					$comment['status'] = 'spam';
					break;
				case 'f':
					throw new Typecho_Widget_Exception('抱歉，本站禁止通过代理服务器进行评论。');
					break;
			}
		}
        return $comment;
    }
}
