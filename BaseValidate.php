<?php


namespace app\validate;

use think\Validate;

class BaseValidate extends Validate
{
    /**
     * @param string $scene
     * @return array
     * Description 获取表单数据
     */
    public function getData($scene = '', $callback = '')
    {
        if(!is_null($scene) && isset($this->scene[$scene])){
            $fields = $this->scene[$scene];
        }else{
            $fields = array_keys($this->rule);
        }
        $data = [];
        foreach ($fields as $value){
            $data[$value] = input($value);
        }
        if(!empty($callback)){
            return $callback($data, input());
        }else{
            return $data;
        }
    }

    /**
     * @param $data
     * @param $field
     * @return bool
     * Description 验证字段是否唯一
     */
    protected function uniquer($data, $field){
        $id = input('id');
        $where = [
            $field => $data
        ];
        if(!empty($id)){
            $where['id'] = ['neq', $id];
        }
        return $this->getCount($where) != 0 ? false : true;
    }

    /**
     * @param $data
     * @param $params
     * @return bool
     * Description 验证字段是否存在于莫个表的字段中
     */
    protected function inScope($data, $params){
        $params = explode(',', $params);
        $arr = call_user_func_array(['app\model\\'. $params[0],'getAll'],[[],$params[1]]);
        return in_array($data,array_column($arr, $params[1]));
    }

    public function __call($method, $params)
    {
        $way = substr($method, 0, 3);
        if(strtolower($way) == 'get'){
            $class_name = basename(str_replace('\\', '/', get_class($this)));
            return call_user_func_array(['app\model\\'. $class_name,$method],$params);
        }
    }
}