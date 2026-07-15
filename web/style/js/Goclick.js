document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', e => {
    const el = e.target.closest('.Goclick');
    if (!el) return;
    const url = el.dataset.url;
    const target = el.dataset.target || '_self';
    if (!url) return;
    if (
      e.ctrlKey ||       // Windows Ctrl+点击
      e.metaKey ||       // Mac Cmd+点击
      e.button === 1     // 鼠标中键
    ) {
      window.open(url, '_blank'); // 新标签
      e.preventDefault();
      return;
    }
    if (target === '_blank') {
      window.open(url, '_blank', 'noopener noreferrer');
    } else {
      location.href = url;
    }
    e.preventDefault();
  });
  document.addEventListener('mousedown', e => {
    if (e.button === 1 && e.target.closest('.Goclick')) {
      e.preventDefault();
    }
  });
});