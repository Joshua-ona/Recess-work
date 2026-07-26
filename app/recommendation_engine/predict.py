import os
import pickle
import pandas as pd

from sklearn.metrics.pairwise import cosine_similarity


# PATHS


BASE_DIR = os.path.dirname(
    os.path.abspath(__file__)
)


MODELS_DIR = os.path.join(
    BASE_DIR,
    "models"
)

# LOAD TRAINED MODEL


print("Loading trained model...")


with open(
    os.path.join(
        MODELS_DIR,
        "tfidf_vectorizer.pkl"
    ),
    "rb"
) as file:

    vectorizer = pickle.load(file)



with open(
    os.path.join(
        MODELS_DIR,
        "discussion_vectors.pkl"
    ),
    "rb"
) as file:

    discussion_vectors = pickle.load(file)



discussions = pd.read_pickle(

    os.path.join(
        MODELS_DIR,
        "discussions.pkl"
    )

)


print("Model loaded successfully")

print(
    "Available discussions:",
    len(discussions)
)


# RECOMMEND FUNCTION


def recommend(
        user_interest,
        limit=5
):

    """
    Recommend discussions based on
    text similarity.
    """


    # Convert user text into vector

    user_vector = vectorizer.transform(
        [
            user_interest
        ]
    )


    # Compare user vector with
    # all discussion vectors

    similarity_scores = cosine_similarity(

        user_vector,

        discussion_vectors

    )[0]



    # Get highest scores

    top_indexes = similarity_scores.argsort()[::-1][:limit]



    results = []



    for index in top_indexes:


        discussion = discussions.iloc[index]


        results.append({

            "title":
                discussion["title"],


            "tags":
                discussion["tags"],


            "language":
                discussion["programming_language"],


            "similarity":
                round(
                    float(
                        similarity_scores[index]
                    )
                    * 100,
                    2
                )

        })



    return results


# TEST



if __name__ == "__main__":


    interest = input(
        "\nEnter programming interest: "
    )


    recommendations = recommend(
        interest
    )


    print(
        "\nRecommended discussions:\n"
    )


    for item in recommendations:


        print(
            "Title:",
            item["title"]
        )


        print(
            "Language:",
            item["language"]
        )


        print(
            "Tags:",
            item["tags"]
        )


        print(
            "Similarity:",
            item["similarity"],
            "%"
        )


        print(
            "----------------------"
        )
