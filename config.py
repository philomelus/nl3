
import os

class Config(object):
    SECRET_KEY = os.environ.get('SECRET_KEY') or 'thisshouldbemoresecure'

    SQLALCHEMY_DATABASE_URI='mysql+pymysql://newui:newuiNEWUI@gearhart.rnstech.com:3306/newui'
    SQLALCHEMY_TRACK_MODIFICATIONS=False
