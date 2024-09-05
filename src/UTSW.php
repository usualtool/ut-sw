<?php
namespace usualtool\UTSW;
class UTSW{
    /*
    word/train_word词库位置 
	$utsw 1中文词库+自定义词库 2仅限自定义词库
	例：$utsw=new library\UTSW\UTSW(2);
    */
    public function __construct($utsw='1'){ 
	    $this->utsw=$utsw;
		$this->word="dict/word.utsw";
        $this->train_word="dict/word.train.utsw";
    }
    /*
    分隔并计算关键词
    $this->SplitWord($content,$title)
    输入一个标题及内容，标题可以为空
    得到3个数组，标题关键词组、与标题关键词组相关段落的关键词组、全文关键词组
    */
    public function SplitWord($content,$title=''){
		if($this->utsw==1):
            $word = file_get_contents($this->train_word).",".file_get_contents($this->word);
		else:
            $word = file_get_contents($this->train_word);
	    endif;
        $tags_array = explode(',', $word);
        $t_tags = array();
        $c_tags = array();
        $n_tags = array();
        $n_content = array();
        $content=$this->DeleteHtml($content);
        if(!empty($title)){
            $title=$this->DeleteHtml($title);
            foreach($tags_array as $t_tag) {
                if(strpos($title, $t_tag) !== false){
                    $t_tags[] = $t_tag;
                }
            }
            foreach($t_tags as $key) {
                $n_content[]=$this->WordInarray($key,$content);
            }
            $n_content=implode("|->|",array_unique($n_content));
            foreach($tags_array as $n_tag) {
                if(strpos($n_content, $n_tag) !== false){
                    $n_tags[] = $n_tag;
                }
            }
        }
        foreach($tags_array as $c_tag) {
            if(strpos($content, $c_tag) !== false){
                $c_tags[] = $c_tag;
            }
        }
        if(!empty($title)){
            $tags=array("t_tags"=>array_unique($t_tags),"n_tags"=>array_unique($n_tags),"c_tags"=>array_unique($c_tags));
        }else{
            $tags=array("c_tags"=>array_unique($c_tags));
        }
        return $tags;
    }
    /*
    查询关键词在文本中出现的位置并重新组成一个新的文本
    WordInarray($keyword,$content)
    */
    public function WordInarray($keyword,$content){
        $n_content=array();
        $content=$this->SplitContent($content);
        foreach($content as $value) {
           if(strpos($value,$keyword)!==false){
               $n_content[] =$value;
           }
        }
        return implode("|->|",$n_content);
    }
    /*
    从数组中提取一个长尾词
    FindLongWord($array)
    */
    public function FindLongWord($array){
        $longword="";
        $longlength=0;
        foreach($array as $element){
            $length=mb_strlen($element); 
            if($length>$longlength){
                $longlength=$length;
                $longword=$element;
            }
        }
        return $longword;
    }
    /*
    分隔文本段落
    SplitContent($content)
    */
    public function SplitContent($content){
        $symbol=array("，","。","？","！","……",",",".","!","?");
        $content=str_replace($symbol,"|->|",$content);
        $content=explode("|->|",$content);
        return $content;
    }
    /*
    清除文本中的HTML代码
    DeleteHtml($content)
    */
    public function DeleteHtml($content){
        $content = strip_tags($content,"");
        $content = str_replace(array("\r\n", "\r", "\n"), "", $content);   
        $content = str_replace("　","",$content);
        $content = str_replace("&nbsp;","",$content);
        $content = str_replace(" ","",$content);
        return ltrim(trim($content));
    }
    /*
    向自定义词库批量增加词组
    AddWordArr($keywords)
    $keywords数组
    添加成功返回1，有重复值返回0
    */
    public function AddWord($keywords){
        $words = file_get_contents($this->train_word);
        $intersect = array_intersect(explode(",",$words),$keywords);
        //存在重复不能加入
        if(!empty($intersect)){
            $word=implode(",",$intersect);
            $result=array("state"=>0,"words"=>$word);
        }else{
            $word = str_replace("--END--","".implode(",",$keywords).",--END--",$words);
            file_put_contents($this->train_word,$word);
            $result=array("state"=>1,"words"=>implode(",",$keywords));
        }
        return json_encode($result);
    }
    /*
    向自定义词库批量删除词组
    AddWordArr($keywords)
    $keywords数组
    添加成功返回1，有重复值返回0
    */
    public function DelWord($keywords){
        $words = file_get_contents($this->train_word);
        $data=array_diff(explode(",",$words), $keywords);
        file_put_contents($this->train_word,implode(",",$data));
        return json_encode(array("state"=>1));
    }
}
