
import os


from flask import Flask


def create_app(test_config=None):
    """Create and initialize application."""

    # Construct framework
    app = Flask(__name__, instance_relative_config=True)

    # Hard-coded config settings TODO:
    app.config.from_mapping(
        SECRET_KEY='dev',
        SQLALCHEMY_DATABASE_URI='mysql+pymysql://newui:newuiNEWUI@gearhart.rnstech.com:3306/newui',
        SQLALCHEMY_TRACK_MODIFICATIONS=False
        )

    # Handle difference in startup when testing
    if test_config is None:
        app.config.from_pyfile('config.py', silent=True)
    else:
        app.config.from_mapping(test_config)

    # Make sure all directories are created (specifically, instance)
    try:
        os.makedirs(app.instance_path)
    except OSError:
        pass
    
    from nl3 import auth
    app.register_blueprint(auth.bp)

    from nl3 import admin
    app.register_blueprint(admin.bp)

    from nl3 import customers
    app.register_blueprint(customers.bp)
    
    from nl3 import routes
    app.register_blueprint(routes.bp)
    
    from nl3 import db
    db.init_app(app)
    with app.app_context():
        db.get_db().init_app(app)
    
    return app

