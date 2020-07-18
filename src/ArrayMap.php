<?php

declare(strict_types=1);

namespace axios\tools;

class ArrayMap implements \ArrayAccess
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var string
     */
    private $separator;

    public function __construct(array $array = [], string $separator = '.')
    {
        $this->array     = $array;
        $this->separator = $separator;
    }

    /**
     * 数组过滤.
     *
     * @desc 可自定义排除过滤
     *
     * @param        $array
     * @param string $except
     * @param bool   $reset_key 是否重置键名
     *
     * @return mixed
     */
    public function filter(array $array, string $except = '', bool $reset_key = false)
    {
        // $except = 'number|null|string'

        $except = explode('|', $except);
        if (empty($except)) {
            $array = array_filter($array);

            return $reset_key ? array_values($array) : $array;
        }

        foreach ($array as $k => $v) {
            if (is_numeric($v) && \in_array('number', $except)) {
                continue;
            }

            if (\is_string($v) && \in_array('string', $except)) {
                continue;
            }

            if (null === $v && \in_array('null', $except)) {
                continue;
            }
            if (empty($v)) {
                unset($array[$k]);
            }
        }

        return $reset_key ? array_values($array) : $array;
    }

    /**
     * 设置任意层级子元素.
     *
     * @param array|int|string $key
     * @param mixed            $value
     *
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (\is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            $keyArray    = $this->filter(explode($this->separator, $key), 'number', true);
            $this->array = $this->recurArrayChange($this->array, $keyArray, $value);
        }

        return $this;
    }

    /**
     * 获取任意层级子元素.
     *
     * @param null|int|string $key
     * @param mixed           $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return $this->array;
        }

        if (false === strpos($key, $this->separator)) {
            return isset($this->array[$key]) ? $this->array[$key] : $default;
        }

        $keyArray = explode($this->separator, $key);
        $tmp      = $this->array;
        foreach ($keyArray as $k) {
            if (isset($tmp[$k])) {
                $tmp = $tmp[$k];
            } else {
                $tmp = $default;

                break;
            }
        }

        return $tmp;
    }

    /**
     * 删除任意层级子元素.
     *
     * @param array|int|string $key
     *
     * @return $this
     */
    public function delete($key)
    {
        if (\is_array($key)) {
            foreach ($key as $k) {
                $this->set($k, null);
            }
        } else {
            $this->set($key, null);
        }

        return $this;
    }

    /**
     * 数据排序.
     *
     * @param string $key
     * @param array  $sortRule example : ['filed-name'=>SORT_ASC,'filed-name'=>SORT_DESC]
     *
     * @return $this
     */
    public function sort(string $key = null, array $sortRule = [])
    {
        $data = $this->get($key);
        if (!\is_array($data)) {
            throw new \InvalidArgumentException('Invalid data. Only supported array.');
        }
        if (0 === \count($sortRule)) {
            throw new \InvalidArgumentException('Invalid sort rule.');
        }
        $params = [];
        foreach ($sortRule as $key => $value) {
            $params[] = $key;
            switch ($value) {
                case SORT_DESC:
                case 'desc':
                    $params[] = SORT_DESC;

                    break;
                default:
                    $params[] = SORT_ASC;
            }
        }

        foreach ($params as $key => $field) {
            if (\is_string($field)) {
                $item = [];
                foreach ($data as $k => $value) {
                    $item[$k] = $value[$field];
                }
                $params[$key] = $item;
            }
        }
        $params[] =&$data;
        \call_user_func_array('array_multisort', $params);

        return array_pop($params);
    }

    /**
     * 获取某一节点下的子元素key列表.
     *
     * @param $key
     *
     * @return array
     */
    public function getChildKeyList($key = null)
    {
        $child = $this->get($key);
        $list  = [];
        $n     = 0;
        foreach ($child as $k => $v) {
            $list[$n++] = $k;
        }

        return $list;
    }

    /**
     * isset($array[$key]).
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return null !== $this->get($offset);
    }

    /**
     * $array[$key].
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * $array[$key] = $value.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * unset($array[$key]).
     *
     * @param mixed $offset
     *
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }

    /**
     * 递归遍历.
     *
     * @param array $array
     * @param array $keyArray
     * @param mixed $value
     *
     * @return array
     */
    private function recurArrayChange($array, $keyArray, $value = null)
    {
        $key0 = $keyArray[0];
        if (1 === \count($keyArray)) {
            $this->changeValue($array, $key0, $value);
        } elseif (\is_array($array) && isset($keyArray[1])) {
            unset($keyArray[0]);
            $keyArray = array_values($keyArray);
            if (!isset($array[$key0])) {
                $array[$key0] = [];
            }
            $array[$key0] = $this->recurArrayChange($array[$key0], $keyArray, $value);
        } else {
            $this->changeValue($array, $key0, $value);
        }

        return $array;
    }

    private function changeValue(&$array, $key, $value)
    {
        if (null === $value) {
            unset($array[$key]);
        } else {
            $array[$key] = $value;
        }
    }
}
