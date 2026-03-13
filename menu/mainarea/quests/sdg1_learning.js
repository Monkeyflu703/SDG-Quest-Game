let currentQuestion = null
let streak = 0
let attempts = 0
const requiredStreak = 10
let questFinished = false

const flame = document.getElementById("streakFlame")
const streakText = document.getElementById("streakText")

function updateFlame(){

    streakText.innerText = streak + " / 10"

    flame.className = ""

    if(streak >= 8){
        flame.classList.add("flame-max")
    }
    else if(streak >= 6){
        flame.classList.add("flame-hot")
    }
    else if(streak >= 3){
        flame.classList.add("flame-warm")
    }
    else{
        flame.classList.add("flame-cold")
    }

}

function loadQuestion(){

    attempts = 0

    fetch("http://localhost:5000/question")

    .then(res => res.json())

    .then(data => {

        currentQuestion = data

        const questionEl = document.getElementById("question")
        const choicesDiv = document.getElementById("choices")

        questionEl.innerText = data.question

        choicesDiv.innerHTML = ""

        data.choices.forEach((choice,index)=>{

            const btn = document.createElement("button")

            btn.innerText = choice

            btn.addEventListener("click",()=>submitAnswer(btn,index))

            choicesDiv.appendChild(btn)

        })

    })

}

function enableButtons(){

    document.querySelectorAll("#choices button")
    .forEach(btn => btn.disabled = false)

}

function disableButtons(){

    document.querySelectorAll("#choices button")
    .forEach(btn => btn.disabled = true)

}

function submitAnswer(button,choice){

    disableButtons()

    fetch("http://localhost:5000/answer",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({
            id:currentQuestion.id,
            choice:choice
        })

    })

    .then(res=>res.json())

    .then(data=>{

        attempts++

        if(data.correct){

            button.classList.add("correct")

            streak++

            updateFlame()

            setTimeout(()=>{

                if(streak >= requiredStreak && !questFinished){

                    questFinished = true

                    parent.postMessage(
                    {type:"quest_complete", success:true},
                    "*"
                    )

                    return
                }

                loadQuestion()

            },700)

        }
        else{

            button.classList.add("wrong")

            if(attempts < 2){

                setTimeout(()=>{

                    button.classList.remove("wrong")

                    enableButtons()

                },700)

            }
            else{

                streak = 0
                updateFlame()

                setTimeout(()=>{

                    loadQuestion()

                },700)

            }

        }

    })

}

/* INITIALIZE */

updateFlame()
loadQuestion()