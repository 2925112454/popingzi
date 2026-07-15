<?php
if ($admin == 1 && $typeuser == 14 && ($allvip == 4||$allvip == 3) && !empty($ppzusername)) {
    // 获取所有广告数据
    $adssql = "SELECT * FROM ppz_ads ORDER BY aid ASC LIMIT 7";
    $adretval = mysqli_query($conn, $adssql);
    
    if (mysqli_num_rows($adretval) != 7) {
        echo '<div class="nulldiv">数据库结构错误，请检查广告管理表结构。</div>';
        exit;
    }

    // 定义广告类型
    $adsTypes = [
        1 => 'hf',          // 横幅广告
        2 => 'rowhf',       // 内容页横幅
        3 => 'yxj',         // 右下角弹窗
        4 => 'ybl',         // 右侧边栏
        5 => 'left',        // 左侧悬浮
        6 => 'right',       // 右侧悬浮
        7 => 'js'           // 自定义 JS
    ];

    $adsData = [];

    // 加载所有广告数据到数组
    while ($row = mysqli_fetch_assoc($adretval)) {
        $aid = $row['aid'];
        $typeKey = $adsTypes[$aid];

        $adsData[$typeKey] = $row;

        // 处理时间格式
        $adsData[$typeKey]['atime'] = date("Y-m-d H:i", strtotime($row['atime']));
        
        // 处理开关状态
        $adsData[$typeKey]['ayes_checked'] = $row['ayes'] ? 'checked' : '';
        $adsData[$typeKey]['avip_checked'] = $row['avip'] ? 'checked' : '';

        // 处理展现位置
        $aeye = $row['aeye'];
        $adsData[$typeKey]['positions'] = [
            'sy' => strpos($aeye, "1") !== false ? 'checked' : '',
            'lb' => strpos($aeye, "2") !== false ? 'checked' : '',
            'row' => strpos($aeye, "3") !== false ? 'checked' : '',
            'so' => strpos($aeye, "4") !== false ? 'checked' : ''
        ];
    }

    // 输出样式和脚本资源
    echo '
    <link rel="stylesheet" href="/style/js/codemirror/codemirror.css">
    <link rel="stylesheet" href="/style/js/codemirror/ayu-dark.css">
    <link rel="stylesheet" href="/style/js/codemirror/lint.css">
    <script src="/style/js/codemirror/codemirror.js"></script>
    <script src="/style/js/codemirror/javascript.js"></script>
    <script src="/style/js/codemirror/active-line.js"></script>
    <script src="/style/js/codemirror/javascript-lint.js"></script>
    <script src="/style/js/codemirror/jshint.js"></script>
    <script src="/style/js/codemirror/lint.js"></script>

    <div class="user-h1">广告管理<button id="adsimgdel"><i class="fa fa-trash-o" aria-hidden="true"></i>清除冗余图片</button></div>';

    // 定义广告标题映射
    $adTitles = [
        'hf' => '横幅广告<span class="adstitlepx"><span>-</span>1200*90px</span>',
        'rowhf' => '内容页横幅<span class="adstitlepx"><span>-</span>880*90px</span>',
        'yxj' => '右下角弹窗<span class="adstitlepx"><span>-</span>240*80px</span>',
        'ybl' => '右侧边栏<span class="adstitlepx"><span>-</span>270*180px</span>',
        'left' => '左侧悬浮<span class="adstitlepx"><span>-</span>90*300px</span>',
        'right' => '右侧悬浮<span class="adstitlepx"><span>-</span>90*300px</span>',
        'js' => '自定义JS'
    ];

    // 循环输出每个广告模块
    foreach ($adsTypes as $aid => $key) {
        $title = $adTitles[$key];
        $data = $adsData[$key];

        echo '
        <div class="navcontent">
            <div class="navcontent-title"><b>' . $title . '</b>
                <div class="navcontent-title-right">
                    <span class="adsspan">开关：<input id="adsoff-' . $key . '" class="switch-btn" type="checkbox" ' . $data['ayes_checked'] . ' /></span>
                    <span class="adsspan">VIP免广告：<input id="adsvip-' . $key . '" class="switch-btn" type="checkbox" ' . $data['avip_checked'] . ' /></span>
                    <span class="adsspan">展现位置：
                        <span><input id="adsip-sy-' . $key . '" class="switch-btnck" type="checkbox" ' . $data['positions']['sy'] . ' ' . ($key === 'rowhf'? 'disabled' : '') . ' /><label for="adsip-sy-' . $key . '" class="nocopy">首页</label></span>
                        <span><input id="adsip-lb-' . $key . '" class="switch-btnck" type="checkbox" ' . $data['positions']['lb'] . ' ' . ($key === 'rowhf'? 'disabled' : '') . ' /><label for="adsip-lb-' . $key . '" class="nocopy">列表页</label></span>
                        <span><input id="adsip-row-' . $key . '" class="switch-btnck" type="checkbox" ' . $data['positions']['row'] . ' ' . ($key === 'rowhf'? '' : '') . ' /><label for="adsip-row-' . $key . '" class="nocopy">内容页</label></span>
                        <span><input id="adsoip-so-' . $key . '" class="switch-btnck" type="checkbox" ' . $data['positions']['so'] . ' ' . ($key === 'rowhf'? 'disabled' : '') . ' /><label for="adsoip-so-' . $key . '" class="nocopy">搜索页</label></span>
                    </span>
                    <span class="adsspan">有效时间：<input id="asdtime-' . $key . '" class="timeinput" type="datetime-local" value="' . $data['atime'] . '" /></span>
                </div>
            </div>';

        // 根据广告类型输出不同的输入区域
        if ($key === 'js') {
            echo '<textarea id="adstext-js" class="adstextarea" rows="10" cols="50">' . htmlspecialchars($data['ajs']) . '</textarea>';
        } else {
            echo '
            <div class="navcontent-input">图片：<input type="text" id="adsimg-' . $key . '" value="' . $data['aimg'] . '" placeholder="点击上传或输入图片地址或用&lt;script&gt;&lt;/script&gt;标签引入第三方广告代码" /><a id="adsimgup-' . $key . '" class="navcontent-up">上传</a></div>
            <div class="navcontent-input">链接：<input type="text" id="adsurl-' . $key . '" value="' . htmlspecialchars($data['aurl']) . '" placeholder="https://" /></div>';
        }

        echo '</div>';
    }

    echo '
    <div class="navcontent"><button id="ads-button">保存</button></div>

    <div class="upload-overlay" id="uploadOverlay" style="">  
        <div class="upload-box">
            <div class="custom-file-upload" id="dragArea">  
                <input type="file" id="fileUpload" style="display: none;">  
                <button id="fileimgbox" type="button"><i class="fa fa-plus"></i></button>  
            </div>
            <div class="file-info"><span id="fileInfo" style="display: flex;">请先点击上方选择要上传的文件</span></div>  
            <button id="closeUploadOverlay"><i class="fa fa-times"></i></button>
            <button id="openUploadOverlay">上传</button>
            <div id="fileerr" style="display: none;"></div>
        </div>  
    </div>
    <script src="/style/js/adsupimg.js" type="text/javascript"></script>
    <script src="/style/js/adsset.js" type="text/javascript"></script>';
} else {
    echo "请勿胡搞！";
}
?>