from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity



def create_content_similarity(df):

    content = (
        df["title"].fillna("")
        + " "
        + df["body"].fillna("")
    )


    vectorizer = TfidfVectorizer(
        stop_words="english"
    )


    tfidf_matrix = vectorizer.fit_transform(
        content
    )


    similarity_matrix = cosine_similarity(
        tfidf_matrix
    )


    return similarity_matrix