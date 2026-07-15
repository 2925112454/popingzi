<?php
    if ($admin==1 && $typeuser==16 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)):

        function formatNumber($number) {
            $num = trim((string)$number);
            $num = preg_replace('/[^0-9.]/', '', $num);
            if ($num === '' || !is_numeric($num)) {
                return '0';
            }
            $num = (float)$num;
            if ($num < 1000) {
                return (string)(int)$num;
            } elseif ($num < 10000) {
                $formatted = number_format($num / 1000, 2, '.', '');
                return $formatted . 'K';
            } else {
                $formatted = number_format($num / 10000, 2, '.', '');
                if (substr($formatted, -3) === '.00') {
                    $formatted = (string)(int)($num / 10000);
                }
                return $formatted . 'W';
            }
        }
        
        if($allvip==2):
            $setid=3;
            if(isset($_GET["t"])&&($_GET["t"]==="3"||$_GET["t"]==="4")):
                $setid=$_GET["t"];
            endif;
        else:
            $setid=1;
            if(isset($_GET["t"])&&($_GET["t"]==="1"||$_GET["t"]==="2"||$_GET["t"]==="3"||$_GET["t"]==="4")):
                $setid=$_GET["t"];
            endif;
        endif;

        // 话题配置信息
        $sub_set_title = "";
        $sub_set_off = 0;
        $sub_set_tag = "";
        $sub_set_mun = 0;
        $sub_set_maxrep = 2;
        $sub_set_sql = "SELECT set_id,set_title,set_off,set_tag,set_mun,set_maxrep FROM `ppz_subset` WHERE `set_id` = 1";
        $sub_set_query = mysqli_query($conn,$sub_set_sql);
        if ($sub_set_query && mysqli_num_rows($sub_set_query) > 0) {
            $sub_set_row = mysqli_fetch_assoc($sub_set_query);
            $sub_set_title = $sub_set_row['set_title'];// 话题标题
            $sub_set_off = $sub_set_row['set_off'];// 话题开关
            $sub_set_tag = $sub_set_row['set_tag'];// 话题附名称
            $sub_set_mun = $sub_set_row['set_mun'];// 话题发表限制
            $sub_set_maxrep = $sub_set_row['set_maxrep'];// 话题评论层级
        }
        //总的话题数量
        $sub_total = 0;
        $sub_total_sql = "SELECT COUNT(id) AS total FROM `ppz_subject`";
        $sub_total_query = mysqli_query($conn,$sub_total_sql);
        if ($sub_total_query && mysqli_num_rows($sub_total_query) > 0) {
            $sub_total_row = mysqli_fetch_assoc($sub_total_query);
            $sub_total = formatNumber($sub_total_row['total']);
        }
        //总的评论数量
        $sub_total_rep = 0;
        $sub_total_rep_sql = "SELECT COUNT(comm_id) AS totalrep FROM `ppz_subcomm`";
        $sub_total_rep_query = mysqli_query($conn,$sub_total_rep_sql);
        if ($sub_total_rep_query && mysqli_num_rows($sub_total_rep_query) > 0) {
            $sub_total_rep_row = mysqli_fetch_assoc($sub_total_rep_query);
            $sub_total_rep = formatNumber($sub_total_rep_row['totalrep']);
        }
