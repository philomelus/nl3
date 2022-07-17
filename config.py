import os
from dotenv import load_dotenv

basedir = os.path.abspath(os.path.dirname(__file__))
load_dotenv(os.path.join(basedir, ".env"))


class Config(object):
    # Run below lines from python shell after import security.  OK to store these
    # here, as they are (and should be) replaced when deploying.
    # --> secrets.token_urlsafe()
    SECRET_KEY = os.environ.get(
        "SECRET_KEY", "EBLqFgCdF7kguR4KTyFn86F0ArP31YUuimdDWJ4C2PA"
    )
    # --> secrets.SystemRandom().getrandbits(128)
    SECURITY_PASSWORD_SALT = os.environ.get(
        "SECURITY_PASSWORD_SALT", "19310750529974137607223013624890289622"
    )

    # SQLALCHEMY_DATABASE_URI='mysql+pymysql://newui:newuiNEWUI@gearhart.rnstech.com:3306/newui'
    SQLALCHEMY_DATABASE_URI = "sqlite:///" + os.path.join(basedir, "dev.db")
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    # SQLALCHEMY_ECHO = True

    # Get from environment so not hard coded in source
    MAIL_SERVER = os.environ.get("MAIL_SERVER")
    MAIL_PORT = os.environ.get("MAIL_PORT")
    MAIL_USE_SSL = os.environ.get("MAIL_USE_SSL")
    MAIL_USE_TLS = os.environ.get("MAIL_USE_TLS")
    MAIL_USERNAME = os.environ.get("MAIL_USERNAME")
    MAIL_PASSWORD = os.environ.get("MAIL_PASSWORD")

    ADMINS = ["user@domain"]

    SECURITY_URL_PREFIX = "/auth"
    # SECURITY_CONFIRMABLE = True
    SECURITY_CHANGEABLE = True
    SECURITY_RECOVERABLE = True
    SECURITY_REGISTERABLE = True

    TURBO_WEBSOCKET_ROUTE = "/tws"
