import pandas as pd
import pickle
import os

from sklearn.feature_extraction.text import TfidfVectorizer


# PROJECT PATHS

# Current folder:
# app/recommendation_engine

BASE_DIR = os.path.dirname(
    os.path.abspath(__file__)
)


DATASET_PATH = os.path.join(
    BASE_DIR,
    "datasets",
    "stackoverflow_combined.csv"
)


MODELS_DIR = os.path.join(
    BASE_DIR,
    "models"
)


# Create models folder if missing

os.makedirs(
    MODELS_DIR,
    exist_ok=True
)


print("Dataset path:")
print(DATASET_PATH)

print()

# LOAD DATASET

print("Loading dataset...")

df = pd.read_csv(
    DATASET_PATH
)


print(
    "Dataset loaded"
)

print(
    "Total discussions:",
    len(df)
)


print()


# CLEAN DATA

print("Cleaning data...")


df = df.fillna("")


print(
    "Cleaning complete"
)


print()

# CREATE TRAINING TEXT

print(
    "Creating discussion content..."
)


df["content"] = (

    df["title"].astype(str)

    + " "

    + df["body"].astype(str)

    + " "

    + df["tags"].astype(str)

    + " "

    + df["programming_language"].astype(str)

)



print(
    "Content created"
)


print()


# TF-IDF TRAINING


print(
    "Training TF-IDF model..."
)


vectorizer = TfidfVectorizer(

    # remove common words
    stop_words="english",

    # learn phrases
    ngram_range=(1,2),

    # maximum vocabulary size
    max_features=10000

)



tfidf_matrix = vectorizer.fit_transform(
    df["content"]
)



print(
    "TF-IDF training completed"
)


print(
    "Vector size:",
    tfidf_matrix.shape
)


print()

# SAVE TRAINED MODEL

print(
    "Saving trained model..."
)


# Save vocabulary model

with open(
    os.path.join(
        MODELS_DIR,
        "tfidf_vectorizer.pkl"
    ),
    "wb"
) as file:

    pickle.dump(
        vectorizer,
        file
    )



# Save discussion vectors

with open(
    os.path.join(
        MODELS_DIR,
        "discussion_vectors.pkl"
    ),
    "wb"
) as file:

    pickle.dump(
        tfidf_matrix,
        file
    )



# Save discussion information

discussion_data = df[
    [
        "question_id",
        "title",
        "tags",
        "programming_language"
    ]
]


discussion_data.to_pickle(

    os.path.join(
        MODELS_DIR,
        "discussions.pkl"
    )

)



print()
print("==============================")
print("MODEL TRAINED SUCCESSFULLY")
print("==============================")

print(
    "Questions trained:",
    len(df)
)


print()

print(
    "Model files saved in:"
)

print(
    MODELS_DIR
)
