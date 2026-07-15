<?php
if (isset($wid) && preg_match('/^[1-9]\d*$/', $wid)){
    function html_to_plain_substr($htmlx, $lengthx){
        // 移除script、style区块
        $htmlx = preg_replace('#<(script|style).*?>.*?</\\1>#is', '', $htmlx);
        $textx = strip_tags($htmlx);
        $textx = html_entity_decode($textx, ENT_QUOTES, 'UTF-8');
        // ========== 敏感内容过滤 ==========
        // 1. 密码相关内容过滤
        $pwdPattern = '/(解压码|提取码|密码|password).{0,8}/iu';
        $textx = preg_replace($pwdPattern, '******', $textx);
        // 2. 邮箱地址过滤（标准邮箱正则，支持中英文前后字符）
        $emailPattern = '/[a-zA-Z0-9_\-\.\+]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z]{2,}/iu';
        $textx = preg_replace($emailPattern, '******', $textx);
        // 3. URL拦截正则
        $pattern = '/(?:(?:https?|ftps?):\/\/|\/\/|www\.)[^\s，。！？；：""\'()（）、]+|[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]{1,}[^\s，。！？；：""\'()（）、]*/iu';
        $textx = preg_replace($pattern, '******', $textx);
        // 合并空白 + 首尾修剪
        $textx = preg_replace('/\s+/u', ' ', $textx);
        $textx = trim($textx);
        // 优先使用mb截取UTF8字符
        if (function_exists('mb_substr')) {
            $textx = mb_substr($textx, 0, $lengthx, 'UTF-8');
        } else {
            $textx = substr($textx, 0, $lengthx * 1);
        }
        // 截取完成后再次去除末尾空格
        $textx = trim($textx);
        return $textx . "……";
    }
    function get_ogscheme(){
            // 代理转发优先，兼容大小写
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $proto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
                if ($proto === 'https') {
                    return 'https';
                }
            }
            // 原生HTTPS兜底
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                return 'https';
            }
            return 'http';
    }
        $og_scheme = get_ogscheme();//获取协议
        $og_host = $_SERVER['HTTP_HOST'];//获取主机名
        $og_uri  = $_SERVER['REQUEST_URI'];//获取请求URI
        $og_fullUrl = $og_scheme . '://' . $og_host . $og_uri;//获取完整URL

    if($wif==3){
        $og_description = !empty($videotext) ? html_to_plain_substr($videotext, 80) : '该视频暂无文字简介……';
        if(!empty($rowimg)){
            $og_image = $rowimg;
        }else{
            $og_image = '';
        }
    }elseif($wif==2){
        $og_description = !empty($videotext) ? html_to_plain_substr($videotext, 80) : '该相册暂无文字简介……';
        if(!empty($rowimg)){
            $og_image = $rowimg;
        }else{
            if(!empty($wrow)){
                $og_imgarr=explode('|',$wrow);
                $og_image = $og_imgarr[0];
            }else{
                $og_image = '';
            }
        }
    }else{
        $og_description = !empty($wrow) ? html_to_plain_substr($wrow, 80) : '该文章暂无文字简介……';
        if(!empty($rowimg)){
            $og_image = $rowimg;
        }else{
            if(!empty($wrow)){
                //提取wrow中的第一个图片(html格式)
                if (preg_match('/<img[^>]+src=[\'"]?([^\'">]+)[\'"]?/i', $wrow, $og_match)) {
                    $og_image = $og_match[1];
                    if (substr($og_image, 0, 5) !== 'data:') {
                        $og_image = $og_image;
                    }
                } else {
                    $og_image = '';
                }
            }else{
                $og_image = '';
            }
        }
    }

    if(!empty($wtag)){
        $og_keywords = $wtag;
    }else{
        $og_keywords = $webpass;
    }
    if(!empty($og_description)){
        $web_og_description = $og_description;
    }else{
        $web_og_description = $webvar;
    }

    if (!empty($og_image)) {
        $og_image = str_replace(['../', './', '\\'], '/', $og_image);
         if (!preg_match('/^(https?:)?\/\//i', $og_image)) {
            $og_image = ltrim($og_image, '/');
            $og_image = $og_scheme . '://' . $og_host . '/' . $og_image;
        }
    }
    // 默认MIME类型兜底
    $og_imgtypex = 'image/jpeg';
    if (!empty($og_image)) {
        $og_img_ext = pathinfo($og_image, PATHINFO_EXTENSION);
        $og_img_ext = strtolower($og_img_ext);
    
        switch ($og_img_ext) {
            case 'webp':
                $og_imgtypex = 'image/webp';
                break;
            case 'gif':
                $og_imgtypex = 'image/gif';
                break;
            case 'png':
                $og_imgtypex = 'image/png';
                break;
            case 'avif':
                $og_imgtypex = 'image/avif';
                break;
            default:
                $og_imgtypex = 'image/jpeg';
                break;
        }
    }
    
echo'
<link rel="canonical" href="'.$og_fullUrl.'" />
<meta name="keywords" content="'.$og_keywords.'" />
<meta name="description" content="'.$web_og_description.'" />
<meta property="og:title" content="'.$wtxt.'" />
<meta property="og:description" content="'.$og_description.'" />
<meta property="og:type" content="article" />
<meta property="og:url" content="'.$og_fullUrl.'" />
<meta property="og:image" content="'.$og_image.'" />
<meta property="og:image:type" content="'.$og_imgtypex.'" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="'.$wtxt.'" />
<meta name="twitter:description" content="'.$og_description.'" />
<meta name="twitter:image" content="'.$og_image.'" />
';
}
?>