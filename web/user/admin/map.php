<?php
if ($admin==1 && $typeuser==11 && ($allvip==4||$allvip==3)  && !empty($ppzusername)){
    function getCurrentDomain() {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
        $domain = $_SERVER['HTTP_HOST'];
        return $protocol . $domain;
    }
    $weburl=getCurrentDomain();//当前域名
    $sitemapPath = $_SERVER['DOCUMENT_ROOT']. '/sitemap.xml';//站点地图文件路径（xml）
    $sitemapPathtxt = $_SERVER['DOCUMENT_ROOT']. '/sitemap.txt';//站点地图文件路径(txt)
    $robotsPath = $_SERVER['DOCUMENT_ROOT']. '/robots.txt';//robots.txt文件路径
    if (file_exists($sitemapPath)||file_exists($sitemapPathtxt)) {
        if(file_exists($sitemapPath)){
            $timestamp = filemtime($sitemapPath);
            $formattedTime = date('Y-m-d H:i:s', $timestamp);
            $filesizexx = filesize($sitemapPath);
            $formattedTime = $formattedTime.'<span class="sitemapsize">大小：'.round($filesizexx / 1024, 2).'KB</span>';
            $mapnewtime=$formattedTime;
        }else{
            if(file_exists($sitemapPathtxt)){
                $timestamp = filemtime($sitemapPathtxt);
                $formattedTime = date('Y-m-d H:i:s', $timestamp);
                $filesize = filesize($sitemapPathtxt);
                $formattedTime = $formattedTime.'<span class="sitemapsize">大小：'.round($filesize / 1024, 2).'KB</span>';
                $mapnewtime=$formattedTime;
            }else{
                $mapnewtime='未生成';
            }
        }
    } else {
        $mapnewtime='未生成';
    }
    if (file_exists($robotsPath)) {
        $timestamp = filemtime($robotsPath);
        $formattedTime = date('Y-m-d H:i:s', $timestamp);
        $filesizex = filesize($robotsPath);
        $formattedTime = $formattedTime.'<span class="sitemapsize">大小：'.round($filesizex / 1024, 2).'KB</span>';
        $rotnewtime=$formattedTime;
    }else{
        $rotnewtime='未生成';
    }
    echo '
<div class="tabs">  
  <div class="tab active" id="tab1"><div class="tab-title"><i class="fa fa-sitemap"></i>SiteMap生成</div></div>  
  <div class="tab" id="tab2"><div class="tab-title"><i class="fa fa-shield"></i>Robots配置</div></div>
</div>
<div class="tab-content"> 
    <div class="content active" id="content1">
        <div class="mapbox">
            <div class="maptitle">
                网站基础URL
                <input class="mapinput" type="text" id="mapurl" value="'.$weburl.'" />
                <span>不包含尾部斜杠的网站根URL</span>
            </div>
            <div class="maptitle">
                生成的条数
                <input class="mapinput" type="number" id="mapnumber" value="1000" min="1000" max="50000" />
                <span>写入SiteMap的url数量（限1000-50000条）</span>
            </div>
            <div class="maptitle">
                包含的页面
                <div class="mapcheckbox mapcheckboxbot">
                 <input type="checkbox" id="mapnewrow" checked/><label for="mapnewrow">最新文章</label>
                 <input type="checkbox" id="maplink" /><label for="maplink">列表页</label>
                 <input type="checkbox" id="mapindex" /><label for="mapindex">首页</label>
                 <input type="checkbox" id="mapsubject" /><label for="mapsubject">话题页</label>
                </div>
            </div>
            <div class="maptitle">
                生成格式
                <div class="mapcheckbox mapcheckboxbot">
                 <input type="checkbox" id="mapxml" checked/><label for="mapxml">XML文件</label>
                 <input type="checkbox" id="maptxt" /><label for="maptxt">TXT文件</label>
                </div>
            </div>
            <div class="maptitle">
                <button class="mapbutton" id="mapbutton">更新SiteMap</button>
            </div>
            <div class="maptext"><div class="mapdel"><a id="xmldel">删除XML文件</a><a id="txtdel">删除TXT文件</a><a href="/sitemap.xml" target="_blank">下载XML文件</a><a href="/sitemap.txt" target="_blank">下载TXT文件</a></div><div>上次更新：'.$mapnewtime.'</div></div>
        </div>
    </div>
    <div class="content" id="content2">
        <div class="mapbox">
            <div class="maptitletop">该配置完全对外公开，任何人都可以查看访问，注意不要暴露关键目录结构/路径；配置文件是君子协议，不代表搜索引擎会完全遵循规则。</div>
            <div class="maptitle">
                网站域名<input class="mapinput" type="text" id="roturl" value="'.$weburl.'" />
            </div>
            <div class="maptitle">
                用户代理（User-Agent）
                <div class="mapcheckbox mapcheckboxbot">
                    <input type="checkbox" id="Googlebot" /><label for="Googlebot">谷歌</label>
                    <input type="checkbox" id="Baiduspider" /><label for="Baiduspider">百度</label>
                    <input type="checkbox" id="BingBot" /><label for="BingBot">必应</label>
                    <input type="checkbox" id="360Spider" /><label for="360Spider">360</label>
                    <input type="checkbox" id="Slurp" /><label for="Slurp">雅虎</label>
                    <input type="checkbox" id="SogouSpider" /><label for="SogouSpider">搜狗</label>
                    <input type="checkbox" id="ToutiaoSpider" /><label for="ToutiaoSpider">今日头条</label>
                    <input type="checkbox" id="DouyinSpider" /><label for="DouyinSpider">抖音</label>
                    <input type="checkbox" id="WeChatSpider" /><label for="WeChatSpider">微信搜索</label>
                    <input type="checkbox" id="ShenmaSpider" /><label for="ShenmaSpider">神马(UC)</label>
                    <input type="checkbox" id="allrot" checked/><label for="allrot">所有爬虫(*)</label>
                </div>
                <span>选择规则适用的爬虫类型，规则对所选的爬虫生效</span>
            </div>

            <div class="maptitle">
                允许抓取的路径
                <textarea id="allow_path" rows="5" cols="50"></textarea>
                <span>示例：/about (允许访问单个页面)，/images/* (允许访问images目录下所有文件)</span>
            </div>

            <div class="maptitle">
                禁止抓取的路径
                <textarea id="allow_pathno" rows="5" cols="50"></textarea>
                <span>示例：/admin (禁止访问单个页面)，/tmp/ (禁止访问tmp目录下所有内容)</span>
            </div>

            <div class="maptitle">
                抓取延迟（秒）
                <input class="mapinput" type="number" id="rotnumber" value="3" min="0" max="315360000" />
                <span>设置爬虫抓取间隔，减轻服务器压力</span>
            </div>

            <div class="maptitle">
                SiteMap地址
                <input class="mapinput" type="text" id="rotmapurl" value="'.$weburl.'/sitemap.xml" />
            </div>

            <div class="maptitle">
                自定义Robots规则
                <textarea id="diyrobots" rows="5" cols="50"></textarea>
                <span>添加额外的自定义规则，每行一条规则</span>
            </div>

            <div class="maptitlex">
                <button class="mapbutton" id="rotyes">读入文件规则</button>
                <button class="mapbutton" id="rotbutton">保存当前规则</button>
            </div>
            <div class="maptext"><div class="mapdel"><a  id="robotsdel">删除robots.txt</a><a href="/robots.txt" target="_blank">下载robots.txt</a></div><div>上次更新：'.$rotnewtime.'</div></div>
        </div>
    </div>
</div>
<script src="/style/js/tab.js" type="text/javascript"></script>
<script src="/style/js/map.js" type="text/javascript"></script>
';

}else{
    echo "请勿胡搞！";
}
?>