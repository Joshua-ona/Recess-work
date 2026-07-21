"""
Content similarity model.

Uses TF-IDF vectorization and cosine similarity
to measure semantic similarity between discussions.
"""


from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity



def create_content_similarity(df):
    """
    Creates a similarity matrix between discussions.

    Parameters:
        df:
            DataFrame containing:
            - title
            - body

    Returns:
        cosine similarity matrix
    """


    content = (
        df["title"]
        .fillna("")
        .astype(str)
        +
        " "
        +
        df["body"]
        .fillna("")
        .astype(str)
    )


    vectorizer = TfidfVectorizer(

        # Ignore common words
        stop_words="english",

        # Learn short phrases
        ngram_range=(1,2),

        # Ignore extremely rare words
        min_df=1,

        # Limit memory usage
        max_features=5000

    )


    tfidf_matrix = vectorizer.fit_transform(
        content
    )


    similarity_matrix = cosine_similarity(
        tfidf_matrix
    )


    return similarity_matrix
