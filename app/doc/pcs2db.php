<?php
/*
转换pcs 到数据库格式

*/
require_once "../path.php";
require_once "../public/_pdo.php";
require_once "../public/function.php";

echo "<h2>转换pcs 到数据库格式</h2>";
$dir = _DIR_USER_BASE_.'/'.$_COOKIE["userid"]._DIR_MYDOCUMENT_;
PDO_Connect("sqlite:"._FILE_DB_FILEINDEX_);
$query = "select file_name, doc_info, modify_time from fileindex where id='".$_GET["doc_id"]."' ";
$Fetch = PDO_FetchAll($query);

if(count($Fetch)>0){
    $file_modify_time = $Fetch[0]["modify_time"];
    if(empty($Fetch[0]["doc_info"])){
        $file = $dir.'/'.$Fetch[0]["file_name"];    
    }
    else{
        echo "已经是数据库格式了。无需转换";
    }
}
else{
    echo "文件不存在";
    exit;    
}

$xml = simplexml_load_file($file);
$xml_head = $xml->xpath('//head')[0];
$strHead = "<head>";
$strHead .= "<type>{$xml_head->type}</type>";
$strHead .= "<mode>{$xml_head->mode}</mode>";
$strHead .= "<ver>{$xml_head->ver}</ver>";
$strHead .= "<toc>{$xml_head->toc}</toc>";
$strHead .= "<style>{$xml_head->style}</style>";
$strHead .= "<doc_title>{$xml_head->doc_title}</doc_title>";
$strHead .= "<tag>{$xml_head->tag}</tag>";
$strHead .= "<book>{$xml_head->book}</book>";
$strHead .= "<paragraph>{$xml_head->paragraph}</paragraph>";
$strHead .= "</head>";

$dataBlock = $xml->xpath('//block');


