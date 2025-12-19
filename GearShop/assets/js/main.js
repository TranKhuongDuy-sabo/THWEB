// Sự kiện khi load trang xong
document.addEventListener("DOMContentLoaded", function() {
    
    var el = document.getElementById("wrapper");
    var toggleButton = document.getElementById("menu-toggle");

    if (toggleButton) {
        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    }
});