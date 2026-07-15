<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $retval=mysqli_query($conn,$sql);
    if(mysqli_num_rows($retval) !== 1){ 
        echo 500;
    }else{
        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
            $vip=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
        }
        if($vip==4){
            if(!isset($_POST['id'])){
              $_POST['id']="";
            }
            if(!isset($_POST['name'])){
              $_POST['name']="";
            }
            $id = trim($_POST['id']);  
            $name = $_POST['name'];  
            $name = trim($name); // 去除首尾空格  
            $name = str_replace(["，", "，", "\r\n", "\n", "\r"], [",", ",", "", "", ""], $name); // 替换逗号和换行符  
            $namearr = explode(",", $name); // 转换为以英文逗号分割的数组  

if(is_array($namearr)&&count($namearr)>1){
                
if(empty($id) || $id<1 || !is_numeric($id)){
    echo 500;
    exit;
}

// 检查是否有重复项  
$duplicates = [];  
$namesToInsert = [];  
$currentName = '';
foreach ($namearr as $index => $nameItem) {  
    $newname = htmlspecialchars(trim(str_replace(array(" ", "\t", "\n", "\r", "'", '"'), '', $nameItem))); // 过滤特殊字符和制表符
    if (empty($newname)) {  
        continue; // 跳过空项
    }
    if ($newname != $currentName) {
        $notsql = "SELECT * FROM ppz_fl WHERE flname=? AND fllinkid=?";  
    $stmt = mysqli_prepare($conn, $notsql);  
    mysqli_stmt_bind_param($stmt, 'si', $newname, $id);  
    mysqli_stmt_execute($stmt);  
    $result = mysqli_stmt_get_result($stmt);  
    if (mysqli_num_rows($result) > 0) {  
        $duplicates[$newname] = true; // 记录重复项  
    } else {  
        if (!isset($duplicates[$newname]) && !in_array($newname, $namesToInsert)) {
            $currentName = $newname; 
            $namesToInsert[] = ['index' => $index, 'name' => $newname]; // 保存顺序和名称
        }
    } 
    mysqli_stmt_close($stmt);  
    }else{
        continue; // 跳过重复项
    }
    
}  
  
// 如果有重复项，输出错误并退出  
if (!empty($duplicates)) {  
    echo 402; // 重复项错误  
    exit;  
}  
  
// 批量插入数据库
if (!empty($namesToInsert)) {  
    usort($namesToInsert, function ($a, $b) {
        //return $b['index'] - $a['index']; // 使用降序排序
        return $a['index'] - $b['index']; // 使用升序排序
    });
$INSERTsql = "INSERT INTO ppz_fl (flname, fllinkid) VALUES ";  
$values = [];
foreach ($namesToInsert as $item) {  
    $values[] = "('$item[name]', $id)"; 
} 
$INSERTsql .= implode(',', $values);  
if (mysqli_query($conn, $INSERTsql)) {  
    echo 200; // 插入成功  
} else {  
    echo 600; // 插入失败  
}
}else{
    echo 500;
}
            }else{
                if(!empty($id) && $id>0 && is_numeric($id)){
                    $namez = trim($name);//去除首尾空格
                    if (!empty($name)&& !empty($namez)){
                        $newname = htmlspecialchars(str_replace(array(" ", "\n", "\r", "'", '"'), '', trim($name)));//过滤特殊字符
                        //判断是否存在重复项
                        $notsql="SELECT * FROM ppz_fl WHERE flname='$newname' AND fllinkid=$id";
                        $notsqlresult=mysqli_query($conn,$notsql);
                        if(mysqli_num_rows($notsqlresult)>0){
                            echo 400;
                            exit;
                        }
    
                        $newsql="INSERT INTO ppz_fl (flname,fllinkid) VALUES ('$newname',$id)";
                        if(mysqli_query($conn,$newsql)){
                            echo 200;
                        }else{
                            echo 600;
                        }
                    }else{
                        echo 404;
                    }
    
                }else{
                    echo 500;
                }
            }
    

        }else{
            echo 500;
        }
    }
}
?>