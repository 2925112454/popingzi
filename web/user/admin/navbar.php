<?php
if ($admin==1 && $typeuser==2 && $allvip==4  && !empty($ppzusername)){
    $navsql="SELECT * FROM ppz_link order by linkid asc";//获取导航栏菜单(即一级分类)
    $navresult=mysqli_query($conn,$navsql);
    $navsize = mysqli_num_rows($navresult);
    if($navsize>0){
        $naviderr=1;
    }else{
        $naviderr=0;
    }
    if(!isset($_GET["nid"])){
        $_GET["nid"]=""; 
    }
    $idnav=trim($_GET["nid"]);
if(empty($idnav)){
    echo '<div class="user-h1">分类导航</div>';
}else{
    echo '<div class="user-h1">编辑列表<a href="?type=2"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
}
if(empty($idnav)){
    echo '<div class="navcontent">
    <div class="nav-add">
    <div class="nav-select">列表图标：<input id="iconname" type="text" placeholder="如：fa-home" /></div>
    <span>* 输入：<a href="https://fontawesome.com/v4/icons/" target="_blank">FontAwesome-V4图标名称</a>或直接写入图片地址</span>
    </div>
    <div class="nav-add">
    <div class="nav-select">列表名称：<input id="navname" type="text" placeholder="如：首页" /></div>
    <div class="nav-select">文章封面类别：<select id="navimg"><option value="1">竖屏</option><option value="2">横屏</option><option value="3">资讯类</option></select></div>
    <div class="nav-select">单行封面数量：<select id="navint"><option value="1">4篇</option><option value="2">3篇</option></select></div>
    </div>
    <div class="nav-addbut"><button id="addnav"><i class="fa fa-plus" aria-hidden="true"></i>添加列表</button></div>
    <script src="/style/js/newnav.js" type="text/javascript"></script>
</div>';
}else{
    if (is_numeric($idnav)){//判断是否为数字
        $newnavid=round((float)$idnav);
        $newnavidsql="SELECT * FROM ppz_link WHERE linkid=$newnavid order by linkid asc";//获取导航栏菜单(即一级分类)
        $newnavidsqlresult=mysqli_query($conn,$newnavidsql);
        $newnavidsize = mysqli_num_rows($newnavidsqlresult);
        if ($newnavidsize!==1){
            echo '<div class="navcontent redborder textcenter">错误操作！</div>';
        }else{
            while($newnavidrow = $newnavidsqlresult->fetch_array()){
                $newnavidname=$newnavidrow['linkname'];//获取导航栏菜单名称
                $newnavidico=$newnavidrow['linkico'];//获取导航栏菜单图标
                $newnavidimg=$newnavidrow['linkimg'];//获取导航栏菜单封面类别
                $newnavidint=$newnavidrow['linkint'];//获取导航栏菜单单行数量
            }
            if($newnavidimg==1){
                $newnavidimg1="selected";
                $newnavidimg2="";
                $newnavidimg3="";
            }elseif($newnavidimg==2){
                $newnavidimg1="";
                $newnavidimg2="selected";
                $newnavidimg3="";
            }else{
                $newnavidimg1="";
                $newnavidimg2="";
                $newnavidimg3="selected";
            }
            if($newnavidint==1){
                $newnavidint1="selected";
                $newnavidint2="";
            }else{
                $newnavidint1="";
                $newnavidint2="selected";
            }
            echo '
            <div class="navcontent redborder">
            <div class="nav-add">
            <div class="nav-select">列表图标：<input id="iconname" value="'.$newnavidico.'" type="text" placeholder="如：fa-home" /></div>
            <span>* 输入：<a href="https://fontawesome.com/v4/icons/" target="_blank">FontAwesome-V4图标名称</a>或直接写入图片地址</span>
            </div>
            <div class="nav-add">
            <div class="nav-select">列表名称：<input id="navname" type="text" value="'.$newnavidname.'" placeholder="如：首页" /></div>
            <div class="nav-select">文章封面类别：<select id="navimg"><option value="1" '.$newnavidimg1.'>竖屏</option><option value="2" '.$newnavidimg2.'>横屏</option><option value="3" '.$newnavidimg3.'>资讯类</option></select></div>
            <div class="nav-select">单行封面数量：<select id="navint"><option value="1" '.$newnavidint1.'>4篇</option><option value="2" '.$newnavidint2.'>3篇</option></select></div>
            </div>
            <div class="nav-addbut"><button id="addnav" data-id="'.$newnavid.'"><i class="fa fa-plus" aria-hidden="true"></i>修改列表</button></div>
            <script src="/style/js/newnavtow.js" type="text/javascript"></script>
            </div>';
        }
 
    }else{
        echo '<div class="navcontent redborder textcenter">错误操作！</div>';
    }
}

if(is_null($idnav)||$idnav==""||$idnav==" "){
    if($naviderr===1){
        echo '<div class="nav-link"><ul>';
        while($navrow=mysqli_fetch_assoc($navresult)){
            $navid=$navrow['linkid'];//一级分类id
            $navname=$navrow['linkname'];//一级分类名称
            $navimg=$navrow['linkimg'];//一级分类下文章封面的类别，1为竖屏，2为横屏，3为资讯类(即一排左侧单图或无图模式)
            $navint=$navrow['linkint'];//一级分类下文章封面的单行数量，1为默认4张，2为3张（注明：对于类别为‘资讯类’的，此参数无效）
            $navico=$navrow['linkico'];//一级分类的图标（出现在名称前，使用的'Font Awesome'图标）
            echo '<li><div class="allnavdiv nocopy" data-linkid="'.$navid.'"><i class="fa fa-caret-right"></i>'.$navname.'</div><div><a title="删除" class="navdel" data-navdid="'.$navid.'"><i class="fa fa-trash" aria-hidden="true"></i></a><a title="编辑" class="navedit" href="popingzi.php?type=2&nid='.$navid.'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><a title="添加分类" class="navnewtwo" data-navid="'.$navid.'"><i class="fa fa-plus-circle" aria-hidden="true"></i></a></div></li>';
            $twonavcxssql="SELECT * FROM ppz_fl WHERE fllinkid=$navid order by flid asc";
            $twonavcxsresult=mysqli_query($conn,$twonavcxssql);
            $twonavcxssize = mysqli_num_rows($twonavcxsresult);
            if($twonavcxssize>0){
                echo '<div class="navtwo-item" id="navtwoid'.$navid.'">';
                while($twonavrowscx=mysqli_fetch_array($twonavcxsresult)){
                    $twoflid=$twonavrowscx['flid'];
                    $twoflname=$twonavrowscx['flname'];
                    echo '<div class="navtwo-itemli">· <span class="navtwo-itemli-name" id="navflname'.$twoflid.'">'.$twoflname.'</span><div class="navfleditdiv"><a title="删除" class="fldel" data-fid="'.$twoflid.'"><i class="fa fa-trash-o" aria-hidden="true"></i></a><a title="编辑" class="fledit" data-fid="'.$twoflid.'"><i class="fa fa-edit" aria-hidden="true"></i></a></div></div>';
                }
                echo '<div class="navtwo-itemli">
                <input type="text" class="navtwo-input" placeholder="请输入分类名称，多个分类可用逗号隔开。" id="navtwoinput'.$navid.'" />
                <button class="navtwoaddbut" data-inputid="'.$navid.'">
                <i class="fa fa-plus" aria-hidden="true"></i>添加分类
                </button>
                </div>

                </div>';
            }else{
                echo '
                <div class="navtwo-item" id="navtwoid'.$navid.'">
                    <div class="navtwo-itemli">
                        <input type="text" class="navtwo-input" placeholder="请输入分类名称，多个分类可用逗号隔开。" id="navtwoinput'.$navid.'" />
                        <button class="navtwoaddbut" data-inputid="'.$navid.'">
                        <i class="fa fa-plus" aria-hidden="true"></i>添加分类
                        </button>
                    </div>
                </div>
                ';
            }

        }
        echo '</ul></div>
        <dialog id="navfldialog"><a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>修改分类</b><input type="text" id="navfldialoginput" placeholder="请输入分类名称" /><button id="navfldialogbut" data-yid="">确定</button><span id="navfldialogerr"></span></dialog>
        <script src="/style/js/allnav.js" type="text/javascript"></script>';
    }else{
        echo '<div class="naverr">还没有列表，请先添加列表！</div>';
    }
}
}else{
echo "请勿胡搞！";
}
?>