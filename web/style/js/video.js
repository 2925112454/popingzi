var videos = document.getElementsByTagName('video');// 获取所有video标签
if (videos.length > 0) {  //若存在video标签
    for (var i = 0; i < videos.length; i++) {  
        var video = videos[i];  
        var sources = video.getElementsByTagName('source');  
          
        // 遍历每个video标签内的source标签  
        for (var j = 0; j < sources.length; j++) {  
            var source = sources[j];  
            var src = source.getAttribute('src');  
              
            // 检查src是否指向一个可能的HLS流（这里只是简单地检查后缀）  
            if (src.endsWith('.m3u8')) {  
                source.parentNode.removeChild(source); // 移除旧的source标签  
                if (Hls.isSupported()) {  
                      var hls = new Hls();  
                    hls.loadSource(src);  
                    hls.attachMedia(video);  
                    // hls.on(Hls.Events.MANIFEST_PARSED, function() {// 当HLS流加载完成后，将video标签设置为自动播放  
                    //     video.play();
                    // });  

                } else {  
                    console.error('网页含有HLS视频流，可惜不支持！');  // 提示不支持HLS
                }  
                break;
            }  
        }  
    } 
}