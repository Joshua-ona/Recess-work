from flask import Flask, jsonify
from user_recommender import recommend_for_user, recommend_groups_for_user
from calculate_score import run_batch_scoring
from apscheduler.schedulers.background import BackgroundScheduler
import atexit
import os
import sys


sys.path.append(os.path.dirname(os.path.abspath(__file__)))

app = Flask(__name__)

run_batch_scoring() # run on boot

scheduler = BackgroundScheduler()
scheduler.add_job(func=run_batch_scoring, trigger="interval", minutes=2)
scheduler.start()
atexit.register(lambda: scheduler.shutdown())

@app.route("/recommendations/<int:user_id>")
def recommendations(user_id):
    results = recommend_for_user(user_id)
    return jsonify(results)

@app.route("/recommend-groups/<int:user_id>")
def recommend_groups(user_id):
    results = recommend_groups_for_user(user_id)
    return jsonify(results)

@app.route("/")
def home():
    return {"status": "Recommendation API running"}

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5001))
    app.run(host="0.0.0.0", port=port, debug=False)