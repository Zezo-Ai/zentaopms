<?php
/**
 * The control file of cache module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2024 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@chandao.com>
 * @package     cache
 * @link        https://www.zentao.net
 */
class cache extends control
{
    /**
     * 设置是否启用缓存。
     * Set cache enable.
     *
     * @access public
     * @return void
     */
    public function setting()
    {
        if($_POST)
        {
            $redis = null;
            $cache = form::data()->get();
            if(isset($cache->redis))
            {
                $redis = (object)$cache->redis;
                unset($cache->redis);
            }
            if(!isset($redis->host))       $redis->host       = '';
            if(!isset($redis->port))       $redis->port       = '';
            if(!isset($redis->username))   $redis->username   = '';
            if(!isset($redis->password))   $redis->password   = '';
            if(!isset($redis->database))   $redis->database   = '';
            if(!isset($redis->serializer)) $redis->serializer = '';

            if($cache->enable)
            {
                $errors = [];
                if(empty($cache->driver))    $errors['driver']    = sprintf($this->lang->error->notempty, $this->lang->cache->driver);
                if(empty($cache->scope))     $errors['scope']     = sprintf($this->lang->error->notempty, $this->lang->cache->scope);
                if(empty($cache->namespace)) $errors['namespace'] = sprintf($this->lang->error->notempty, $this->lang->cache->namespace);
                if($cache->driver == 'redis' && empty($redis->host)) $errors['redis[host]'] = sprintf($this->lang->error->notempty, $this->lang->cache->redis->host);
                if($cache->driver == 'redis' && empty($redis->port)) $errors['redis[port]'] = sprintf($this->lang->error->notempty, $this->lang->cache->redis->port);
                if($cache->driver == 'redis' && empty($redis->serializer)) $errors['redis[serializer]'] = sprintf($this->lang->error->notempty, $this->lang->cache->redis->serializer);
                if($cache->driver == 'redis' && empty($redis->database) && $redis->database !== '0') $errors['redis[database]'] = sprintf($this->lang->error->notempty, $this->lang->cache->redis->database);
                if($errors) return $this->send(array('result' => 'fail', 'message' => $errors));

                if($cache->driver == 'apcu')
                {
                    /* 检查是否加载了 APCu 扩展。Check if the APCu extension is loaded. */
                    if(!extension_loaded('apcu')) return $this->send(array('result' => 'fail', 'message' => $this->lang->cache->apcu->notLoaded));
                    if(!ini_get('apc.enabled')) return $this->send(array('result' => 'fail', 'message' => $this->lang->cache->apcu->notEnabled));
                }
                if($cache->driver == 'redis')
                {
                    /* 检查是否加载了 Redis 扩展。Check if the Redis extension is loaded. */
                    if(!extension_loaded('redis')) return $this->send(array('result' => 'fail', 'message' => $this->lang->cache->redis->notLoaded));
                    if($redis->serializer == 'igbinary')
                    {
                        if(!extension_loaded('igbinary')) return $this->send(array('result' => 'fail', 'message' => $this->lang->cache->redis->igbinaryNotLoaded));
                        $reflection = new ReflectionClass(new Redis());
                        if(!$reflection->hasConstant('SERIALIZER_IGBINARY')) return $this->send(array('result' => 'fail', 'message' => $this->lang->cache->redis->igbinaryNotSupported));
                    }

                    /* 检查 Redis 连接是否正常。Check if the Redis connection is normal. */
                    try
                    {
                        helper::connectRedis($redis);
                    }
                    catch(Exception $e)
                    {
                        return $this->send(array('result' => 'fail', 'message' => $e->getMessage()));
                    }
                }
            }

            $this->loadModel('setting')->setItems('system.common.cache', $cache);
            $this->setting->setItems('system.common.redis', $redis);

            $this->cache->clear($cache->enable);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => true));
        }

        if($this->config->cache->enable)
        {
            $this->view->rate  = $this->mao->memory('rate');
            $this->view->used  = $this->mao->memory('used');
            $this->view->total = $this->mao->memory('total');
        }

        $this->view->title = $this->lang->cache->common;
        $this->display();
    }

    /**
     * 清除数据缓存。
     * Clear data cache.
     *
     * @access public
     * @return void
     */
    public function flush()
    {
        $this->cache->clear($this->config->cache->enable);
        return $this->send(array('result' => 'success', 'message' => $this->lang->cache->clearSuccess, 'load' => true));
    }
}
