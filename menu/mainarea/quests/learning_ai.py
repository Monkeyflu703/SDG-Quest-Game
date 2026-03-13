from flask import Flask, request, jsonify
from flask_cors import CORS
import random

app = Flask(__name__)
CORS(app)

player_model = {
    "skill": 0.5,
    "last_question": None
}

questions = [

# ================= EASY =================
{
"id": 1,
"question": "What is the main goal of SDG 1?",
"choices": [
"End poverty in all its forms everywhere",
"Increase global trade",
"Improve internet access",
"Promote tourism"
],
"answer": 0,
"difficulty": 0.3
},
{
"id": 2,
"question": "Which SDG aims to end hunger and improve nutrition?",
"choices": [
"SDG 2",
"SDG 3",
"SDG 8",
"SDG 12"
],
"answer": 0,
"difficulty": 0.3
},
{
"id": 3,
"question": "What is the main focus of SDG 3?",
"choices": [
"Ensure healthy lives for all",
"Build more hospitals",
"Increase private healthcare profits",
"Reduce medical research"
],
"answer": 0,
"difficulty": 0.3
},
{
"id": 4,
"question": "What is the primary goal of SDG 4?",
"choices": [
"Provide inclusive quality education",
"Build private universities",
"Reduce school days",
"Limit teacher hiring"
],
"answer": 0,
"difficulty": 0.3
},

# ================= MEDIUM =================
{
"id": 5,
"question": "Which action supports SDG 1 most directly?",
"choices": [
"Providing conditional cash transfers to low-income households tied to education and healthcare",
"Expanding infrastructure in wealthy districts",
"Offering short-term disaster relief only",
"Reducing taxes for all income levels"
],
"answer": 0,
"difficulty": 0.5
},
{
"id": 6,
"question": "Which program addresses chronic food insecurity?",
"choices": [
"Supporting smallholder farmers with training and irrigation",
"Focusing national investment on export crops",
"Reducing government role in food distribution",
"Encouraging urban populations to rely on imports"
],
"answer": 0,
"difficulty": 0.5
},
{
"id": 7,
"question": "Which intervention improves long-term population health outcomes?",
"choices": [
"Expanding vaccination programs and preventive education",
"Encouraging reliance on private health insurance markets",
"Reducing community healthcare funding",
"Increasing hospital capacity without preventive care"
],
"answer": 0,
"difficulty": 0.5
},
{
"id": 8,
"question": "Which initiative promotes equitable education systems?",
"choices": [
"Providing teacher training and resources to underserved communities",
"Increasing tuition to fund infrastructure",
"Limiting education spending to high-performing regions",
"Reducing national standards"
],
"answer": 0,
"difficulty": 0.5
},

# ================= HARD =================
{
"id": 9,
"question": "Which policy would most effectively reduce structural poverty?",
"choices": [
"Developing long-term social protection systems including unemployment benefits and pensions",
"Encouraging private charities to manage programs independently",
"Focusing development exclusively on urban economic centers",
"Replacing welfare programs with temporary emergency grants"
],
"answer": 0,
"difficulty": 0.7
},
{
"id": 10,
"question": "A government invests heavily in drought-resistant crops and irrigation systems for rural farmers. Which SDG is most directly supported?",
"choices": [
"SDG 2: Zero Hunger",
"SDG 9: Industry, Innovation and Infrastructure",
"SDG 8: Decent Work and Economic Growth",
"SDG 13: Climate Action"
],
"answer": 0,
"difficulty": 0.7
},
{
"id": 11,
"question": "Which approach reflects the long-term intent of SDG 3?",
"choices": [
"Strengthening universal health coverage and preventive care",
"Prioritizing private healthcare expansion in major cities",
"Reducing public health funding while increasing specialized facilities",
"Focusing primarily on elective procedures"
],
"answer": 0,
"difficulty": 0.7
},
{
"id": 12,
"question": "A country allocates extra funding to train teachers and improve resources in rural schools. Which SDG is this supporting?",
"choices": [
"SDG 4: Quality Education",
"SDG 8: Decent Work and Economic Growth",
"SDG 10: Reduced Inequalities",
"SDG 9: Industry, Innovation and Infrastructure"
],
"answer": 0,
"difficulty": 0.7
}

]

def choose_question():

    skill = player_model["skill"]

    # Sort questions by closeness to skill
    sorted_questions = sorted(
        questions,
        key=lambda q: abs(q["difficulty"] - skill)
    )

    # Take top 2 closest questions
    candidates = sorted_questions[:2]

    # Avoid repeating the same question
    if player_model["last_question"]:
        candidates = [
            q for q in candidates
            if q["id"] != player_model["last_question"]
        ] or candidates

    q = random.choice(candidates)

    player_model["last_question"] = q["id"]

    return q


@app.route("/question")
def get_question():

    q = choose_question()

    return jsonify({
        "id": q["id"],
        "question": q["question"],
        "choices": q["choices"]
    })


@app.route("/answer", methods=["POST"])
def answer():

    data = request.json
    question_id = data["id"]
    choice = data["choice"]

    q = next(x for x in questions if x["id"] == question_id)

    correct = (choice == q["answer"])

    if correct:
        player_model["skill"] += 0.1
    else:
        player_model["skill"] -= 0.1

    player_model["skill"] = max(0, min(1, player_model["skill"]))

    return jsonify({
        "correct": correct,
        "skill": player_model["skill"]
    })


if __name__ == "__main__":
    app.run(port=5000)