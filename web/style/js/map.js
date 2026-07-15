document.addEventListener('DOMContentLoaded', function() {
    /* 生成网站SiteMap */
    const input_mapurl=document.getElementById('mapurl');//网站基础URL
    const input_mapnumber=document.getElementById('mapnumber');//生成最新条数（限制1000-50000）
    const input_mapxml=document.getElementById('mapxml');//生成XML文件（type="checkbox"）
    const input_maptxt=document.getElementById('maptxt');///生成TXT文件（type="checkbox"）
    const input_mapnewrow=document.getElementById('mapnewrow');//最新文章页面（type="checkbox"）
    const input_maplink=document.getElementById('maplink');///列表页面（type="checkbox"）
    const input_mapindex=document.getElementById('mapindex');///首页（type="checkbox"）
    const input_mapsubject=document.getElementById('mapsubject');///话题（type="checkbox"）
    const input_apbut=document.getElementById('mapbutton');//提交按钮
    const xmldel=document.getElementById('xmldel');//XML文件删除
    const txtdel=document.getElementById('txtdel');//TXT文件删除
    const robotsdel=document.getElementById('robotsdel');//robots.txt文件删除
    if(input_apbut&&input_mapurl&&input_mapnumber&&input_mapnumber.value>999&&input_mapnumber.value<50001&&txtdel&&xmldel&&robotsdel){
        //提交按钮点击事件
        input_apbut.addEventListener('click',function(){
            const mapurl=input_mapurl.value;
            const mapnumber=input_mapnumber.value;
            let mapxml,maptxt,mapnewrow,maplink,mapindex,mapsubject;
                if(input_mapxml.checked){
                    mapxml=1;
                }else{
                    mapxml=0;
                }
                if(input_maptxt.checked){
                    maptxt=1;
                }else{
                    maptxt=0;
                }
                if(input_mapnewrow.checked){
                    mapnewrow=1;
                }else{
                    mapnewrow=0;
                }
                if(input_maplink.checked){
                    maplink=1;
                }else{
                    maplink=0;
                }
                if(input_mapindex.checked){
                    mapindex=1;
                }else{
                    mapindex=0;
                }
                if(input_mapsubject.checked){
                    mapsubject=1;
                }else{
                    mapsubject=0;
                }
            if(!mapurl){
                alert('<font>(｡ŏ_ŏ)</font> 网站基础URL不能为空！');
                return;
            }
            if(!mapnumber){
                alert('<font>(｡ŏ_ŏ)</font> 生成最新条数不能为空！');
                return;
            }
            if(mapnumber<1000){
                alert('<font>(｡ŏ_ŏ)</font> 生成最新条数太少了！');
                input_mapnumber.value=1000;
                return;
            }
            if(mapnumber>50000){
                alert('<font>(｡ŏ_ŏ)</font> 生成最新条数太多了！');
                input_mapnumber.value=50000;
                return;
            }
            if(mapxml==0&&maptxt==0){
                alert('<font>(｡ŏ_ŏ)</font> 至少选择一个文件格式！');
                return;
            }
            if(mapnewrow==0&&maplink==0&&mapindex==0&&mapsubject==0){
                alert('<font>(｡ŏ_ŏ)</font> 至少选择一个页面！');
                return;
            }
            if(!mapurl.startsWith('http://')&&!mapurl.startsWith('https://')){
                alert('<font>(｡ŏ_ŏ)</font> 网站URL格式错误！');
                return;
            }

                $.ajax({
                    type: "POST",
                    url: "/api/SiteMap.php",
                    dataType: "json",
                    data: {
                        url: mapurl,
                        number: mapnumber,
                        xml: mapxml,
                        txt: maptxt,
                        mapnewrow: mapnewrow,
                        maplink: maplink,
                        mapindex: mapindex,
                        mapsubject: mapsubject,
                    },
                    success: function(smap) {
                        if (smap.code === 200) {
                            alert('<font>(◕‿◕)</font> 成功！共写入' + smap.count + '条数据！');
                        }else if(smap.code === 500) {
                            alert('<font>(｡ŏ_ŏ)</font> '  + smap.msg);
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！' );
                            console.log(smap);
                        }
                    }
                });
            
        });
        //删除文件
        function mapdelete(type){
            if(type=='xml'||type=='txt'||type=='robots'){
                const typex=type.toUpperCase();
                let delif = "";
                if(type=='robots'){
                    delif = confirm('确定删除robots.txt吗？');
                }else{
                    delif = confirm('确定删除'+typex+'文件吗？');
                }
                if(delif){
                    $.ajax({
                    type: "POST",
                    url: "/api/SiteMapdel.php",
                    dataType: "json",
                    data: {
                        type: type,
                    },
                    success: function(smapd) {
                        if (smapd.code === 200) {
                            alert('<font>(◕‿◕)</font> 删除成功！');
                        }else if(smapd.code === 500) {
                            alert('<font>(｡ŏ_ŏ)</font> '  + smapd.msg);
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！' );
                            console.log(smapd);
                        }
                    }
                });
                }
            }
        }

        xmldel.addEventListener('click',function(){
            mapdelete('xml');
        });
        txtdel.addEventListener('click',function(){
            mapdelete('txt');
        });
        robotsdel.addEventListener('click',function(){
            mapdelete('robots');
        });
    }
    /* Robots配置 */
    const roturl = document.getElementById('roturl');//网站域名(不包含尾部斜杠的网站根URL)
    const Googlebot = document.getElementById('Googlebot');//用户代理（User-Agent）：谷歌
    const Baiduspider = document.getElementById('Baiduspider');////用户代理（User-Agent）：百度
    const BingBot = document.getElementById('BingBot');//用户代理（User-Agent）：必应
    const sanliulingSpider = document.getElementById('360Spider');//用户代理（User-Agent）：360
    const Slurp = document.getElementById('Slurp');//用户代理（User-Agent）：雅虎
    const SogouSpider = document.getElementById('SogouSpider');//用户代理（User-Agent）：搜狗
    const ToutiaoSpider = document.getElementById('ToutiaoSpider');//用户代理（User-Agent）：今日头条
    const DouyinSpider = document.getElementById('DouyinSpider');//用户代理（User-Agent）：抖音
    const WeChatSpider = document.getElementById('WeChatSpider');//用户代理（User-Agent）：微信搜索
    const ShenmaSpider = document.getElementById('ShenmaSpider');//用户代理（User-Agent）：神马
    const allrot = document.getElementById('allrot');//用户代理（User-Agent）：所有爬虫(*)
    const allow_path = document.getElementById('allow_path');//允许抓取的路径(textarea)
    const allow_pathno = document.getElementById('allow_pathno');//禁止抓取的路径(textarea)
    const rotnumber = document.getElementById('rotnumber');//抓取延迟（秒）
    const rotmapurl = document.getElementById('rotmapurl');//SiteMap地址
    const diyrobots = document.getElementById('diyrobots');//自定义Robots规则(textarea)
    const rotyes=  document.getElementById('rotyes');//读入当前配置(button按钮)
    const rotbutton=  document.getElementById('rotbutton');//保存配置(button按钮)
    if(allow_path&&allow_pathno&&rotnumber&&rotmapurl&&diyrobots&&rotyes&&rotbutton&&roturl&&Googlebot&&BingBot&&sanliulingSpider&&Baiduspider&&Slurp&&SogouSpider&&ToutiaoSpider&&DouyinSpider&&WeChatSpider&&ShenmaSpider&&allrot){
        let google,baidu,bing,sanliuling,Yahoo,sogou,toutiao,douyin,wechat,shenma,all;
        rotbutton.addEventListener('click', function() {
            if(!roturl.value){
                alert('<font>(｡ŏ_ŏ)</font> 网站域名不能为空！');
                return;
            }
            if(!roturl.value.startsWith('http://')&&!roturl.value.startsWith('https://')){
                alert('<font>(｡ŏ_ŏ)</font> 网站域名格式错误！');
                return;
            }
            if(rotmapurl.value){
                if(!rotmapurl.value.startsWith('http://')&&!rotmapurl.value.startsWith('https://')){
                    alert('<font>(｡ŏ_ŏ)</font> SiteMap地址错误！');
                    return;
                }
            }
            if(!rotnumber.value){
                alert('<font>(｡ŏ_ŏ)</font> 抓取延迟不能为空！');
                return;
            }
            if(rotnumber.value<0){
                alert('<font>(｡ŏ_ŏ)</font> 抓取延迟不能小于0！');
                return;
            }
            if(rotnumber.value>315360000){
                alert('<font>(｡ŏ_ŏ)</font> 抓取延迟值过大！');
                return;
            }

            if(Googlebot.checked){
                google=1;
            }else{
                google=0;
            }
            if(Baiduspider.checked){
                baidu=1;
            }else{
                baidu=0;
            }
            if(BingBot.checked){
                bing=1;
            }else{
                bing=0;
            }
            if(sanliulingSpider.checked){
                sanliuling=1;
            }else{
                sanliuling=0;
            }
            if(Slurp.checked){
                Yahoo=1;
            }else{
                Yahoo=0;
            }
            if(SogouSpider.checked){
                sogou=1;
            }else{
                sogou=0;
            }
            if(ToutiaoSpider.checked){
                toutiao=1;
            }else{
                toutiao=0;
            }
            if(DouyinSpider.checked){
                douyin=1;
            }else{
                douyin=0;
            }
            if(WeChatSpider.checked){
                wechat=1;
            }else{
                wechat=0;
            }
            if(ShenmaSpider.checked){
                shenma=1;
            }else{
                shenma=0;
            }
            if(allrot.checked){
                all=1;
            }else{
                all=0;
            }

            if(google==0&&baidu==0&&bing==0&&sanliuling==0&&Yahoo==0&&sogou==0&&toutiao==0&&douyin==0&&wechat==0&&shenma==0&&all==0){
                alert('<font>(｡ŏ_ŏ)</font> 至少选择一个搜索爬虫！');
                return;
            }

            $.ajax({
                    type: "POST",
                    url: "/api/Robots.php",
                    dataType: "json",
                    data: {
                        url: roturl.value,
                        google:google,
                        baidu:baidu,
                        bing:bing,
                        sanliuling:sanliuling,
                        Yahoo:Yahoo,
                        sogou:sogou,
                        toutiao:toutiao,
                        douyin:douyin,
                        wechat:wechat,
                        shenma:shenma,
                        all:all,
                        yes:allow_path.value,
                        no:allow_pathno.value,
                        number:rotnumber.value,
                        map:rotmapurl.value,
                        diy:diyrobots.value
                    },
                    success: function(rob) {
                        if (rob.code === 200) {
                            alert('<font>(◕‿◕)</font> 保存成功！');
                        }else if(rob.code === 500) {
                            alert('<font>(｡ŏ_ŏ)</font> '  + rob.msg);
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！' );
                            console.log(rob);
                        }
                    }

                });
            
        })
        rotyes.addEventListener('click', function() {
                $.ajax({
                    type: "POST",
                    url: "/api/readrobots.php",
                    dataType: "json",
                    success: function(roby) {
                        if (roby.code === 200) {
                            parseAndFillRobotsContent(roby.data);
                            alert('<font>(◕‿◕)</font> 读入成功！');
                        }else if(roby.code === 500) {
                            alert('<font>(｡ŏ_ŏ)</font> '  + roby.msg);
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！' );
                            console.log(roby);
                        }
                    }

                });
        });
                // 解析robots.txt内容并填充到表单中
                function parseAndFillRobotsContent(content) {
                    // 重置所有表单字段
                    resetFormFields();
                    
                    const lines = content.split('\n');
                    let currentAgent = null; // 当前处理的User-Agent
                    let inCustomSection = false; // 是否在自定义规则部分
                    let isFirstCustomLine = true; // 是否自定义部分的第一行
                    
                    lines.forEach((line, index) => {
                        line = line.trim();
                        
                        // 跳过空行
                        if (!line) return;
                        
                        // 检测自定义规则部分开始
                        if (line === '# 自定义规则') {
                            inCustomSection = true;
                            isFirstCustomLine = false;
                            return;
                        }
                        
                        // 自定义规则部分 - 直接添加到diyrobots文本框，不解析
                        if (inCustomSection) {
                            diyrobots.value += line + '\n';
                            isFirstCustomLine = false;
                            return;
                        }
                        
                        // 跳过非自定义部分的注释
                        if (line.startsWith('#')) return;
                        
                        // 检测User-Agent行
                        if (line.startsWith('User-agent:') || line.startsWith('User-Agent:')) {
                            currentAgent = line.split(':', 2)[1].trim();
                            handleUserAgent(currentAgent);
                            isFirstCustomLine = true;
                            return;
                        }
  
                        // 仅处理有当前User-Agent时的规则
                        if (currentAgent) {
                            handleRule(line, currentAgent);
                        }
                    });
                    
                    // 清理文本框空行
                    cleanTextareas();
                }

                // 处理规则行（Allow/Disallow/Crawl-delay）
                function handleRule(line, agent) {
                    if (line.startsWith('Allow:')) {
                        const path = line.split(':', 2)[1].trim();
                        addToField(allow_path, path, agent);
                    } else if (line.startsWith('Disallow:')) {
                        const path = line.split(':', 2)[1].trim();
                        addToField(allow_pathno, path, agent);
                    } else if (line.startsWith('Crawl-delay:')) {
                        const delay = line.split(':', 2)[1].trim();
                        // 仅当当前User-Agent为*时设置全局延迟
                        if (agent === '*'){
                            rotnumber.value = delay;
                        }else{
                            rotnumber.value = 0;
                        }
                    } else if (line.startsWith('Sitemap:')) {
                        // 修复Sitemap解析 - 使用正确的分割方式
                        const sitemap = line.substring('Sitemap:'.length).trim();
                        rotmapurl.value = sitemap;
                    }
                }

                // 重置所有表单字段
                function resetFormFields() {
                    [Googlebot, Baiduspider, BingBot, sanliulingSpider, Slurp, 
                    SogouSpider, ToutiaoSpider, DouyinSpider, WeChatSpider, 
                    ShenmaSpider, allrot].forEach(checkbox => checkbox.checked = false);
                    allow_path.value = allow_pathno.value = rotnumber.value = rotmapurl.value = diyrobots.value = '';
                }

                // 处理User-Agent识别和复选框状态
                function handleUserAgent(agent) {                  
                    // 更新复选框状态
                    switch(agent) {
                        case 'Googlebot': Googlebot.checked = true; break;
                        case 'Baiduspider': Baiduspider.checked = true; break;
                        case 'BingBot': BingBot.checked = true; break;
                        case '360Spider': sanliulingSpider.checked = true; break;
                        case 'YahooBot': Slurp.checked = true; break;
                        case 'Sogou': SogouSpider.checked = true; break;
                        case 'BytedanceSpider': ToutiaoSpider.checked = true; break;
                        case 'DuckDuckBot': DouyinSpider.checked = true; break;
                        case 'WeChat': WeChatSpider.checked = true; break;
                        case 'YisouSpider': ShenmaSpider.checked = true; break;
                        case '*': allrot.checked = true; break;
                    }
                }

                // 按User-Agent合并规则（避免重复）
                function addToField(field, value, agent) {
                    const fieldValue = field.value;
                    
                    // 检查是否已存在该规则
                    if (!fieldValue.includes(value)) {
                        field.value += (fieldValue ? '\n' : '') + value;
                    }
                }

                // 清理文本框空行
                function cleanTextareas() {
                    allow_path.value = allow_path.value.trim();
                    allow_pathno.value = allow_pathno.value.trim();
                    diyrobots.value = diyrobots.value.trim();
                }
    }
    
});