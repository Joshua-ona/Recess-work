from user_recommender import recommend_for_user


print("Generating recommendations...")


results = recommend_for_user(
    user_id=5
)


if results:

    print("\nRecommended Discussions:\n")

    for item in results:

        print(
            item["title"],
            "=> Score:",
            item["score"]
        )

else:

    print(
        "No recommendations found"
    )