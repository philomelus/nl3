
import logging
import os

from flask import Flask
from config import Config
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager
from logging.handlers import SMTPHandler, RotatingFileHandler


db = SQLAlchemy()
login = LoginManager()
login.login_view = 'auth.login'


def create_app(config_class=Config):
    app = Flask(__name__)
    app.config.from_object(Config)

    db.init_app(app)
    login.init_app(app)

    # Register error handling blueprint
    from nl import errors
    app.register_blueprint(errors.bp)

    # Register main glue blueprint
    from nl import main
    app.register_blueprint(main.bp)

    # Register authentication blueprint
    from nl import auth
    app.register_blueprint(auth.bp)

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
        
    return app


from nl import models

