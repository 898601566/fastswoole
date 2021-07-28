<?php

interface Component
{

    public function operation();
}

abstract class Decorator implements Component
{ // 装饰角色 

    protected $_component;

    public function __construct(Component $component)
    {
        $this->_component = $component;
    }

    public function operation()
    {
        $this->_component->operation();
    }

}

class ConcreteDecoratorA extends Decorator
{ // 具体装饰类A

    public function __construct(Component $component)
    {
        parent::__construct($component);
    }

    public function operation()
    {
        parent::operation();    //  调用装饰类的操作
        $this->addedOperationA();   //  新增加的操作
    }

    public function addedOperationA()
    {
        echo 'A类装甲装备完成;<br>';
    }

}

class ConcreteDecoratorB extends Decorator
{ // 具体装饰类B

    public function __construct(Component $component)
    {
        parent::__construct($component);
    }

    public function operation()
    {
        parent::operation();
        $this->addedOperationB();
    }

    public function addedOperationB()
    {
        echo "B类武器装备完成;<br>";
    }

}

class ConcreteComponent implements Component
{ //具体组件类

    public function operation()
    {
        echo "高达原型机准备就绪;<br>";
    }

}
