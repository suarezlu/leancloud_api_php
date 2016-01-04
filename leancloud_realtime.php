<?php
const LEANCLOUD_API_URL = 'https://api.leancloud.cn/1.1';

/**
 * 实时通信api
 *
 * https://leancloud.cn/docs/realtime_rest_api.html
 */
class Leancloud_Realtime{
    private $app_id = null;
    private $app_key = null;
    private $master_key = null;

    public function __construct($app_id,$app_key,$master_key){
        $this->app_id = $app_id;
        $this->app_key = $app_key;
        $this->master_key = $master_key;
    }

    public function classes_get($class,$class_id){
        $url = LEANCLOUD_API_URL . "/classes/$class";
        $class_id && $url .= "/$class_id";

        return $this->http_get($url,array());
    }

    public function classes_conversation_get($conversation_id=''){
        return $this->classes_get('_Conversation',$conversation_id);
    }

    /**
     * 创建一个对话
     *
     * @param $data
     * @return array|mixed
     */
    public function classes_conversation_post($data){
        $url = LEANCLOUD_API_URL . '/classes/_Conversation';

        return $this->http_post($url,$data);
    }

    /**
     * 增删对话成员
     *
     * @param $conversation_id
     * @param $data
     * @return array|mixed
     */
    public function classes_conversation_put($conversation_id,$data){
        $url = LEANCLOUD_API_URL . "/classes/_Conversation/$conversation_id";

        return $this->http_put($url,$data);
    }

    /**
     * 获取聊天记录
     *
     * @param $param
     * @param bool $use_master_key
     * @return array|mixed
     */
    public function rtm_messages_logs_get($param,$use_master_key=false){
        $url = LEANCLOUD_API_URL . '/rtm/messages/logs';

        return $this->http_get($url,$param,$use_master_key);
    }

    /**
     * 删除聊天记录
     *
     * @param $param
     * @return array|mixed
     */
    public function rtm_messages_logs_delete($param){
        $url = LEANCLOUD_API_URL . '/rtm/messages/logs';

        return $this->http_delete($url,$param,true);
    }

    /**
     * 取未读消息数
     *
     * @param $client_id
     * @return array|mixed
     */
    public function rtm_messages_unread_get($client_id){
        $url = LEANCLOUD_API_URL . "/rtm/messages/unread/$client_id";

        return $this->http_get($url);
    }

    /**
     * 通过REST API发消息
     *
     * @param $data
     * @return array|mixed
     */
    public function rtm_messages_post($data){
        $url = LEANCLOUD_API_URL . '/rtm/messages';

        return $this->http_post($url,$data,true);
    }

    /**
     * 获取暂态对话的在线人数
     *
     * @param $param
     * @return array|mixed
     */
    public function rtm_transient_group_onlines($param){
        $url = LEANCLOUD_API_URL . '/rtm/transient_group/onlines';

        return $this->http_get($url,$param);
    }

    /**
     * 查询在线状态
     *
     * @param $data
     * @return array|mixed
     */
    public function rtm_online_post($data){
        $url = LEANCLOUD_API_URL . '/rtm/online';

        return $this->http_post($url,$data,true);
    }

    /**
     * 封装http get
     * http get request
     *
     * @param $url
     * @param $data
     * @param bool $use_master_key
     * @return array|mixed
     */
    private function http_get($url,$data=array(),$use_master_key=false){
        $query = $comma = '';

        if($data){
            foreach($data as $k=>$v){
                $query .= $comma . $k . '=' .$v;
                $comma = '&';
            }
            $url .= $url . '?' . $query;
        }

        $app_key = $this->app_key;
        $use_master_key && $app_key = $this->master_key;

        $headers = array("X-LC-Id:$this->app_id","X-LC-Key:$app_key",'Content-Type:application/json');

        $ch = curl_init();
        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        );
        curl_setopt_array($ch,$option);

        $r_data = curl_exec($ch);
        curl_close($ch);

        if($r_data){
            return json_decode($r_data);
        }else
            return array();
    }

    /**
     * 封装http post
     * http post request
     *
     * @param $url
     * @param $data
     * @param bool $use_master_key
     * @return array|mixed
     */
    private function http_post($url,$data,$use_master_key=false){
        $app_key = $this->app_key;
        $use_master_key && $app_key = $this->master_key;

        $headers = array("X-LC-Id:$this->app_id","X-LC-Key:$app_key",'Content-Type:application/json');

        $ch = curl_init();
        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        );
        curl_setopt_array($ch,$option);

        $r_data = curl_exec($ch);
        curl_close($ch);

        if($r_data){
            return json_decode($r_data);
        }else
            return array();
    }

    /**
     * 封装http put
     *
     * @param $url
     * @param $data
     * @param bool $use_master_key
     * @return array|mixed
     */
    private function http_put($url,$data,$use_master_key=false){
        $app_key = $this->app_key;
        $use_master_key && $app_key = $this->master_key;

        $headers = array("X-LC-Id:$this->app_id","X-LC-Key:$app_key",'Content-Type:application/json');

        $ch = curl_init();
        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data)
        );
        curl_setopt_array($ch,$option);

        $r_data = curl_exec($ch);
        curl_close($ch);

        if($r_data){
            return json_decode($r_data);
        }else
            return array();
    }

    /**
     * 封装http delete方法
     *
     * @param $url
     * @param array $data
     * @param $use_master_key
     * @return array|mixed
     */
    private function http_delete($url,$data=array(),$use_master_key){
        $query = $comma = '';

        if($data){
            foreach($data as $k=>$v){
                $query .= $comma . $k . '=' .$v;
                $comma = '&';
            }
            $url .= $url . '?' . $query;
        }

        $app_key = $this->app_key;
        $use_master_key && $app_key = $this->master_key;

        $headers = array("X-LC-Id:$this->app_id","X-LC-Key:$app_key",'Content-Type:application/json');

        $ch = curl_init();
        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
        );
        curl_setopt_array($ch,$option);

        $r_data = curl_exec($ch);
        curl_close($ch);

        if($r_data){
            return json_decode($r_data);
        }else
            return array();
    }

}
