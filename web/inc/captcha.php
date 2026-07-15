<?php
// 开启会话
session_start();

// 设置图片大小
$image_width = 120;
$image_height = 47;

// 随机字符集
$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

// 生成随机验证码
$code = '';
$code_length = 4;// 验证码长度
for ($i = 0; $i < $code_length; $i++) {
    $code .= $charset[mt_rand(0, strlen($charset) - 1)];
}

// 将验证码存储到会话中
$_SESSION["captchanewyzm"] = $code;

// 创建图像资源
$image = imagecreatetruecolor($image_width, $image_height);

// 设置背景颜色
$bg_color = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $bg_color);

// 添加干扰点
$dot_count = 50; // 减少干扰点数量
for ($i = 0; $i < $dot_count; $i++) {
    $dot_color = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imagesetpixel($image, mt_rand(0, $image_width), mt_rand(0, $image_height), $dot_color);
}

// 添加干扰线
$line_count = 4; // 减少干扰线数量
for ($i = 0; $i < $line_count; $i++) {
    $line_color = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), $line_color);
}

// 绘制验证码字符
$font_size = 12; // 增大字体大小
$char_width = 20;
$char_height = 20;
for ($i = 0; $i < $code_length; $i++) {
    $x = ($image_width / $code_length) * $i + 5;
    $y = ($image_height - $char_height) / 2 + mt_rand(-5, 5);
    $char_color = imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
    $angle = mt_rand(-20, 20); // 进一步缩小旋转角度范围
    $image_char = imagecreatetruecolor($char_width, $char_height);
    imagefill($image_char, 0, 0, $bg_color);
    imagechar($image_char, $font_size, 5, 5, $code[$i], $char_color);
    $rotated_char = imagerotate($image_char, $angle, $bg_color);
    $rotated_width = imagesx($rotated_char);
    $rotated_height = imagesy($rotated_char);
    if ($x + $rotated_width > $image_width) {
        $x = $image_width - $rotated_width;
    }
    if ($y + $rotated_height > $image_height) {
        $y = $image_height - $rotated_height;
    }
    if ($y < 0) {
        $y = 0;
    }
    imagecopymerge($image, $rotated_char, $x, $y, 0, 0, $rotated_width, $rotated_height, 100);
    imagedestroy($image_char);
    imagedestroy($rotated_char);
}

// 设置响应头
header('Content-type: image/png');

// 输出图像
imagepng($image);

// 销毁图像资源
imagedestroy($image);
?>