{

    //复制数据
    //打开逐词解析数据库
    $dns = "sqlite:"._FILE_DB_USER_WBW_;
    $dbhWBW = new PDO($dns, "", "",array(PDO::ATTR_PERSISTENT=>true));
    $dbhWBW->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  

    //打开译文数据库
    $dns = "sqlite:"._FILE_DB_SENTENCE_;
    $dbhSent = new PDO($dns, "", "",array(PDO::ATTR_PERSISTENT=>true));
    $dbhSent->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 

    //逐词解析新数据数组
    $arrNewBlock = array();
    $arrNewBlockData = array();
    $arrBlockTransform = array();
    
    //译文新数据数组
    $arrSentNewBlock = array();
    $arrSentNewBlockData = array();
    $arrSentBlockTransform = array(); 

    $newDocBlockList=array();
    foreach($dataBlock as $block){
        switch($block->info->type){
            case "translate":
                echo "wbw:".$block->info->book."<br>";
            break;
            case "wbw":
                echo "translate:".$block->info->book."<br>";
            break;
        }
    
    }

    foreach($dataBlock as $block) {
        switch($block->info->type){
            case 1:
            break;
            case "translate":
                //译文
                $blockid = sprintf("%s",$block->info->id);
                $newDocBlockList[]=array('type' => 2,'block_id' => $blockid);
                $arrSentBlockTransform["{$blockid}"] = $blockid;
                //if(count($fBlock)>0)
                {
                    array_push( $arrSentNewBlock,array($blockid,
                                                    "",
                                                    $block->info->book,
                                                    $block->info->paragraph,
                                                    $_COOKIE["userid"],
                                                    $block->info->language,
                                                    $block->info->author,
                                                    "",
                                                    "",
                                                    "1",
                                                    $file_modify_time,
                                                    mTime()
                                                ));
                }
                foreach($block->data->children() as $sen){
                    if(isset($sen->begin)){
                        $sent_begin=$sen->begin;
                    }
                    else{
                        $sent_begin="";
                    }
                    if(isset($sen->end)){
                        $sent_end=$sen->end;
                    }
                    else{
                        $sent_end="";
                    }
                    if(isset($sen->text)){
                        $paraText=$sen->text;
                        if( $block->info->level>0 &&  $block->info->level<8){
                            $toc.=$sen->text;
                        }
                    }
                    array_push( $arrSentNewBlockData,array(UUID::V4(),
                                    $blockid,
                                    $block->info->book,
                                    $block->info->paragraph,
                                    $sent_begin,
                                    $sent_end,
                                    "",
                                    $block->info->author,
                                    $_COOKIE["userid"],
                                    $paraText,
                                    $block->info->language,
                                    "1",
                                    "7",
                                    $file_modify_time,
                                    mTime()
                                ));
                }
            break;
            case "wbw":
                $blockid = sprintf("%s",$block->info->id);
                $newDocBlockList[]=array('type' => 6,'block_id' => $blockid);
                $arrBlockTransform["{$blockid}"] = $blockid;
                {
                    array_push( $arrNewBlock,array($blockid,
                                                    "",
                                                    $_COOKIE["userid"],
                                                    $block->info->book,
                                                    $block->info->paragraph,
                                                    "",
                                                    $block->info->language,
                                                    "",
                                                    $file_modify_time,
                                                    mTime()
                                                ));
                }

                $currWordId = "";
                $currWordReal = "";
                $currWordStatus = "";
                $sWordData = "";
                $iWordCount = 0;
                foreach($block->data->children() as $word){
                    $word_id = explode("-",$word->id)[2];
                    $arrWordId = explode("_",$word_id);
                    $realWordId = $arrWordId[0];
                    if($realWordId != $currWordId){
                        if($iWordCount>0){
                            array_push( $arrNewBlockData,array(UUID::V4(),
                                $blockid,
                                $block->info->book,
                                $block->info->paragraph,
                                $currWordId,
                                $currWordReal,
                                $sWordData,
                                $file_modify_time,
                                mTime(),
                                $currWordStatus,
                                $_COOKIE["userid"]
                            ));
                            $sWordData = "";
                        }
                        $currWordId = $realWordId;
                        $currWordReal = $word->real;
                        $currWordStatus = $word->status;
                    }
                    
                    $sWordData .= "<word>";
                    $sWordData .= "<pali>{$word->pali}</pali>";
                    $sWordData .= "<real>{$word->real}</real>";
                    $sWordData .= "<id>{$word->id}</id>";
                    $sWordData .= "<type status=\"0\">{$word->type}</type>";
                    $sWordData .= "<gramma status=\"0\">{$word->gramma}</gramma>";
                    $sWordData .= "<mean status=\"0\">{$word->mean}</mean>";
                    $sWordData .= "<org status=\"0\">{$word->org}</org>";
                    $sWordData .= "<om status=\"0\">{$word->om}</om>";
                    $sWordData .= "<case status=\"0\">{$word->case}</case>";
                    $sWordData .= "<note status=\"0\">{$word->note}</note>";
                    $sWordData .= "<style>{$word->style}</style>";
                    $sWordData .= "<status>{$word->status}</status>";
                    $sWordData .= "<parent>{$word->parent}</parent>";
                    if(isset($word->bmc)){
                        $sWordData .= "<bmc>{$word->bmc}</bmc>";
                    }
                    if(isset($word->bmt)){
                        $sWordData .= "<bmt>{$word->bmt}</bmt>";
                    }
                    if(isset($word->un)){
                        $sWordData .= "<un>{$word->un}</un>";
                    }
                    if(isset($word->lock)){
                        $sWordData .= "<lock>{$word->lock}</lock>";
                    }
                    $sWordData .= "</word>";


                     $iWordCount++;
                }
                array_push( $arrNewBlockData,array(UUID::V4(),
                $blockid,
                $block->info->book,
                $block->info->paragraph,
                $word_id,
                $word->real,
                $sWordData,
                $file_modify_time,
                mTime(),
                $word->status,
                $_COOKIE["userid"]
            ));
            break;
            case 2:

            break;
        }

    }
    
    //逐词解析block数据块


    if(count($arrNewBlock)>0){
        $dbhWBW->beginTransaction();
        $query="INSERT INTO wbw_block ('id','parent_id','owner','book','paragraph','style','lang','status','modify_time','receive_time') VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmtNewBlock = $dbhWBW->prepare($query);
        foreach($arrNewBlock as $oneParam){
            $stmtNewBlock->execute($oneParam);
        }
        // 提交更改 
        $dbhWBW->commit();
        if (!$stmtNewBlock || ($stmtNewBlock && $stmtNewBlock->errorCode() != 0)) {
            $error = $dbhWBW->errorInfo();
            echo "error - $error[2] <br>";
        }
        else{
            //逐词解析block块复刻成功
            $count=count($arrNewBlock);
            echo "wbw block $count recorders.<br/>";                  
        }
    }

    if(count($arrNewBlockData)>0){
        // 开始一个事务，逐词解析数据 关闭自动提交
        $dbhWBW->beginTransaction();
        $query="INSERT INTO wbw ('id','block_id','book','paragraph','wid','word','data','modify_time','receive_time','status','owner') VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmtWbwData = $dbhWBW->prepare($query);
        foreach($arrNewBlockData as $oneParam){
            $stmtWbwData->execute($oneParam);
        }
        // 提交更改 
        $dbhWBW->commit();
        if (!$stmtWbwData || ($stmtWbwData && $stmtWbwData->errorCode() != 0)) {
            $error = $dbhWBW->errorInfo();
            echo "error - $error[2] <br>";
        }
        else{
            //逐词解析 数据 复刻成功
            $count=count($arrNewBlockData);
            echo "new wbw $count recorders.";
        }   
    }
    
    
    //译文 block数据块
    
    if(count($arrSentNewBlock)>0){
        $dbhSent->beginTransaction();
        $query="INSERT INTO sent_block ('id','parent_id','book','paragraph','owner','lang','author','editor','tag','status','modify_time','receive_time') VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmtSentNewBlock = $dbhSent->prepare($query);
        foreach($arrSentNewBlock as $oneParam){
            //print_r($oneParam);
            $stmtSentNewBlock->execute($oneParam);
        }
        // 提交更改
        $dbhSent->commit();
        if (!$stmtSentNewBlock || ($stmtSentNewBlock && $stmtSentNewBlock->errorCode() != 0)){
            $error = $dbhSent->errorInfo();
            echo "error - $error[2] <br>";
        }
        else{
            //译文 block块复刻成功
            $count=count($arrNewBlock);
            echo "wbw block $count recorders.<br/>";                  
        }
    }

    if(count($arrSentNewBlockData)>0){
        // 开始一个事务，逐词解析数据 关闭自动提交
        $dbhSent->beginTransaction();
        $query="INSERT INTO sentence ('id','block_id','book','paragraph','begin','end','tag','author','editor','text','language','ver','status','modify_time','receive_time') VALUES (? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ?, ?)";
        $stmtSentData = $dbhSent->prepare($query);
        foreach($arrSentNewBlockData as $oneParam){
            $stmtSentData->execute($oneParam);
        }
        // 提交更改 
        $dbhSent->commit();
        if (!$stmtSentData || ($stmtSentData && $stmtSentData->errorCode() != 0)) {
            $error = $dbhSent->errorInfo();
            echo "error - $error[2] <br>";
        }
        else{
            //译文 数据 复刻成功
            $count=count($arrSentNewBlockData);
            echo "new translation $count recorders.";
        }   
    }
    

    //插入记录到文件索引
    $filesize=0;
    //服务器端文件列表
    PDO_Connect("sqlite:"._FILE_DB_FILEINDEX_);
    $query="INSERT INTO fileindex ('id',
                                   'parent_id',
                                   'user_id',
                                   'book',
                                   'paragraph',
                                   'file_name',
                                   'title',
                                   'tag',
                                   'status',
                                   'create_time',
                                   'modify_time',
                                   'accese_time',
                                   'file_size',
                                   'share',
                                   'doc_info',
                                   'doc_block',
                                   'receive_time'
                                   ) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $PDO->prepare($query);
    $query="UPDATE 'fileindex' SET 'doc_info' = ? , 'doc_block' = ?  WHERE id = ? ";
	$stmt = $PDO->prepare($query);
    $newData=array(
                   $strHead,
                   json_encode($newDocBlockList, JSON_UNESCAPED_UNICODE), 
                   $_GET["doc_id"]
                );
    $stmt->execute($newData);
    if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
        $error = PDO_ErrorInfo();
        echo "error - $error[2] <br>";
    }
    else{
        //文档列表插入成功
        echo "doc list updata 1 recorders.";
        echo "<a href='../studio/editor.php?op=opendb&doc_id={$_GET["doc_id"]}'>在编辑器中打开</a>";
    }          
                
}
?>