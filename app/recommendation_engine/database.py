import os
from dotenv import load_dotenv
from sqlalchemy import create_engine


load_dotenv()


def get_connection():

    database_url = os.getenv("DATABASE_URL")

    if not database_url:
        raise ValueError(
            "DATABASE_URL is missing. Check your .env file."
        )

    return create_engine(database_url)
