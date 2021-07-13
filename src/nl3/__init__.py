
from flask import Flask
from flask_sqlalchemy import SQLAlchemy

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://newui:newuiNEWUI@gearhart.rnstech.com:3306/newui'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

from .models import db

db.init_app(app)

@app.route("/")
def hello_world():
    from .models import User
    result = ""
    for user in User.query.all():
        result += "<p>{} ({})</p>".format(user.name, user.login)
    return result
    
