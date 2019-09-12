<?php
/**
 * Created by PhpStorm.
 * User: cycle_3
 * Email: 953006367@qq.com
 * Date: 2019/9/7
 * Time: 10:00
 */
namespace Aliyunvideo\Controller;

use Common\Controller\AdminBase;
use Aliyunvideo\Service\AliyunVoucherService;
use Aliyunvideo\Service\VideoConfService;

/**
 * 视频面板
 * Class VideoPanelController
 * @package Aliyunvideo\Controller
 */
class VideoPanelController extends AdminBase
{

    /**
     * 视频上传面板
     */
    public function fileUploadPanel(){
        AliyunVoucherService::updatinOverdueVideo();
        $jsLink = '/app/Application/Aliyunvideo'.DIRECTORY_SEPARATOR .'Libs'.DIRECTORY_SEPARATOR;
        $this->assign('jsLink',$jsLink);
        $this->display();
    }

    /**
     * 获取视频列表
     */
    public function getVideoList(){
        $where = [];
        $group_id = I('group_id','','trim');
        $where['group_id'] = $group_id;
        $page = I('page','','trim');
        $limit = I('limit','','trim');
        $where['is_aliyun'] = '1';
        $where['cover_url'] = ['neq',''];
        $where['is_delete'] = '0';
        $res = AliyunVoucherService::getVideoList($where,'edit_time desc',$page,$limit);
        $this->ajaxReturn($res);
    }

    /**
     * 删除视频
     */
    public function delVideoList(){
        $id = I('id','','trim');
        $res = AliyunVoucherService::delVideo($id);
        $this->ajaxReturn($res);
    }

    /**
     * 校验是否填写了配置信息
     */
    public function checkConf(){
        $res = VideoConfService::getVideoConfFind();
        if(!$res['data']['videoConfFind']['accesskey_id'] ||
            !$res['data']['videoConfFind']['accesskey_secret']){
            $this->ajaxReturn(self::createReturn(false));
        } else {
            $this->ajaxReturn(self::createReturn(true));
        }
    }

    /**
     * 获取视频的详细信息
     */
    public function getVideoDetails(){
        $videoId = I('video_id', '', 'trim');
        $res = AliyunVoucherService::aliyunVideoPlay($videoId);
        $this->ajaxReturn($res);
    }

}