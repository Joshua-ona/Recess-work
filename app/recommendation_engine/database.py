from sqlalchemy import create_engine


def get_connection():

    engine = create_engine(
        "mysql+pymysql://root:@127.0.0.1/discussion_forum"
    )

    return engine