<?php

namespace Topxia\Common;


class Tree
{

    /**
     * @var array<Tree>
     */
    private $children = array();

    /**
     * @var Tree
     */
    private $parent;

    /**
     * @var mixed
     */
    public $data;

    public function __construct($data = array(), Tree $parent = null)
    {
        $this->data   = $data;
        $this->parent = $parent;
    }

    /**
     * Like ArrayToolkit::column
     * @param $key
     * @return mixed
     */
    public function column($key)
    {
        return $this->reduce(function ($ret, $tree) use ($key){
            if(!empty($tree->data[$key])){
                array_push($ret, $tree->data[$key]);
            }
            return $ret;
        }, array());
    }

    /**
     * each Tree Node call closure
     * @param \Closure $closure
     * @return $this
     */
    public function each(\Closure $closure)
    {
        $closure($this);
        foreach ($this->getChildren() as $child) {
            $child->each($closure);
        }

        return $this;
    }

    /**
     * Like array_reduce
     * @see http://php.net/manual/zh/function.array-reduce.php
     * @param \Closure $closure
     * @param null     $initial
     * @return mixed
     */
    public function reduce(\Closure $closure, $initial = null)
    {
        is_null($initial) ? $ret = $this : $ret = $initial;

        $ret = $closure($ret ,$this);

        foreach ($this->children as $child){
            $ret = $child->reduce($closure, $ret);
        }

        return $ret;
    }

    /**
     * @return array<type($this->data)>
     */
    public function toArray()
    {
        $ret = $this->data;
        $ret['children'] = array();

        foreach ($this->getChildren() as $child) {
            array_push($ret['children'], $child->toArray());
        }

        return $ret;

        /*return $this->reduce(function ($ret, $tree){
            $array = $tree->data;
            $array['children'] = array()
            return $ret;
        });*/
    }

    /**
     * @param \Closure $closure
     * @return Tree
     */
    public function find(\Closure $closure)
    {
        if($closure($this)){
            return $this;
        }
        $ret = null;
        foreach ($this->children as $child){
            $ret = $child->find($closure);
            if(!is_null($ret)){
                break;
            }
        }

        return $ret;
    }

    public static function buildWithArray(array $input, $rootId = 0, $key = 'id', $parentKey = 'parentId')
    {
        /* 过滤掉构建不出树的元素
         * $ids = ArrayToolkit::column($input, $key);
        $input = array_filter($input, function ($array) use ($parentKey, $ids){
            return in_array($array[$parentKey], $ids, true);
        });*/

        $root = new self(array(
            $key => $rootId
        ));

        // 方便找到父节点
        $map = array(
            $rootId => $root
        );

        $buildingArray = $input;

        while (!empty($buildingArray)) {
            foreach ($buildingArray as $value) {
                if (isset($map[$value[$parentKey]])) {
                    $tree = new Tree($value);
                    $map[$value[$key]] = $tree;
                    $parent = $map[$value[$parentKey]];
                    $parent->addChild($tree);
                    $tree->setParent($parent);
                }
            }

            $buildingArray = array_filter($buildingArray, function ($array) use ($map, $key, $parentKey) {
                return !in_array($array[$key], array_keys($map), true);
            });
        }

        return $root;
    }

    /**
     * @param Tree $child
     * @return $this
     */
    public function addChild(Tree $child)
    {
        array_push($this->children, $child);
        return $this;
    }

    /**
     * @return array<Tree>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Tree
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Tree $parent
     * @return $this
     */
    public function setParent(Tree $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function hasChildren()
    {
        return !empty($this->children);
    }
}