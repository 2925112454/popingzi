document.addEventListener('DOMContentLoaded', function() {
    function funsubgoback(url) {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            if(url){
                window.location.href = url;
            }else{
                window.location.href = "/";
            }            
        }
    }
    const subgoback = document.querySelectorAll('.subgoback');
    if(subgoback.length > 0){
        subgoback.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const url = button.getAttribute('data-url');
                funsubgoback(url);
            });
        });
    }
})