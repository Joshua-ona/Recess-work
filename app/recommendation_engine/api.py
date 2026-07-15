from flask import Flask, jsonify, request
from user_recommender import recommend_for_user, recommend_groups_for_user

app = Flask(__name__)

@app.route("/recommendations/<int:user_id>")
def recommendations(user_id):
    results = recommend_for_user(user_id)
    return jsonify(results)

@app.route("/recommend-groups/<int:user_id>") # <-- NEW ENDPOINT
def recommend_groups(user_id):
    results = recommend_groups_for_user(user_id)
    return jsonify(results)

@app.route("/")
def home():
    return {"status": "Recommendation API running"}

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5001, debug=True)