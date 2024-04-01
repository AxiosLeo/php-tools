<?php

declare(strict_types=1);

namespace axios\tools;

class ArrayMap implements \ArrayAccess
{
    private array $array;

    private string $separator;

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
     * @param array  $array     get data by key
     * @param string $except    except number(0)|null|string('')
     * @param bool   $reset_key 是否重置键名
     */
    public function filter(array $array, string $except = 'null|string', bool $reset_key = false): array
    {
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
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value = null): self
    {
        if (\is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            $recurArrayChange = function ($array, $keyArr, $value) use (&$recurArrayChange) {
                $key = array_shift($keyArr);
                if (null === $key) {
                    return $value;
                }
                if (0 === \count($keyArr)) {
                    // is last
                    if (null === $value) {
                        unset($array[$key]);

                        return $array;
                    }
                    $array[$key] = $value;

                    return $array;
                }
                if (!isset($array[$key])) {
                    $array[$key] = [];
                }
                $array[$key] = $recurArrayChange($array[$key], $keyArr, $value);

                return $array;
            };

            $keyArray    = explode($this->separator, trim($key, ' .'));
            $this->array = $recurArrayChange($this->array, $keyArray, $value);
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

        if (!str_contains($key, $this->separator)) {
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
     * @return mixed
     */
    public function getAllToString(array $map = [])
    {
        $map     = array_merge(['false' => 'false', 'true' => 'true'], $map);
        $recurse = function (&$array = []) use (&$recurse, $map) {
            if (\is_array($array)) {
                foreach ($array as &$a) {
                    if (\is_array($a)) {
                        $a = $recurse($a);
                    }
                    if (\is_int($a)) {
                        $a = (string) $a;
                    }
                    if (true === $a || false === $a) {
                        $a = $a ? $map['true'] : $map['false'];
                    }
                    if (null === $a) {
                        $a = '';
                    }
                }
            } elseif (\is_int($array)) {
                $array = (string) $array;
            } elseif (null === $array) {
                $array = '';
            } elseif (true === $array || false === $array) {
                $array = $array ? $map['true'] : $map['false'];
            }

            return $array;
        };

        $arr = $this->get();
        $recurse($arr);

        return $arr;
    }

    /**
     * 删除任意层级子元素.
     *
     * @param array|int|string $key
     *
     * @return $this
     */
    public function delete($key): self
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
     * @param null|string $key      get data by key
     * @param array       $sortRule example : ['filed-name'=>SORT_ASC,'filed-name'=>SORT_DESC]
     *
     * @return $this
     */
    public function sort(string $key = null, array $sortRule = []): mixed
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
                case \SORT_DESC:
                case 'desc':
                    $params[] = \SORT_DESC;

                    break;

                default:
                    $params[] = \SORT_ASC;
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
     * @param null $key
     */
    public function getChildKeyList($key = null): array
    {
        return array_keys($this->get($key));
    }

    /**
     * isset($array[$key]).
     *
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
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
    public function offsetGet($offset): array
    {
        return $this->get($offset);
    }

    /**
     * $array[$key] = $value.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * unset($array[$key]).
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}
