let score = 0
let time = 60
let gameStarted = false

const scoreEl = document.getElementById("score")
const timeEl = document.getElementById("time")
const typeEl = document.getElementById("currentType")
const cursor = document.getElementById("trashCursor")

const startBtn = document.getElementById("startGameBtn")

startBtn.addEventListener("click", () => {

    document.getElementById("startOverlay").style.display = "none"

    gameStarted = true

    startGame()

})

function startGame(){

    /* TIMER */

    const timer = setInterval(()=>{

        time--

        timeEl.textContent = time

        if(time <= 0){

            clearInterval(timer)

            endGame()

        }

    },1000)

    setInterval(()=>{

        if(gameStarted){
            spawnTrash()
        }

    },500)

}

let currentType = "bio"

const trashTypes = [
    {
        type: "bio",
        label: "Biodegradable",
        icons: ["🍌","🍎","🥕","🍃","🌽","🥬","🍞","🍂"]
    },
    {
        type: "nonbio",
        label: "Non-Biodegradable",
        icons: ["🥤","🧴","🛍️","📦","🥫","🧃","🧻","🪣"]
    },
    {
        type: "hazard",
        label: "Hazardous",
        icons: ["🔋","💊","🧪","☣️","🪫","💉","⚗️"]
    }
]

/* ========================
CURSOR FOLLOW
======================== */

document.addEventListener("mousemove", e=>{
    cursor.style.left = e.clientX + "px"
    cursor.style.top = e.clientY + "px"
})

/* ========================
CHANGE TRASH TYPE
======================== */

function changeTrashType(){

    const random = trashTypes[Math.floor(Math.random()*trashTypes.length)]

    currentType = random.type
    typeEl.textContent = random.label

    cursor.textContent = "🗑"

}

setInterval(changeTrashType,15000)

changeTrashType()

/* ========================
SPAWN TRASH
======================== */

function spawnTrash(){

    const randomType = trashTypes[Math.floor(Math.random()*trashTypes.length)]

    const icon = randomType.icons[Math.floor(Math.random()*randomType.icons.length)]

    const trash = document.createElement("div")

    trash.classList.add("trash")
    trash.textContent = icon
    trash.dataset.type = randomType.type

    trash.style.left = Math.random()*window.innerWidth+"px"
    trash.style.top = "-40px"

    document.getElementById("game").appendChild(trash)

    fall(trash)
}

/* ========================
FALLING
======================== */

function fall(trash){

    let y = -40

    const fallLoop = setInterval(()=>{

        y += 3
        trash.style.top = y + "px"
        trash.style.left =
            trash.offsetLeft + (Math.random()*4 - 2) + "px"

        checkCollision(trash)

        if(y > window.innerHeight){
            trash.remove()
            clearInterval(fallLoop)
        }

    },20)

}


/* ========================
COLLECT TRASH
======================== */

function checkCollision(trash){

    const trashRect = trash.getBoundingClientRect()
    const cursorRect = cursor.getBoundingClientRect()

    const hit =
        trashRect.left < cursorRect.right &&
        trashRect.right > cursorRect.left &&
        trashRect.top < cursorRect.bottom &&
        trashRect.bottom > cursorRect.top

    if(hit){

        const type = trash.dataset.type

        if(type === currentType){
            score++
        }else{
            score--
        }

        scoreEl.textContent = score

        trash.remove()
    }

}

function endGame(){

    alert("Time's up! Final Score: " + score)

    if(score >= 20){

        parent.postMessage(
        {type:"quest_complete", success:true},
        "*"
        )

    }else{

        parent.postMessage(
        {type:"quest_complete", success:false},
        "*"
        )

    }

}


