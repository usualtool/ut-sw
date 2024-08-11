<?php
require_once dirname(__FILE__).'/'.'autoload.php';
use usualtool\UTSW\UTSW;
$utsw=new UTSW(2);//1中文词库+自定义词库；2自定义词库
$title = "UT框架是什么";
$content="UT框架是基于PHP的多端开发框架，类库完善，适合开发各种类型的应用。UT框架内置几乎所有关系数据库或非关系数据库的类库，拥有可自定义的模板引擎、语言本地化解析器及各种函数库。轻便简易的开发模式使开发者更容易理解流程、上手开发。使用UT虽然需要PHP基础知识，但更多的是对UT函数方法的调用，这将节省更多的开发时间。";
print_r($utsw->SplitWord($content,$title));
//添加自定义关键词：数组
//$utsw->AddWord(array("UT框架"));
//删除自定义关键词：数组
//$utsw->DelWord(array("UT框架"));