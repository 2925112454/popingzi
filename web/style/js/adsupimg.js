document.addEventListener('DOMContentLoaded', function() {
    // 全局状态管理
    const uploadState = {
        fileyes: 0,
        filesarr: null,
        currentFileInputId: null
    };
    
    // DOM元素缓存
    const Domelements = {
        uploadOverlay: document.getElementById('uploadOverlay'),
        closeUploadOverlay: document.getElementById('closeUploadOverlay'),
        openUploadOverlay: document.getElementById('openUploadOverlay'),
        fileUpload: document.getElementById('fileUpload'),
        fileimgbox: document.getElementById('fileimgbox'),
        fileInfo: document.getElementById('fileInfo'),
        fileerr: document.getElementById('fileerr')
    };
    
    // 配置参数
    const UPconfig = {
        filesizekb: 1024, // 限制文件上传大小，单位kb
        allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/x-icon', 'image/bmp'] // 允许上传的文件MIME类型
    };
    
    // 报错信息更新函数
    function upadsimageserr(err) {
        if (Domelements.fileerr) {
            Domelements.fileerr.style.display = 'block';
            Domelements.fileerr.innerHTML = err;
        }
    }
    
    // 错误信息清除函数(初始化函数)
    function upadsimageserrtime() {
        if (Domelements.fileerr) {
            Domelements.fileerr.style.display = 'none';
            Domelements.fileerr.innerHTML = "";
        }
    }
    
    // 文件信息函数初始化
    function fileInfonull() {
        if (Domelements.fileInfo) {
            Domelements.fileInfo.style.display = 'flex';
            Domelements.fileInfo.innerHTML = '请先点击上方选择要上传的文件';
        }
    }
    
    // 文件信息更新函数
    function fileInfothuer(size, type, ext) {
        const mimeTypeMap = {
            // MIME类型到友好描述的映射
            'image/png': 'PNG图片',
            'image/jpeg': 'JPG图片',
            'image/gif': 'GIF图片',
            'image/bmp': 'BMP图片',
            'image/tiff': 'TIF图片',
            'image/x-icon': 'ICO图标',
            'image/svg+xml': 'SVG文件',
            'video/mp4': 'MP4视频',
            'video/webm': 'WebM视频',
            'video/ogg': 'Ogg视频',
            'video/quicktime': 'QuickTime视频',
            'video/3gpp': '3GPP视频',
            'video/3gp2': '3GP2视频',
            'video/avi': 'AVI视频',
            'video/flv': 'FLV视频',
            'video/mkv': 'MKV视频',
            'video/mpeg': 'MPEG视频',
            'video/x-matroska': 'Matroska视频',
            'video/x-ms-asf': 'ASF视频',
            'video/x-ms-wmv': 'WMV视频',
            'video/x-flv': 'FLV视频',
            'audio/mpeg': 'MP3音频',
            'audio/ogg': 'Ogg音频',
            'audio/wav': 'WAV音频',
            'audio/wave': 'WAVE音频',
            'audio/webm': 'WebM音频',
            'audio/x-ms-wma': 'WMA音频',
            'audio/aac': 'AAC音频',
            'audio/flac': 'FLAC音频',
            'audio/midi': 'MIDI音频',
            'audio/x-m4a': 'M4A音频',
            'application/x-rar-compressed': 'RAR压缩包',
            'application/x-zip-compressed': 'ZIP压缩包',
            'application/rar': 'RAR压缩包',
            'application/x-rar': 'RAR压缩包',
            'application/x-zip': 'ZIP压缩包',
            'application/zip': 'ZIP压缩包',
            'application/x-7z-compressed': '7Z压缩包',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX表格',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX文档',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX演示文稿',
            'application/pdf': 'PDF文档',
            'text/plain': '文本文件',
            'application/msword': 'DOC文档',
            'default': ext.toUpperCase() + '文件'
        };
        
        const friendlyFileType = mimeTypeMap[type] || mimeTypeMap['default'] || (ext + "文件");
        
        if (Domelements.fileInfo) {
            Domelements.fileInfo.style.display = 'grid';
            Domelements.fileInfo.innerHTML = '<b>文件大小：</b>' + size + 'KB<b>文件类型：</b>' + friendlyFileType;
        }
    }
    
    // 初始化文件预览
    function filePreview() {
        if (Domelements.fileimgbox) {
            Domelements.fileimgbox.style.display = 'block';
            Domelements.fileimgbox.innerHTML = '<i class="fa fa-plus"></i>';
        }
    }
    
    // 更新文件预览
    function filePreviewthuer(latestFile) {
        if (Domelements.fileimgbox) {
            Domelements.fileimgbox.style.display = 'block';
            
            // 判断是图片还是视频还是音频，图片则生成img元素，视频则生成video元素，音频则生成audio元素
            if (latestFile.type.startsWith('image/')) {
                Domelements.fileimgbox.innerHTML = '<img src="' + URL.createObjectURL(latestFile) + '" alt="">';
            } else if (latestFile.type.startsWith('video/')) {
                Domelements.fileimgbox.innerHTML = '<video src="' + URL.createObjectURL(latestFile) + '" controls></video>';
            } else if (latestFile.type.startsWith('audio/')) {
                Domelements.fileimgbox.innerHTML = '<audio src="' + URL.createObjectURL(latestFile) + '" controls></audio>';
            } else {
                Domelements.fileimgbox.innerHTML = '<i class="fa fa-file"></i>';
            }
        }
    }
    
    // 上传图片函数
    function uploadImageads(file) {
        if (file && uploadState.fileyes === 0 && file.size > 0) {
            const formData = new FormData();
            formData.append('file', file); // 添加文件数据
            
            $.ajax({
                url: '/api/upadsimg.php',    
                type: 'POST',
                data: formData, // 直接传递formData对象  
                contentType: false, // 不设置内容类型  
                processData: false, // 不处理发送的数据  
                dataType: 'json', // 指定返回的数据类型为JSON  
                success: function(data) { // data已经是解析后的对象  
                    try {    
                        if (data.code === 200) {
                            const fileInput = document.getElementById(uploadState.currentFileInputId);
                            if (fileInput) {
                                fileInput.value = data.url;
                            }
                            
                            upadsimageserr('<span class="fileyes"><i class="fa fa-check-square-o"></i>文件上传成功</span>');
                            
                            setTimeout(function() {
                                upadsimageserrtime();//定时初始化错误提示
                            }, 2500);
                            uploadState.fileyes = 200;
                            uploadState.filesarr = null; // 清空文件对象
                        } else if (data.code === 500) {
                            upadsimageserr(data.meg);
                            uploadState.fileyes = 1;
                            uploadState.filesarr = null;
                        }else if (data.code === 600) {
                            upadsimageserr('文件上传失败');
                            uploadState.fileyes = 1;
                        } else {
                            upadsimageserr('服务器响应错误');
                            uploadState.fileyes = 1;
                        }
                    } catch (e) {    
                        upadsimageserr('服务器响应错误');
                        uploadState.fileyes = 1;  
                    }    
                },    
                error: function(jqXHR, textStatus, errorThrown) {    
                    upadsimageserr('上传文件时发生错误');
                    uploadState.fileyes = 1;
                }    
            });
        }
    }
    
    // 重置所有状态
    function allnull() {
        upadsimageserrtime(); // 清除错误信息
        fileInfonull();       // 重置文件信息显示
        filePreview();        // 重置文件预览界面
        uploadState.filesarr = null;         // 清空文件对象
        uploadState.fileyes = 0;             // 重置上传状态
        
        if (Domelements.fileUpload) {
            Domelements.fileUpload.value = ''; // 清空文件输入框内容，以便重新选择相同文件
        }
        
        if (Domelements.fileimgbox) {
            Domelements.fileimgbox.replaceWith(Domelements.fileimgbox.cloneNode(true));
            // 重新绑定点击事件
            Domelements.fileimgbox = document.getElementById('fileimgbox');
            bindFileImgBoxClick();
        }
        
        if (Domelements.openUploadOverlay) {
            Domelements.openUploadOverlay.replaceWith(Domelements.openUploadOverlay.cloneNode(true));
            // 重新绑定点击事件
            Domelements.openUploadOverlay = document.getElementById('openUploadOverlay');
            bindUploadButtonClick();
        }                   
    }
    
    // 绑定文件预览区域点击事件
    function bindFileImgBoxClick() {
        if (Domelements.fileimgbox) {
            Domelements.fileimgbox.addEventListener('click', function() {
                if (Domelements.fileUpload) {
                    Domelements.fileUpload.click();
                }
            });
        }
    }
    
    // 绑定文件选择事件
    function bindFileChangeEvent() {
        if (Domelements.fileUpload) {
            Domelements.fileUpload.addEventListener('change', function() {
                upadsimageserrtime(); // 清除错误信息
                fileInfonull();       // 重置文件信息显示
                filePreview();        // 重置文件预览界面
                uploadState.filesarr = null;         // 清空文件对象
                uploadState.fileyes = 0;             // 重置上传状态
                
                uploadState.filesarr = Domelements.fileUpload.files; // 获取选择的文件
                
                if (!uploadState.filesarr || uploadState.filesarr.length !== 1) {
                    return;
                }
                
                const file = uploadState.filesarr[0]; // 获取第一个文件
                const filesize = file.size; // 获取文件大小
                const fllesizeMBs = filesize / 1024; // 转换文件大小单位为KB
                const fllesizeMB = fllesizeMBs.toFixed(2);
                const filetype = file.type; // 获取文件类型
                const filename = file.name; // 获取文件名
                const fileext = filename.split('.').pop(); // 获取文件扩展名
                
                // 判断是否是空文件
                if (filesize <= 0) {
                    upadsimageserr('文件不能为空');
                    uploadState.filesarr = null; // 清空文件对象
                    return;
                } 
                
                // MIME 类型校验
                if (!UPconfig.allowedFileTypes.includes(filetype)) {
                    upadsimageserr('文件格式不支持');
                    uploadState.filesarr = null; // 清空文件对象
                    return;
                }
                
                // 对于 SVG 等特殊情况，补充扩展名校验
                if (fileext.toLowerCase() === 'svg' && !filetype.endsWith('svg+xml')) {
                    upadsimageserr('文件格式不支持');
                    uploadState.filesarr = null; // 清空文件对象
                    return;
                }
                
                if (filesize > UPconfig.filesizekb * 1024) {
                    upadsimageserr('文件不能超过' + UPconfig.filesizekb + 'KB');
                    uploadState.filesarr = null; // 清空文件对象
                    return;
                }
                
                fileInfothuer(fllesizeMB, filetype, fileext); // 更新文件信息
                filePreviewthuer(file); // 更新文件预览
            });
        }
    }
    
    // 绑定上传按钮点击事件
    function bindUploadButtonClick() {
        if (Domelements.openUploadOverlay) {
            Domelements.openUploadOverlay.addEventListener('click', function() {
                if (uploadState.filesarr && uploadState.filesarr.length > 0) {
                    const file = uploadState.filesarr[0];
                    
                    if (uploadState.fileyes === 0) {
                        uploadImageads(file);
                    } else if (uploadState.fileyes === 1) {
                        upadsimageserr('请重新选择文件后再试');
                    } else {
                        upadsimageserr('文件已上传 请重新选择文件');
                    }
                } else {
                    upadsimageserr('请先选择文件');
                }
            });
        }
    }
    
    // 绑定关闭弹窗事件
    function bindCloseOverlayEvent() {
        if (Domelements.closeUploadOverlay && Domelements.uploadOverlay) {
            Domelements.closeUploadOverlay.addEventListener('click', function() {
                Domelements.uploadOverlay.style.display = 'none';
                allnull(); // 重置所有
            });
        }
    }
    
    // 初始化上传弹窗
    function initUploadOverlay() {
        if (Domelements.uploadOverlay) {
            Domelements.uploadOverlay.style.display = 'none';
        }
         // 设置文件选择器的accept属性，限制允许的文件类型
        if (Domelements.fileUpload) {
            // 将MIME类型数组转换为逗号分隔的字符串
            Domelements.fileUpload.accept = UPconfig.allowedFileTypes.join(',');
        }
        
        bindCloseOverlayEvent();
        bindFileImgBoxClick();
        bindFileChangeEvent();
        bindUploadButtonClick();
    }
    
    // 初始化所有上传按钮
    function initUploadButtons() {
        const uploadButtons = [
            { buttonId: 'adsimgup-hf', inputId: 'adsimg-hf' },
            { buttonId: 'adsimgup-rowhf', inputId: 'adsimg-rowhf' },
            { buttonId: 'adsimgup-yxj', inputId: 'adsimg-yxj' },
            { buttonId: 'adsimgup-ybl', inputId: 'adsimg-ybl' },
            { buttonId: 'adsimgup-left', inputId: 'adsimg-left' },
            { buttonId: 'adsimgup-right', inputId: 'adsimg-right' }
        ];
        
        uploadButtons.forEach(({ buttonId, inputId }) => {
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function() {
                    uploadState.currentFileInputId = inputId;
                    
                    if (Domelements.uploadOverlay) {
                        Domelements.uploadOverlay.style.display = 'flex';
                        allnull(); // 重置所有状态
                    }
                });
            }
        });
    }
    
    // 初始化
    function init() {
        initUploadOverlay();
        initUploadButtons();
    }

    // 启动应用
    init();
});