<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    #常量定义
    
    const CONST_ERROR_MSG_DEFAULT_GROUP='default';
    
    #常量定义 END
    
    private $_errorMsgArr=array();
    
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout='//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();
    
    /**
     * 视图目录名称
     * 
     */
    private $_view_dir='';

    ###############

    /**
     * 获取视图文件的目录
     * 
     */
    public function getViewPath(){
        if(($module=$this->getModule())===null){
            $module=Yii::app();
        }
        if('' != $this->_view_dir){ //改造重点，如果有视图目录，这是不使用 id 作为目录分界，从而完成跨越视图调用。
            return $module->getViewPath().DIRECTORY_SEPARATOR.$this->_view_dir;
        }
        return $module->getViewPath().DIRECTORY_SEPARATOR.$this->getId();
    }
    
    /**
     * 检查并设置自定义的view dir
     */
    private function _checkAndSetViewDir($view){
        $view_split= explode('/', $view);
        if(!isset($view_split[1]) || ''== $view_split[1] ){
            //解析其他目录视图
            $view=$view_split[0];
        }else{
            $view=$view_split[1];
            $this->_view_dir=$view_split[0]; //设置视图子目录，这样允许跨视图文件解析
        }
        return $view;
    }
    
    /**
     * 像JKGLib 一样的view 方法调用视图，支持跨id 目录视图文件解析
     * 支持JKTesting 测试系统接入。
     * 
     * @return array();
     */
    public function viewSingle($view,$data=null,$return=false){
        
        $view=$this->_checkAndSetViewDir($view);
        
        $result_arr= array_merge($data, array('content'=>parent::renderPartial($view, $data, $return),));        
        if(false === $return){
            echo $result_arr['content'];
        }
        
        $this->_view_dir=''; //设置为空
        return $result_arr;
    }
    
    /**
     * 像JKGLib 一样的view 方法调用视图，支持跨id 目录视图文件解析
     * 支持JKTesting 测试系统接入。
     * 
     * @return array();
     */
    public function view($view,$data=null,$return=false){
        
        $view=$this->_checkAndSetViewDir($view);
        
        $result_arr= array_merge($data, array('content'=>parent::render($view, $data, $return),));        
        if(false === $return){
            echo $result_arr['content'];
        }
        
        $this->_view_dir=''; //设置为空
        return $result_arr;
    }
        
    /**
     * 添加错误信息
     * @param string $message 消息
     * @param string $group 可选组参数，这个值不能为null ，可以忽略默认值参考常量
     * @return IJKWebController
     */
    public function addErrorMsg($message,$group=self::CONST_ERROR_MSG_DEFAULT_GROUP){
        if(null == $group)throw new Exception('$group param not be empty.','141101_1637');
        if(!is_array($this->_errorMsgArr[$group])){
            $this->_errorMsgArr[$group]=array();
        }
        $this->_errorMsgArr[$group][]=$message;
        return $this;
    }
    
    
    /**
     * 判断是否存在错误信息
     * @return boolean
     */
    public function hasErrorMsg($group=null){
        if(null == $group){
            foreach($this->_errorMsgArr as $groupkey=>$groupvalue){
                if(0 < count($this->_errorMsgArr[$groupkey]))return true;
            }
        }else{
            if(0 < count($this->_errorMsgArr[$group]))return true;
        }
        
        return false; //没有错误信息
    }
    
    /**
     * 获取错误信息
     * @return array
     */
    public function getErrorMsg($group=self::CONST_ERROR_MSG_DEFAULT_GROUP){
        if(null == $group){
            return $this->_errorMsgArr;
        }
        return $this->_errorMsgArr[$group];
    }
    
    /**
     * 清除错误数组
     * @param string $group 要清除的组参数
     * @return IJKWebController
     */
    public function cleanErrorMsg($group=self::CONST_ERROR_MSG_DEFAULT_GROUP){
        if(null == $group){
            $this->_errorMsgArr=array();
        }else{
            unset($this->_errorMsgArr[$group]);
        }
        return $this;
    }
}