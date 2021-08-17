
import logging
import os
from logging.handlers import SMTPHandler, RotatingFileHandler

from flask import Flask, g
from flask_security import Security, SQLAlchemyUserDatastore
from flask_sqlalchemy import SQLAlchemy

from config import Config


db = SQLAlchemy()
app = Flask(__name__)
app.config.from_object(Config)

# Setup logging and error reporting
if not app.debug and not app.testing:
    if app.config['MAIL_SERVER']:
        auth = None
        if app.config['MAIL_USERNAME'] or app.config['MAIL_PASSWORD']:
            auth = (app.config['MAIL_USERNAME'], app.config['MAIL_PASSWORD'])
        secure = None
        if app.config['MAIL_USE_TLS']:
            secure = ()
        mail_handler = SMTPHandler(
            mailhost=(app.config['MAIL_SERVER'], app.config['MAIL_PORT']),
            fromaddr='no-reply@' + app.config['MAIL_SERVER'],
            toaddrs=app.config['ADMINS'],
            subject='Newsledger Failure',
            credentials=auth, secure=secure)
        mail_handler.setLevel(logging.ERROR)
        app.logger.addHandler(mail_handler)

    if not os.path.exists('logs'):
        os.mkdir('logs')
    file_handler = RotatingFileHandler('logs/newsledger.log', maxBytes=10240,
                                       backupCount=10)
    file_handler.setFormatter(logging.Formatter(
        '%(asctime)s %(levelname)s: %(message)s [in %(pathname)s:%(lineno)d]'))
    file_handler.setLevel(logging.INFO)
    app.logger.addHandler(file_handler)

    app.logger.setLevel(logging.INFO)
    app.logger.info('Newsledger startup.')

# Initialize flask-sqlalchemy
db.init_app(app)

# Set up flask-security
from nl.models.auth import Role, User
app.user_datastore = SQLAlchemyUserDatastore(db, User, Role)
app.security = Security(app, app.user_datastore)

# Register main glue blueprint
from nl import main
app.register_blueprint(main.bp)

# Register API blueprint
from nl import api
app.register_blueprint(api.bp, url_prefix='/api')
    
# Register administration blueprint
from nl import admin
app.register_blueprint(admin.bp)

# Register customers blueprint
from nl import customers
app.register_blueprint(customers.bp)

# Register routes blueprint
from nl import routes
app.register_blueprint(routes.bp)

# Register stores and racks blueprint
from nl import stores
app.register_blueprint(stores.bp)

from nl import models
from nl.models import auth
from nl.models import config
from nl.models import customers
from nl.models import routes

