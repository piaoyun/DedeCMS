<?php if (!defined('DEDEINC')) {
    exit("Request Error!");
}

/**
 * 单表模型视图类
 *
 * @version        $Id: arc.sgpage.class.php 1 15:48 2010年7月7日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https: //weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once DEDEINC . "/arc.partview.class.php";

/**
 * 单表模型列表视图类
 *
 * @package          SgListView
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class sgpage
{
    public $dsql;
    public $dtp;
    public $TypeID;
    public $Fields;
    public $TypeLink;
    public $partView;

    /**
     *  php5构造函数
     *
     * @access    public
     * @param     int  $aid  内容ID
     * @return    string
     */
    public function __construct($aid)
    {
        global $cfg_basedir, $cfg_templets_dir, $cfg_df_style, $envs;

        $this->dsql = $GLOBALS['dsql'];
        $this->dtp = new DedeTagParse();
        $this->dtp->refObj = $this;
        $this->dtp->SetNameSpace("dede", "{", "}");
        $this->Fields = $this->dsql->GetOne("SELECT * FROM `#@__sgpage` WHERE aid='$aid' ");
        $envs['aid'] = $this->Fields['aid'];

        //设置一些全局参数的值
        foreach ($GLOBALS['PubFields'] as $k => $v) {
            $this->Fields[$k] = $v;
        }
        if ($this->Fields['ismake'] == 1) {
            $pv = new PartView();
            $pv->SetTemplet($this->Fields['body'], 'string');
            $this->Fields['body'] = $pv->GetResult();
        }
        $tplfile = $cfg_basedir . str_replace('{style}', $cfg_templets_dir . '/' . $cfg_df_style, $this->Fields['template']);
        $this->dtp->LoadTemplate($tplfile);
        $this->ParseTemplet();
    }

    //php4构造函数
    public function sgpage($aid)
    {
        $this->__construct($aid);
    }

    /**
     *  显示内容
     *
     * @access    public
     * @return    void
     */
    public function Display()
    {
        $this->dtp->Display();
    }

    /**
     *  获取内容
     *
     * @access    public
     * @return    void
     */
    public function GetResult()
    {
        return $this->dtp->GetResult();
    }

    /**
     *  保存结果为文件
     *
     * @access    public
     * @return    void
     */
    public function SaveToHtml()
    {
        $filename = $GLOBALS['cfg_basedir'] . $GLOBALS['cfg_cmspath'] . '/' . $this->Fields['filename'];
        $filename = preg_replace("/\/{1,}/", '/', $filename);
        $this->dtp->SaveTo($filename);
    }

    /**
     *  解析模板里的标签
     *
     * @access    public
     * @return    string
     */
    public function ParseTemplet()
    {
        $GLOBALS['envs']['likeid'] = $this->Fields['likeid'];
        MakeOneTag($this->dtp, $this);
    }

    //关闭所占用的资源
    public function Close()
    {
    }
} //End Class