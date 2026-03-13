    document.addEventListener("DOMContentLoaded", function () {

        /* =========================
        ELEMENTS
        ========================= */
        const startScreen = document.getElementById("startScreen");
        const startBtn = document.getElementById("startBtn");
        const exitStartBtn = document.getElementById("exitStartBtn");

        const deviceSelect = document.getElementById("deviceSelect");
        const deviceButtons = document.querySelectorAll(".device-btn");

        const loadingOverlay = document.getElementById("loadingOverlay");

        const gameUI = document.getElementById("gameUI");

        const questBtn = document.getElementById("questBtn");
        const questPopup = document.getElementById("questPopup");
        const closePopup = document.getElementById("closePopup");

        const optionsBtn = document.getElementById("optionsBtn");
        const optionsPopup = document.getElementById("optionsPopup");
        const closeOptions = document.getElementById("closeOptions");
        const exitGameBtn = document.getElementById("exitGameBtn");

        function isMobileDevice() {
            const selectedDevice = localStorage.getItem("selectedDevice") || "desktop";
            return selectedDevice === "mobile";
        }

        // ===== SCREEN CONTROL FUNCTION =====
        function showScreen(screen) {
            startScreen.style.display = "none";
            if (deviceSelect) deviceSelect.style.display = "none";
            if (loadingOverlay) loadingOverlay.style.display = "none";
            gameUI.style.display = "none";

            if (screen === "start") startScreen.style.display = "flex";
            if (screen === "device") deviceSelect.style.display = "flex";
            if (screen === "loading") loadingOverlay.style.display = "flex";
            if (screen === "game") gameUI.style.display = "flex";

        }

        /* =========================
        INITIAL STATE
        ========================= */
        gameUI.style.display = "none";
        if (deviceSelect) deviceSelect.style.display = "none";
        if (loadingOverlay) loadingOverlay.style.display = "none";
        showScreen("start");

        /* =========================
        START BUTTON
        ========================= */
        startBtn.addEventListener("click", function () {
            showScreen("device");
        });

        /* =========================
        DEVICE SELECTION
        ========================= */
        if (deviceButtons.length > 0) {
            deviceButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const selectedDevice = this.dataset.device;  // ← actual clicked device
                    localStorage.setItem("selectedDevice", selectedDevice);

                    console.log("Device selected:", selectedDevice);

                    showScreen("loading");

                    setTimeout(() => {
                        showScreen("game");
                        loadQuests(selectedDevice); // pass device explicitly
                        questPopup.classList.add("active"); // ✅ open popup to see quests
                    }, 2000);
                });
            });
        }

        /* =========================
        EXIT BUTTONS
        ========================= */
        exitStartBtn.addEventListener("click", () => {
            window.location.href = "/login_registration/menu/game.php";
        });

        exitGameBtn.addEventListener("click", () => {
            window.location.href = "/login_registration/menu/game.php";
        });
        
        /* =========================
        COINS (Persistent)
        ========================= */
        let coins = parseInt(localStorage.getItem("coins")) || 0;
        const coinDisplay = document.getElementById("coinCount");
        coinDisplay.textContent = coins;

        function updateCoins(amount) {
            coins += amount;
            coinDisplay.textContent = coins;
            localStorage.setItem("coins", coins);
        }

        /* =========================
        LEADERBOARD POINTS (Separate)
        ========================= */
        let leaderboardPoints = parseInt(localStorage.getItem("leaderboardPoints")) || 0;

        function updateLeaderboardPoints(coinReward) {

            let pointsEarned = Math.floor(coinReward * 0.25);

            leaderboardPoints += pointsEarned;
            localStorage.setItem("leaderboardPoints", leaderboardPoints);

            console.log("Leaderboard Points:", leaderboardPoints);

            return pointsEarned; // ✅ return only earned points
        }

        /* =========================
        QUEST POPUP
        ========================= */
        questBtn.addEventListener("click", () => {
            questPopup.classList.add("active");
        });

        closePopup.addEventListener("click", () => {
            questPopup.classList.remove("active");
        });

        window.addEventListener("click", function (e) {
            if (e.target === questPopup) {
                questPopup.classList.remove("active");
            }
            if (e.target === optionsPopup) {
                optionsPopup.classList.remove("active");
            }
        });

        /* =========================
        OPTIONS POPUP
        ========================= */
        optionsBtn.addEventListener("click", () => {
            optionsPopup.classList.add("active");
        });

        closeOptions.addEventListener("click", () => {
            optionsPopup.classList.remove("active");
        });

        /* =========================
        QUEST SYSTEM + COOLDOWN
        ========================= */
        function launchGame(title, url, button, cooldownKey, showLegend, onComplete) {
            const gameContainer = document.getElementById("gameContainer");

            const gameWindow = document.createElement("div");
            gameWindow.classList.add("game-window");
            gameWindow.style.left = "0px";
            gameWindow.style.top = "0px";

            let legendHTML = "";

            if(showLegend){
                legendHTML = `
                    <div class="game-legend">
                        <div><strong>Biodegradable:</strong> 🍌 🍎 🥕 🍃 🌽 🥬 🍞 🍂</div>
                        <div><strong>Non-Biodegradable:</strong> 🥤 🧴 🛍️ 📦 🥫 🧃 🧻 🪣</div>
                        <div><strong>Hazardous:</strong> 🔋 💊 🧪 ☣️ 🪫 💉 ⚗️</div>
                    </div>
                `;
            }

            gameWindow.innerHTML = `
                <div class="game-header">
                    <span>${title}</span>
                    <button class="closeGame">✖</button>
                </div>

                <div class="game-body">
                    <iframe src="${url}"></iframe>
                </div>

                ${legendHTML}
            `;

            gameContainer.appendChild(gameWindow);

            window.addEventListener("message", function(event){
                if(event.data.type === "quest_complete"){

                    const cooldownDuration = 60000; // 60 seconds cooldown
                    const cooldownEnd = Date.now() + cooldownDuration;

                    // Start cooldown regardless of success or failure
                    localStorage.setItem(cooldownKey, cooldownEnd);
                    startCooldown(button, cooldownKey);

                    if(event.data.success){

                        if(onComplete){
                            onComplete(); // reward player
                        }

                    }else{

                        alert("Quest Failed. Try again!");

                        /* START COOLDOWN EVEN ON LOSS */
                        const cooldownEnd = Date.now() + 60000;
                        localStorage.setItem(cooldownKey, cooldownEnd);
                        startCooldown(button, cooldownKey);

                    }

                    gameWindow.remove();

                }

            }, { once: true });

            /* =========================
            WINDOW DRAG SYSTEM
            ========================= */

            const header = gameWindow.querySelector(".game-header");

            let isDragging = false;
            let startX = 0;
            let startY = 0;
            let windowX = 0;
            let windowY = 0;

            header.addEventListener("mousedown", (e) => {

                isDragging = true;

                // mouse starting position
                startX = e.clientX;
                startY = e.clientY;

                // window starting position
                windowX = gameWindow.offsetLeft;
                windowY = gameWindow.offsetTop;

                document.addEventListener("mousemove", dragWindow);
                document.addEventListener("mouseup", stopDrag);

            });

            function dragWindow(e){

                if(!isDragging) return;

                const container = document.getElementById("gameContainer");

                const maxX = container.clientWidth - gameWindow.offsetWidth;
                const maxY = container.clientHeight - gameWindow.offsetHeight;

                let newX = windowX + (e.clientX - startX);
                let newY = windowY + (e.clientY - startY);

                // boundaries
                newX = Math.max(0, Math.min(newX, maxX));
                newY = Math.max(0, Math.min(newY, maxY));

                gameWindow.style.left = newX + "px";
                gameWindow.style.top = newY + "px";

            }

            function stopDrag(){

                isDragging = false;

                document.removeEventListener("mousemove", dragWindow);
                document.removeEventListener("mouseup", stopDrag);

            }

            // Close window
            gameWindow.querySelector(".closeGame").addEventListener("click", () => {
                const existingCooldown = localStorage.getItem(cooldownKey);
                if(!existingCooldown) {
                    const cooldownEnd = Date.now() + 20000; // 20s for leaving early
                    localStorage.setItem(cooldownKey, cooldownEnd);
                    startCooldown(button, cooldownKey);
                }
                gameWindow.remove();
            });
        }
        function bindQuestButtons(selectedDevice) {
            const completeButtons = document.querySelectorAll(".completeQuestBtn");

            completeButtons.forEach((button, index) => {
                const cooldownKey = "questCooldown_" + selectedDevice + "_" + index;
                checkCooldown(button, cooldownKey);

                button.addEventListener("click", function () {

                    const reward = parseInt(this.dataset.reward);
                    const title = this.textContent || "Quest"; // fallback title
                    const url = this.dataset.url || "default_game.php"; // desktop game URL
                    if(url.includes("sdg1_learning.php")){
                        fetch("/login_registration/menu/mainarea/quests/start_learning_ai.php")
                        .then(() => console.log("Learning AI server started"))
                        .catch(err => console.error("Failed to start Learning AI server:", err));
                    }
                    const showLegend = this.dataset.legend === "true";
                    const isVerifier = this.dataset.verifier === "true"; // mobile verifier

                    // Close quest popup
                    questPopup.classList.remove("active");

                    if (isMobileDevice() && isVerifier) {
                        // Mobile quests with verifier
                        openVerifier(this.dataset.questType, () => {
                            // Only reward after verifier approves
                            const earnedPoints = updateLeaderboardPoints(reward);
                            updateCoins(reward);

                            alert(`Quest Completed!\n+${reward} Coins 💰\n+${earnedPoints} Leaderboard Points 🏆`);

                            // Send leaderboard points to server
                            fetch("/login_registration/menu/submit_score.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                credentials: "include",
                                body: "points=" + earnedPoints
                            })
                            .then(res => res.json())
                            .then(data => console.log("Server response:", data))
                            .catch(err => console.error("Leaderboard Error:", err));
                        });

                    } else {
                        // Desktop quests or mobile quests without verifier
                        launchGame(title, url, button, cooldownKey, showLegend, () => {
                            const earnedPoints = updateLeaderboardPoints(reward);
                            updateCoins(reward);

                            alert(`Quest Completed!\n+${reward} Coins 💰\n+${earnedPoints} Leaderboard Points 🏆`);

                            // Send leaderboard points to server
                            fetch("/login_registration/menu/submit_score.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                credentials: "include",
                                body: "points=" + earnedPoints
                            })
                            .then(res => res.json())
                            .then(data => console.log("Server response:", data))
                            .catch(err => console.error("Leaderboard Error:", err));
                        });
                    }

                });
            });
        }

        function checkCooldown(button, key) {
            const cooldownEnd = localStorage.getItem(key);
            if (!cooldownEnd) return;

            if (Date.now() < cooldownEnd) {
                startCooldown(button, key);
            } else {
                localStorage.removeItem(key);
            }
        }

        function startCooldown(button, key) {
            button.disabled = true;

            const interval = setInterval(() => {
                const remaining = Math.ceil((localStorage.getItem(key) - Date.now()) / 1000);

                if (remaining <= 0) {
                    clearInterval(interval);
                    button.disabled = false;
                    button.textContent = "Complete Quest";
                    localStorage.removeItem(key);
                } else {
                    button.textContent = "Cooldown: " + remaining + "s";
                }
            }, 1000);
        }

        function loadQuests(selectedDevice) {
            const questList = questPopup.querySelector("ul");
            questList.innerHTML = "";

            deviceQuests[selectedDevice].forEach((quest, index) => {
                const li = document.createElement("li");
                li.innerHTML = `
                    ${quest.title} <br>
                    <strong>Reward:</strong> ${quest.reward} Coins <br>
                    <button class="completeQuestBtn" 
                            data-reward="${quest.reward}" 
                            data-verifier="${quest.verifier ? 'true' : 'false'}" 
                            data-quest-type="${quest.questType || ''}"
                            data-url="${quest.url}"
                            data-legend="${quest.showLegend ? 'true' : 'false'}">
                        Complete Quest
                    </button>
                `;
                questList.appendChild(li);
            });

            bindQuestButtons(selectedDevice);
        }

        // ===== DEVICE-SPECIFIC QUESTS =====
       const deviceQuests = {
            desktop: [
                { 
                    title: "🌱 SDG 12 Awareness Game", 
                    reward: 50,
                    url: "quests/sdg12_awareness.php",
                    showLegend: true
                },
                { 
                    title: "📚 SDG Learning Game", 
                    reward: 100,
                    url: "quests/sdg1_learning.php" 
                },
                { 
                    title: "📝 Research Report Simulation", 
                    reward: 75,
                    url: "quests/sdg1_research.php" 
                }
            ],
            mobile: [
                { 
                    title: "🗑 Clean up a local park", 
                    reward: 50, 
                    verifier: true, 
                    questType: "clean_park" 
                },
                { 
                    title: "📷 Take photos of community recycling", 
                    reward: 75, 
                    verifier: true, 
                    questType: "take_photos" 
                },
                { 
                    title: "📱 Share SDG 1 tips on social media", 
                    reward: 50, 
                    verifier: true, 
                    questType: "share_tips" 
                }
            ]
        };

    });