?>
    <div class="tabs">
        <?php if($allvip==4 || $allvip==3):?>
        <a href="?type=16"><div class="tab <?php if($setid==1){echo "active";}?>" id="tab1"><div class="tab-title"><i class="fa fa-cog"></i>话题设置</div></div></a>
        <a href="?type=16?&t=2"><div class="tab <?php if($setid==2){echo "active";}?>" id="tab1"><div class="tab-title"><i class="fa fa-tags"></i>话题标签</div></div></a>
        <?php endif;?>
        <a href="?type=16?&t=3"><div class="tab <?php if($setid==3){echo "active";}?>" id="tab1"><div class="tab-title"><i class="fa fa-bars"></i>话题列表(<?php echo $sub_total;?>)</div><?php if($sub_total_wait>0):?><div class="subadmin"><?php echo $sub_total_waitx;?></div><?php endif;?></div></a>
        <a href="?type=16?&t=4"><div class="tab <?php if($setid==4){echo "active";}?>" id="tab1"><div class="tab-title"><i class="fa fa-comments"></i>评论列表(<?php echo $sub_total_rep;?>)</div></div></a>
    </div>
    <div class="tab-content">
        <div class="sub-set-content"><div class="content-form">

            <?php if($setid==1&&($allvip==4 || $allvip==3)):?>
                <div class="upif">
                    <div class="upfileradio">  
                    话题开关：  
                    <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="subset_yes" value="1" class="custom-radio" <?php if($sub_set_off==1){echo "checked";}?>><label for="subset_yes">开启</label></div>
                    <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="subset_no" value="0" class="custom-radio" <?php if($sub_set_off==0){echo "checked";}?>><label for="subset_no">关闭</label></div>
                    </div>
                </div>
                <div class="upif"><span>话题标题：<b>话题标题，展示在菜单栏，4字以内最佳。</b></span><input placeholder="话题标题" type="text" id="subtitle" value="<?php echo $sub_set_title;?>" /></div>
                <div class="upif"><span>话题名称：<b>话题版块内使用，表示具体的名称，建议2个字。</b></span><input placeholder="话题名称" type="text" id="subname" value="<?php echo $sub_set_tag;?>" /></div>
                <div class="upif"><span>限制发表：<b>每个用户每天最多可发表的篇数，0为不限制。</b></span><input type="number" min="0" max="999999999" id="submun" value="<?php echo $sub_set_mun;?>" /></div>
                <div class="upif"><span>回复层级：<b>每个话的评论最多可回复的层级数量，0为不限制，建议1或2。</b></span><input type="number" min="0" max="999999999" id="repmun" value="<?php echo $sub_set_maxrep;?>" /></div>
                <div class="content-btn-div"><button class="content-btn" id="up_sub_set_button">确认</button></div>
                <script src="/subject/admin/set.js" type="text/javascript"></script>
            <?php elseif($setid==2&&($allvip==4 || $allvip==3)):?>
                <div class="servicediv"><input placeholder="请输入标签名称" type="text" id="servicefl"><button class="content-btn" id="servicebtn" type="submit">+添加标签</button></div>
                <ul class="serviceul_sub nocopy">
                    <?php
                        $service_tag_sql = "SELECT sub_id,sub_name FROM `ppz_subtype` ORDER BY `sub_id` ASC";
                        $service_tag_query = mysqli_query($conn,$service_tag_sql);
                        if ($service_tag_query && mysqli_num_rows($service_tag_query) > 0) {
                            while ($service_tag_row = mysqli_fetch_assoc($service_tag_query)) {
                                $service_tag_id = $service_tag_row['sub_id'];
                                $service_tag_name = $service_tag_row['sub_name'];
                                echo '<li title="双击编辑" id="subtag_del_'.$service_tag_id.'"><div data-txt="'.$service_tag_name.'" data-id="'.$service_tag_id.'" class="serviceli"><span>'.$service_tag_name.'</span><a title="删除" class="serfldel_sub" data-id="'.$service_tag_id.'"><i class="fa fa-times" aria-hidden="true"></i></a></div></li>';
                            }
                        }else{
                            echo '<div class="null">请在上方添加标签</div>';
                        }
                    ?>
                </ul>
                <dialog id="navfldialog"><a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>修改标签</b><input type="text" id="navfldialoginput" placeholder="请输入标签名称"><button id="navfldialogbut" data-id="">确定</button><span id="navfldialogerr"></span></dialog>
                <script src="/subject/admin/tagset.js" type="text/javascript"></script>
            <?php elseif($setid==3):?>
                <?php
                    // 1. 筛选参数处理
                    $statusFilter = isset($_GET['status']) && in_array($_GET['status'], ['1','2','3']) ? $_GET['status'] : '';
                    if (in_array($statusFilter, ['1', '2', '3'])) {
                        $statusFilter = (int)$statusFilter;
                    } else {
                        $statusFilter = '';
                    }
                    // 2. 分页参数处理
                    $p = isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >= 1 ? (int)$_GET['p'] : 1; // 当前页
                    $pageSize = 20; // 每页显示条数
                    $offset = ($p - 1) * $pageSize; // 偏移量

                    // 3. 总条数查询（带筛选条件）
                    $totalSql = "SELECT COUNT(id) AS total FROM `ppz_subject`";
                    if (!empty($statusFilter)) {
                        $totalSql .= " WHERE yes = " . (int)$statusFilter;
                    }
                    $totalQuery = mysqli_query($conn, $totalSql);
                    $total = 0;
                    if ($totalQuery && mysqli_num_rows($totalQuery) > 0) {
                        $totalRow = mysqli_fetch_assoc($totalQuery);
                        $total = (int)$totalRow['total'];
                    }
                    $totalPages = $total > 0 ? ceil($total / $pageSize) : 1; // 总页数

                    // 4. 话题列表数据查询（关联用户、标签表 + 筛选条件）
                    $subjectSql = "SELECT s.*, u.uname, st.sub_name 
                                FROM `ppz_subject` s
                                LEFT JOIN `ppz_newusername` u ON s.admin = u.uid
                                LEFT JOIN `ppz_subtype` st ON s.type = st.sub_id";
                    // 添加筛选条件
                    if (!empty($statusFilter)) {
                        $subjectSql .= " WHERE s.yes = " . (int)$statusFilter;
                    }
                    $subjectSql .= " ORDER BY s.top DESC, s.time DESC
                                LIMIT {$offset}, {$pageSize}";
                    $subjectQuery = mysqli_query($conn, $subjectSql);

                    // 5. 分页按钮处理（保留原有逻辑，追加筛选参数）
                    $pageHtml = '';
                    // 构建分页链接基础参数
                    $baseHref = '?type=16&t=3';
                    if (!empty($statusFilter)) {
                        $baseHref .= '&status=' . $statusFilter;
                    }
                    
                    // 首页
                    if ($p == 1) {
                        $pageHtml .= '<a class="page-no-button nocopy">首页</a>';
                    } else {
                        $pageHtml .= '<a href="' . $baseHref . '&p=1" class="page-button nocopy">首页</a>';
                    }
                    // 上一页
                    if ($p <= 1) {
                        $pageHtml .= '<a class="page-no-button nocopy">上一页</a>';
                    } else {
                        $prevPage = $p - 1;
                        $pageHtml .= '<a href="' . $baseHref . '&p='.$prevPage.'" class="page-button nocopy">上一页</a>';
                    }
                    // 下一页
                    if ($p >= $totalPages) {
                        $pageHtml .= '<a class="page-no-button nocopy">下一页</a>';
                    } else {
                        $nextPage = $p + 1;
                        $pageHtml .= '<a href="' . $baseHref . '&p='.$nextPage.'" class="page-button nocopy">下一页</a>';
                    }
                    // 尾页
                    if ($p == $totalPages) {
                        $pageHtml .= '<a class="page-no-button nocopy">尾页</a>';
                    } else {
                        $pageHtml .= '<a href="' . $baseHref . '&p='.$totalPages.'" class="page-button nocopy">尾页</a>';
                    }
                ?>
                <table class="regtxt-table">
                <div class="listdivr">
                        <div>
                        <span>筛选：</span>
                            <div class="dropdown">
                                <button class="dropbtn">
                                    <?php 
                                    $filterText = '全部状态';
                                    if ($statusFilter == 1) $filterText = '待审核';
                                    elseif ($statusFilter == 2) $filterText = '违规驳回';
                                    elseif ($statusFilter == 3) $filterText = '已发表';
                                    echo $filterText;
                                    ?>
                                    <i class="fa fa-sort"></i>
                                </button>
                                <div class="dropdown-content">
                                        <a href="?type=16&t=3">全部状态</a>
                                        <a href="?type=16&t=3&status=1">待审核</a>
                                        <a href="?type=16&t=3&status=2">违规驳回</a>
                                        <a href="?type=16&t=3&status=3">已发表</a>
                                </div>
                            </div>
                        <thead>
                        <tr>
                            <th width="8%">选择</th>
                            <th width="8%">状态</th>
                            <th width="20%">标题</th>
                            <th width="14%">发表者</th>
                            <th width="10%">标签</th>
                            <th width="20%">IP地址</th>
                            <th width="10%">发表时间</th>
                            <th width="10%">操作</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="clear">
                                <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a><a id="allcheckboxexe">批量审核</a><a id="alltop">置顶加精</a>第<?php echo $p;?>页（共<?php echo $totalPages;?>页）- 共计：<?php echo $total;?>条记录</span>
                                <span class="page-right">
                                <?php echo $pageHtml;?>
                                </span>
                            </div></td>
                        </tr>
                        </tfoot>
                        <tbody>            
                            <?php if ($subjectQuery && mysqli_num_rows($subjectQuery) > 0):?>
                                <?php while ($row = mysqli_fetch_assoc($subjectQuery)):?>
                                    <?php
                                        // 状态样式和文本处理
                                        $statusClass = '';
                                        $statusText = '';
                                        $statusDataText = '';
                                        switch ((int)$row['yes']) {
                                            case 1:
                                                $statusClass = 'yesorno';
                                                $statusText = '待审核';
                                                break;
                                            case 2:
                                                $statusClass = 'no noto nocopy';
                                                $statusText = '违规驳回';
                                                // 处理违规原因：去除空格、回车、换行、转义字符
                                                $noReason = isset($row['no']) ? trim(preg_replace('/[\r\n\s\\\]+/', '', $row['no'])) : '';
                                                $statusDataText = ' data-text="'.$noReason.'"';
                                                break;
                                            case 3:
                                                $statusClass = 'yes';
                                                $statusText = '已发表';
                                                break;
                                            default:
                                                $statusClass = 'yesorno';
                                                $statusText = '未知状态';
                                        }

                                        // 置顶/精选标识处理
                                        $topTag = '';
                                        switch ((int)$row['top']) {
                                            case 2:
                                                $topTag = '<span class="yesorno">[精选]</span>';
                                                break;
                                            case 3:
                                                $topTag = '<span class="no">[置顶]</span>';
                                                break;
                                        }

                                        // 修复时间格式化问题：先转成整型再格式化
                                        $timeValue = $row['time'];
                                        $publishTime = !empty($timeValue) ? $timeValue : '未知时间';

                                        // 标签名称（兼容无标签情况）
                                        $tagName = isset($row['sub_name']) ? $row['sub_name'] : '无标签';
                                        $tagId = isset($row['type']) ? $row['type'] : 0;

                                        // 用户名（兼容无用户数据情况）
                                        $username = isset($row['uname']) ? $row['uname'] : '未知用户';
                                        $userId = isset($row['admin']) ? $row['admin'] : 0;

                                        // 话题ID
                                        $subjectId = (int)$row['id'];
                                    ?>
                                    <tr class="alt-row"> 
                                    <td><input type="checkbox" name="id" value="<?php echo $subjectId;?>"></td>
                                    <td style="max-width:40px;"><span class="<?php echo $statusClass;?>"<?php echo $statusDataText;?>><?php echo $statusText;?></span></td>
                                    <td style="max-width:100px;"><a href="/subject/detail.php?id=<?php echo $subjectId;?>" target="_blank"><?php echo $topTag;?><?php echo $row['title'];?></a></td>
                                    <td style="max-width:60px;"><a href="/user.php?id=<?php echo $userId;?>" target="_blank"><?php echo $username;?></a></td>
                                    <td style="max-width:130px;"><a href="/subject/?t=<?php echo $tagId;?>" target="_blank"><?php echo $tagName;?></a></td>
                                    <td style="max-width:90px;"><a href="https://www.ipshudi.com/<?php echo $row['ip'];?>" target="_blank"><?php echo $row['ip'];?></a></td>
                                    <td style="max-width:130px;"><?php echo $publishTime;?></td>
                                    <td><a href="/subject/edit.php?id=<?php echo $subjectId;?>" target="_blank" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="udel" data-d="<?php echo $subjectId;?>" title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                    </tr>
                                <?php endwhile;?>
                            <?php else:?>
                                <tr>
                                    <td colspan="8" class="null">暂无数据</td>
                                </tr>
                            <?php endif;?>
                    </tbody>
                    </table>
                    <dialog id="subdialog"><h1><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>审核未通过</h1><p id="sub_no_mesg"></p><span>*请按要求修改后再提交审核！</span></dialog>
                    <div id="customMenu">
                    <div id="menuCheck">通过审核</div>
                        <div id="menuCheckx">等待审核</div>
                        <div id="menuCheckxx">驳回审核</div>
                        <div id="menuCheckxxx">置顶话题</div>
                        <div id="menuCheckxxxx">精选话题</div>
                        <div id="menuCheckxxxxx">普通话题</div>
                    </div>
                    <script src="/style/js/subrightclick.js" type="text/javascript"></script>
                    <script type="text/javascript" src="/subject/admin/eye.js"></script>
            <?php elseif($setid==4):?>
                <?php
                    // 1. 筛选参数处理（已读/未读）
                    $readFilter = isset($_GET['status']) && in_array($_GET['status'], ['0','1']) ? $_GET['status'] : '';
                    if (in_array($readFilter, ['0', '1'])) {
                        $readFilter = (int)$readFilter;
                    } else {
                        $readFilter = '';
                    }
                    
                    // 2. 分页参数处理
                    $p = isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >= 1 ? (int)$_GET['p'] : 1; // 当前页
                    $pageSize = 20; // 每页显示条数
                    $offset = ($p - 1) * $pageSize; // 偏移量

                    // 3. 总条数查询
                    $totalRepSql = "SELECT COUNT(comm_id) AS total FROM `ppz_subcomm`";
                    if (!is_null($readFilter)&&$readFilter!=='') {
                        $totalRepSql .= " WHERE comm_yes = " . (int)$readFilter;
                    }
                    $totalRepQuery = mysqli_query($conn, $totalRepSql);
                    $totalRep = 0;
                    if ($totalRepQuery && mysqli_num_rows($totalRepQuery) > 0) {
                        $totalRepRow = mysqli_fetch_assoc($totalRepQuery);
                        $totalRep = (int)$totalRepRow['total'];
                    }
                    $totalRepPages = $totalRep > 0 ? ceil($totalRep / $pageSize) : 1; // 总页数

                    // 4. 评论列表数据查询
                    $repSql = "SELECT sc.*, u.uname 
                                FROM `ppz_subcomm` sc
                                LEFT JOIN `ppz_newusername` u ON sc.comm_admin = u.uid";
                    // 添加筛选条件
                    if (!is_null($readFilter)&&$readFilter!=='') {
                        $repSql .= " WHERE sc.comm_yes = " . (int)$readFilter;
                    }
                    $repSql .= " ORDER BY sc.comm_time DESC
                                LIMIT {$offset}, {$pageSize}";
                    $repQuery = mysqli_query($conn, $repSql);

                    // 5. 分页按钮处理
                    $repPageHtml = '';
                    // 构建分页链接基础参数
                    $repBaseHref = '?type=16&t=4';
                    if (!is_null($readFilter)&&$readFilter!=='') {
                        $repBaseHref .= '&status=' . $readFilter;
                    }
                    
                    // 首页
                    if ($p == 1) {
                        $repPageHtml .= '<a class="page-no-button nocopy">首页</a>';
                    } else {
                        $repPageHtml .= '<a href="' . $repBaseHref . '&p=1" class="page-button nocopy">首页</a>';
                    }
                    // 上一页
                    if ($p <= 1) {
                        $repPageHtml .= '<a class="page-no-button nocopy">上一页</a>';
                    } else {
                        $prevPage = $p - 1;
                        $repPageHtml .= '<a href="' . $repBaseHref . '&p='.$prevPage.'" class="page-button nocopy">上一页</a>';
                    }
                    // 下一页
                    if ($p >= $totalRepPages) {
                        $repPageHtml .= '<a class="page-no-button nocopy">下一页</a>';
                    } else {
                        $nextPage = $p + 1;
                        $repPageHtml .= '<a href="' . $repBaseHref . '&p='.$nextPage.'" class="page-button nocopy">下一页</a>';
                    }
                    // 尾页
                    if ($p == $totalRepPages) {
                        $repPageHtml .= '<a class="page-no-button nocopy">尾页</a>';
                    } else {
                        $repPageHtml .= '<a href="' . $repBaseHref . '&p='.$totalRepPages.'" class="page-button nocopy">尾页</a>';
                    }

                    // 格式化点赞数函数
                    function formatLikeCount($likeStr) {
                        // 处理空值/非字符串情况
                        if (empty($likeStr) || !is_string($likeStr)) {
                            return '0';
                        }
                        // 分割点赞用户ID数组
                        $likeArr = explode(',', $likeStr);
                        // 过滤空元素（处理末尾逗号等情况）
                        $likeArr = array_filter($likeArr, function($val) {
                            return !empty(trim($val));
                        });
                        $count = count($likeArr);
                        
                        // 超过99999999按亿显示
                        if ($count > 99999999) {
                            $billion = $count / 100000000;
                            // 保留2位小数，去除末尾的0和小数点
                            $formatted = number_format($billion, 2, '.', '');
                            if (substr($formatted, -3) === '.00') {
                                $formatted = (int)$billion;
                            }
                            return $formatted . '亿+';
                        }
                        return (string)$count;
                    }
                ?>
                <script type="text/javascript" src="/subject/emoji.js"></script>
                <table class="regtxt-table">
                    <div class="listdivr">
                        
                        <div>
                        <span>筛选：</span>
                            <div class="dropdown">
                                <button class="dropbtn">
                                    <?php 
                                    $filterText = '全部状态';
                                    if ($readFilter === 0) $filterText = '未读状态';
                                    elseif ($readFilter === 1) $filterText = '已读状态';
                                    echo $filterText;
                                    ?>
                                    <i class="fa fa-sort"></i>
                                </button>
                                <div class="dropdown-content">
                                        <a href="?type=16&t=4">全部状态</a>
                                        <a href="?type=16&t=4&status=1">已读状态</a>
                                        <a href="?type=16&t=4&status=0">未读状态</a>
                                </div>
                            </div>
                        </div>
                    </div>

                        <thead>
                        <tr>
                            <th width="8%">选择</th>
                            <th width="8%">状态</th>
                            <th width="20%">内容</th>
                            <th width="14%">评论者</th>
                            <th width="10%">点赞数</th>
                            <th width="20%">IP地址</th>
                            <th width="10%">评论时间</th>
                            <th width="10%">操作</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="clear">
                                <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a>第<?php echo $p;?>页（共<?php echo $totalRepPages;?>页）- 共计：<?php echo $totalRep;?>条记录</span>
                                <span class="page-right">
                                <?php echo $repPageHtml;?>
                                </span>
                            </div></td>
                        </tr>
                        </tfoot>
                        <tbody>            
                            <?php if ($repQuery && mysqli_num_rows($repQuery) > 0):?>
                                <?php while ($row = mysqli_fetch_assoc($repQuery)):?>
                                    <?php
                                        // 状态样式和文本处理
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ((int)$row['comm_yes']) {
                                            case 0:
                                                $statusClass = 'no';
                                                $statusText = '未读';
                                                break;
                                            case 1:
                                                $statusClass = 'yes';
                                                $statusText = '已读';
                                                break;
                                            default:
                                                $statusClass = 'no';
                                                $statusText = '状态异常';
                                        }

                                        // 评论ID
                                        $commId = (int)$row['comm_id'];
                                        // 来源文章ID
                                        $commSubId = (int)$row['comm_subid'];
                                        // 评论内容
                                        $commText = isset($row['comm_text']) ? $row['comm_text'] : '';
                                        $commText = str_replace(array("\r", "\n"), '', $commText);//去换行回车
                                        $commText = trim($commText);//去首尾空格
                                        // 评论者ID和昵称
                                        $commAdminId = (int)$row['comm_admin'];
                                        $commAdminName = isset($row['uname']) ? $row['uname'] : '未知用户';
                                        // IP地址
                                        $commIp = isset($row['comm_ip']) ? $row['comm_ip'] : '';
                                        // 评论时间
                                        $commTime = isset($row['comm_time']) ? $row['comm_time'] : '未知时间';
                                        // 点赞数格式化
                                        $likeCount = formatLikeCount($row['comm_top']);
                                    ?>
                                    <tr class="alt-row"> 
                                    <td><input type="checkbox" name="repid" value="<?php echo $commId;?>"></td>
                                    <td style="max-width:40px;"><a href="/subject/detail.php?id=<?php echo $commSubId;?>" target="_blank"><span class="<?php echo $statusClass;?>"><?php echo $statusText;?></span></a></td>
                                    <td style="max-width:100px;"><a class="eyes_rep" data-text="<?php echo htmlspecialchars($commText, ENT_QUOTES, 'UTF-8');?>"><?php echo $commText;?></a></td>
                                    <td style="max-width:60px;"><a href="/user.php?id=<?php echo $commAdminId;?>" target="_blank"><?php echo $commAdminName;?></a></td>
                                    <td style="max-width:130px;"><?php echo $likeCount;?></td>
                                    <td style="max-width:90px;"><a href="https://www.ipshudi.com/<?php echo $commIp;?>" target="_blank"><?php echo $commIp;?></a></td>
                                    <td style="max-width:130px;"><?php echo $commTime;?></td>
                                    <td><a class="rep_edit" data-text="<?php echo htmlspecialchars($commText, ENT_QUOTES, 'UTF-8');?>" data-id="<?php echo $commId;?>" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="rep_del" data-d="<?php echo $commId;?>" title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                    </tr>
                                <?php endwhile;?>
                            <?php else:?>
                                <tr>
                                    <td colspan="8" class="null">暂无数据</td>
                                </tr>
                            <?php endif;?>
                    </tbody>
                </table>
                <dialog class="subeyescommenttext" id="eyescomment">
                    <a id="eyescommentclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>评论详情</b>
                    <div id="subeyescommenttext"></div>
                </dialog>
                <dialog class="subeyescommenttext editrep" id="editrep">
                    <a id="editrepclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>编辑评论</b>
                    <textarea style="max-width:initial;" id="editreptext" class="editreptext"></textarea>
                    <div id="emojibox" class="emojibox nocopy"></div>
                    <div class="flex"><div id="editrepmsg" class="msg"></div><button id="editrepsubmit" data-id="">确认修改</button></div>
                </dialog>
                
                <script type="text/javascript" src="/subject/admin/repeye.js"></script>
                
            <?php endif;?>
        </div></div>
    </div>
<?php else:?>请勿胡搞！<?php endif;?>