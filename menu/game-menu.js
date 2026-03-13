document.addEventListener("DOMContentLoaded", function () {

    const startBtn = document.getElementById("startGameBtn");
    const overlay = document.getElementById("loadingOverlay");

    startBtn.addEventListener("click", function () {
        overlay.style.display = "flex";

        setTimeout(() => {
            window.location.href = "mainarea/sdg1.php";
        }, 2000);
    });

    // Fix back-button cache issue
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            overlay.style.display = "none";
        }
    });

});